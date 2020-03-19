/*
* distance.js v0.2
* By: CALCULATED FIELD PROGRAMMERS
* The script allows operations with distance
* Copyright 2015 CODEPEOPLE
* You may use this project under MIT or GPL licenses.
*/

;(function(root){
	var lib = {},
		loadingFlag = false,
		defaultFormId 	  = 'cp_calculatedfieldsf_pform_1',
		defaultUnitSystem = 'km',
		defaultTravelMode = 'driving',
		defaultAvoidHighways = false,
		defaultAvoidTolls 	 = false,
		distanceArr = [],
		travelTimeArr= [],
		callbacks   = []; 
	

	/*** PRIVATE FUNCTIONS ***/
	
	/*
	* Runs all callbacks after loading the Google API
	*/
	function _runCallbacks()
	{
		var h = callbacks.length;
		if( h )
		{
			for( var i = 0; i < h; i++ )
			{
				callbacks[i]();
			}	
		}	
		callbacks = [];
	};
	
	/*
	* Inserts the SCRIPT tag for loading the Google API
	*/
	function _createScriptTags()
	{
		// If Google Maps has not been loaded, and has not been created the script tags for loading the API
		if( !loadingFlag )
		{	
			loadingFlag = true;
			var script=document.createElement('script');
			script.type  = "text/javascript";
			script.src = '//maps.google.com/maps/api/js?'+( ( typeof google_api_key != 'undefined' ) ? 'key='+google_api_key+'&' : '' )+'callback=CPCFF_DISTANCE_MODULE_RUNCALLBACKS';
			document.body.appendChild(script);
		}					
	};
	
	/*
	* Check the default value and the attribute and returns the correct value.
	*/
	function _getValue( attr, val )
	{
		if( 
			typeof google != 'undefined' &&
			typeof google[ 'maps' ] != 'undefined' 
		)
		{
			val = String(val).toUpperCase();
		
			switch( attr )
			{
				case 'unitSystem':
					val = ( val == 'MI' ) ? google.maps.UnitSystem.IMPERIAL : google.maps.UnitSystem.METRIC;
				break;
				case 'travelMode':
					switch( val )
					{
						case 'BICYCLING': val = google.maps.TravelMode.BICYCLING; break;
						case 'TRANSIT'  : val = google.maps.TravelMode.TRANSIT; break;
						case 'WALKING'  : val = google.maps.TravelMode.WALKING; break;
						default  		: val = google.maps.TravelMode.DRIVING; break;
					}
				break;
			}
		}
		
		return val;
	};
	
	/*
	* Evaluate all equations in the form
	*/
	function _reCalculate( form_id )
	{
		fbuilderjQuery.fbuilder.calculator.defaultCalc( '#'+form_id, false );
	};
	
	/*** PUBLIC FUNCTIONS ***/
	lib.cf_distance_version = '0.2';
	
	/*
	* DISTANCE( address_a_string, address_b_string, unit_system, travel_mode, form_id ) 	
	*
	* unit_system: 
	* km  - Kilometters
	* mi  - Miles
	*
	* travel_mode:
	* DRIVING - Indicates standard driving directions using the road network
	* BICYCLING - Requests bicycling directions via bicycle paths & preferred streets
	* TRANSIT - Requests directions via public transit routes
	* WALKING - Requests walking directions via pedestrian paths & sidewalks
	*
	* form_id is passed from the _calculate function in the fbuilder.fcalculated.js file, and should not be passed from the equation's edition
	*
	* the function returns the distance between address_a and address_b, in the unit_system
	*/
	lib.DISTANCE = function( address_a, address_b, unit_system, travel_mode, form_id ){

		if( typeof address_a != 'undefined' && typeof address_b != 'undefined' )
		{
			address_a = (new String(address_a)).replace( /^\s+/, '' ).replace( /\s+$/, '' );
			address_b = (new String(address_b)).replace( /^\s+/, '' ).replace( /\s+$/, '' );
			if( address_a.length > 2 && address_b.length > 2 )
			{
				if( typeof unit_system == 'undefined' ) unit_system = defaultUnitSystem;
				if( typeof travel_mode == 'undefined' ) travel_mode = defaultTravelMode;
				form_id	    = ( typeof form_id != 'undefined' ) ? form_id : ( ( typeof fbuilderjQuery.fbuilder.calculator.form_id != 'undefined' ) ? fbuilderjQuery.fbuilder.calculator.form_id : defaultFormId );

				// The pair of address was processed previously
				for( var i in distanceArr )
				{
					if( distanceArr[ i ][ 'a' ] == address_a && distanceArr[ i ][ 'b' ] == address_b ) return distanceArr[ i ][ 'distance' ];
				}
				
				// Google Maps has not been included previously
				if( typeof google == 'undefined' || google['maps'] == null )
				{	
					// List of functions to be called after complete the Google Maps loading
					callbacks.push( 
						( 
							function( address_a, address_b, unit_system, travel_mode, form_id )
							{ 
								return function(){ DISTANCE( address_a, address_b, unit_system, travel_mode, form_id ) };
							}	
						)( address_a, address_b, unit_system, travel_mode, form_id ) 
					);
					_createScriptTags();
					return;	
				}	

				var service = new google.maps.DistanceMatrixService(),
					request = {
						origins		: [ address_a ],
						destinations: [ address_b ],
						travelMode	: _getValue( 'travelMode',  travel_mode ),
						unitSystem	: _getValue( 'unitSystem',  unit_system )
					};

				service.getDistanceMatrix(
					request, 
					( 
						function( form_id, request )
						{
							return function (response, status) 
									{
										var r;
										if (status == google.maps.DistanceMatrixStatus.OK) 
										{	
											try{
												r = response.rows[ 0 ].elements[ 0 ].distance[ 'text' ];
												r = r.replace(/\,/g, '.')
												     .replace(/[^\.\d]/g, '')
													 .replace(/\.(\d{3})/g, '$1');	 
											}catch( err ){ r = 'FAIL'; }	
										}
										else r = 'FAIL';
										distanceArr.push({'a':request.origins[ 0 ], 'b':request.destinations[ 0 ], 'distance':r});
										_reCalculate( form_id );
									};
						} 
					)( form_id, request )
				);
			}	
		}	
		return 0;
	};
	
	/*
	* TRAVELTIME( address_a_string, address_b_string, as_text, travel_mode, avoid_highways, avoid_tolls ) 	
	*
	* as_text: 
	* true or 1  - Returns a textual representation of travel time
	* false or 0 - Returns the travel time in seconds
	*
	* travel_mode:
	* DRIVING - Indicates standard driving directions using the road network
	* BICYCLING - Requests bicycling directions via bicycle paths & preferred streets
	* TRANSIT - Requests directions via public transit routes
	* WALKING - Requests walking directions via pedestrian paths & sidewalks
	*
	* avoid_highways: true, false
	* avoid_tolls: true, false
	*
	* form_id is passed from the _calculate function in the fbuilder.fcalculated.js file, and should not be passed from
	* the equation's edition
	*
	* the function returns the time between address_a and address_b
	*/
	lib.TRAVELTIME = function( address_a, address_b, as_text, travel_mode, avoid_highways, avoid_tolls, form_id ){
	
		if( typeof address_a != 'undefined' && typeof address_b != 'undefined' )
		{
			address_a = (new String(address_a)).replace( /^\s+/, '' ).replace( /\s+$/, '' );
			address_b = (new String(address_b)).replace( /^\s+/, '' ).replace( /\s+$/, '' );
			if( address_a.length > 2 && address_b.length > 2 )
			{
				if( typeof as_text == 'undefined' ) 	 as_text = false;
				if( typeof travel_mode == 'undefined' )  travel_mode = defaultTravelMode;
				if( typeof avoid_highways != 'boolean' ) avoid_highways = defaultAvoidHighways;
				if( typeof avoid_tolls != 'boolean' ) 	 avoid_tolls = defaultAvoidTolls;
				
				form_id	    = ( typeof form_id != 'undefined' ) ? form_id : ( ( typeof fbuilderjQuery.fbuilder.calculator.form_id != 'undefined' ) ? fbuilderjQuery.fbuilder.calculator.form_id : defaultFormId );

				// The pair of address was processed previously
				for( var i in travelTimeArr )
				{
					if( travelTimeArr[ i ][ 'a' ] == address_a && travelTimeArr[ i ][ 'b' ] == address_b ) return travelTimeArr[ i ][ 'time' ];
				}
				
				// Google Maps has not been included previously
				if( typeof google == 'undefined' || google['maps'] == null )
				{	
					// List of functions to be called after complete the Google Maps loading
					callbacks.push( 
						( 
							function( address_a, address_b, as_text, travel_mode, avoid_highways, avoid_tolls, form_id )
							{ 
								return function(){ TRAVELTIME( address_a, address_b, as_text, travel_mode, avoid_highways, avoid_tolls, form_id ) };
							}	
						)( address_a, address_b, as_text, travel_mode, avoid_highways, avoid_tolls, form_id ) 
					);
					_createScriptTags();
					return;	
				}	
				
				var service = new google.maps.DistanceMatrixService(),
					request = {
						origins		: [ address_a ],
						destinations: [ address_b ],
						travelMode	: _getValue( 'travelMode',  travel_mode ),
						avoidHighways : avoid_highways,
						avoidTolls  : avoid_tolls
					};

				service.getDistanceMatrix(
					request, 
					( 
						function( form_id, as_text, request )
						{
							return function (response, status) 
									{
										var r;
										if (status == google.maps.DistanceMatrixStatus.OK) 
										{
											try{
												r = response.rows[ 0 ].elements[ 0 ].duration[ ( as_text ) ? 'text' : 'value' ];
											}catch( err ){ r = 'FAIL'; }	
										}
										else r = 'FAIL';
										travelTimeArr.push({'a':request.origins[ 0 ], 'b':request.destinations[ 0 ], 'time':r});
										_reCalculate( form_id );
									};
						} 
					)( form_id, as_text, request )
				);
			}	
		}	
		return 0;
	};
	
	
	lib.CPCFF_DISTANCE_MODULE_RUNCALLBACKS = function(){ _runCallbacks(); };
	
	root.CF_DISTANCE = lib;
	
})(this);