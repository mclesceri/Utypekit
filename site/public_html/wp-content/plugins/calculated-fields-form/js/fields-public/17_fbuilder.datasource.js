	$.fbuilder.controls[ 'datasource' ] = function(){};
	$.fbuilder.controls[ 'datasource' ].prototype = {
		isDataSource:true,
		active : '',
		list : {
			'database'	: { cffaction : 'get_data_from_database' },
			'posttype'  : { cffaction : 'get_posts' },
			'taxonomy'	: { cffaction : 'get_taxonomies' },
			'user' 		: { cffaction : 'get_users' },
			'recordset'	: {
				recordsetData: {
					recordset 	: '',
					value 		: '',
					text  		: '',
					where 		: ''
				},
				getData : function( callback, parentObj )
					{
						var obj = { data : [] },
							d  	= this.recordsetData, 
							fi 	= parentObj[ 'form_identifier' ],
							rs 	= $.trim(d.recordset),
							r, // For records
							w 	= $.trim( d.where ), 
							t 	= $.trim( d.text  ), 
							v 	= $.trim( d.value ),
							tmp;
							
						if( rs != '' )
						{
							
							r = $.fbuilder[ 'forms' ][ fi ].getItem( rs+fi ).val();
							if( w != '' ) w = parentObj.parseVars(w);
							parentObj.hasBeenPutRelationHandles = false; // To be sure the events are triggered
							parentObj.replaceVariables( [rs], {} )
			
							for( var i in r )
							{
								if( w == '' || (function(o,w){
									w = w.replace(/\brecord\s*\[/gi, 'o[');
									return eval(w);
								})(r[i],w))
								{
									tmp = {};
									if(t != '' && typeof r[i][t] != 'undefined' ) tmp['text' ] = r[i][t];
									if(v != '' && typeof r[i][v] != 'undefined' ) tmp['value'] = r[i][v];
									obj.data.push( tmp );
								}
							}
							callback( obj );	
						}	
					}
			},
			'csv' 			: {
				cffaction : 'get_csv_rows',
				csvData : {
					text   	: 0,
					value  	: 0,
					fields 	: [],
					rows 	: [],
					where	: ''
				},
				getData : function( callback, parentObj )
					{
						var isRS= parentObj.ftype == 'frecordsetds',// is recordset
							obj = { data : [] },
							d   = this.csvData, 
							w   = $.trim( d.where ),
							v, t, r;
						
						if( w != '' ) w = parentObj.parseVars(w);
						for( var i in d.rows )
						{
							v = d.value;
							t = ( typeof d.text  == 'object' ) ? d.text : [d.text];
							if( !$.isArray( d.rows[ i ] ) )
							{
								for( var j = 0, h = t.length; j < h; j++ )
									t[ j ] = d.fields[j];
								
								v = d.fields[ v ];
							}
							
							if( w == '' || w == d.rows[ i ][ v ] )
							{
								r = {};
								if( !isRS ) r['value'] = d.rows[i][v];
								for( var j = 0, h = t.length; j < h; j++ )
									r[ (isRS) ? t[j] : 'text' ] = d.rows[i][t[j]];
								obj.data.push( r );
							}	
						}	
						callback( obj );
					}
			}
		},
		getData : function( callback )
			{
				var me 	= this,
					obj = me.list[ me.active ];
		
				if( me.active == 'csv' && typeof obj.csvData[ 'rows' ] != 'undefined' && obj.csvData[ 'rows' ].length )
				{	
					if( typeof obj[ 'getData' ] != 'undefined' ) obj.getData( callback, me );
					if( $( '[id="'+me.name+'"]' ).closest( '.pbreak:hidden' ).length ) $( '[id="'+me.name+'"]' ).addClass( 'ignorepb' );
				}
				else if( me.active == 'recordset')
				{
					obj.getData(callback, me);
				}	
				else
				{
					var url = document.location.href,
						data = {
							cffaction : obj.cffaction,
							form 	  : obj.form,
							field	  : me.name.replace( me.form_identifier, '' ),
							vars	  : {} 
						};
						
					if( typeof obj.vars != 'undefined' )
					{
						if ( !me.replaceVariables( obj.vars, data[ 'vars' ] ) ) return;
					}

					if( typeof me.ajaxConnect != 'undefined' ) me.ajaxConnect.abort();
					me.ajaxConnect = $.ajax(
						{
							dataType : 'json',
							url : url,
							cache : false,
							data : data,
							success : (function( me ){
								return function( data ){
									callback( data );
									if( $( '[id="'+me.name+'"]' ).closest( '.pbreak:hidden' ).length )
									{
										$( '[id="'+me.name+'"]' ).addClass( 'ignorepb' );
									}	
								};
							})(me)
						}
					);
				}	
			},
		parseVars : function( p )
			{
				var o = {};
				p = p.replace( /^\s*/, '' ).replace( /\s*$/, '' );
				if( p != '' )
				{
					if( ( v = p.match( /<\s{0}%[^%]*%\s{0}>/ ) ) != null )
					{
						v = v.map(function(x){return x.replace(/(<\s{0}%|%\s{0}>)/g, '');});
						this.replaceVariables( v, o );
						for( var i in v )
						{
							var index = encodeURI( v[ i ] );
							if( typeof o[ index ] != 'undefined' )
							{
								p = p.replace( new RegExp('<\s{0}%'+v[ i ].replace(/[\-\[\]\{\}\(\)\*\+\?\.\,\\\^\$\|\#\s]/g, "\\$&")+'%\s{0}>', 'g'),  o[ index ] );
							}	
						}	
					}
				}
				return p;
			},	
		replaceVariables : function( vars, _rtn )
			{
				var	me = this,
					field,
					formId = form_identifier = me.form_identifier,
					id,
					isValid = true,
					tmpArr = [], // To avoid duplicate handles
					val = '';

				for( var i = 0, h = vars.length; i < h; i++ )
				{
					id 		= vars[ i ]+formId;
					field 	= $.fbuilder[ 'forms' ][ formId ].getItem( id );
					
					if( typeof field != 'undefined' && field != false )
					{
						val = field.val();
						if( $( '#'+id ).val() == '' ) isValid = false;
						if( ( typeof me.hasBeenPutRelationHandles == 'undefined' || !me.hasBeenPutRelationHandles ) && $.inArray( id, tmpArr ) == -1 )
						{	
							$( document ).on( 'change', '#'+id, function(){ me.after_show(); } );
						}	
					}
					else
					{
						try{
							if( typeof window[ vars[ i ] ] != 'undefined' ) val = window[ vars[ i ] ];
							else val = eval( vars[ i ] );
						}catch( err ){
							val = '';
						}
					}
					_rtn[ encodeURI( vars[ i ] ) ] = (val+'').replace( /^['"]+/, '' ).replace( /['"]+$/, '');
				}	
				me.hasBeenPutRelationHandles = true;
				return isValid;
			},
		setDefault : function()
			{
				var d = this.parseVars($.trim(this.defaultSelection)),
					l,e,t,
					n = this.name;
					
				if( !/^\s*$/.test(d))
				{
					l = $.fbuilder.htmlEncode(d).split('|');
					for(var i in l)
					{
						t = $.trim(l[i]);
						if(!/^\s*$/.test(t))
						{
							e = $('[name*="'+n+'"][value="'+t+'"],[name*="'+n+'"][vt="'+t+'"]');
							if( e.length ) e.prop('checked', true);
							else
							{
								e = $('[name*="'+n+'"]').find( 'option[value="'+t+'"],option[vt="'+t+'"]');
								if(e.length) e.prop( 'selected', true );
							}	
						}	
					}	
				}	
			}
	};