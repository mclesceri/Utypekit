	$.fbuilder.controls[ 'fCalculated' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fCalculated' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Untitled",
			ftype:"fCalculated",
			predefined:"",
			required:false,
			size:"medium",
			eq:"",
			suffix:"",
			prefix:"",
			decimalsymbol:".",
			groupingsymbol:"",
			dependencies:[ {'rule' : '', 'complex' : false, 'fields' : [ '' ] } ],
			readonly:true,
			hidefield:false,
			show:function()
				{
					return '<div class="fields '+this.csslayout+' cff-calculated-field" id="field'+this.form_identifier+'-'+this.index+'" style="'+((this.hidefield)? 'padding:0;margin:0;border:0;opacity:0;width:0;height:0;overflow:hidden;' : '' )+'"><label>'+this.title+''+( ( this.required ) ? '<span class="r">*</span>' : '' )+'</label><div class="dfield"><input id="'+this.name+'" name="'+this.name+'" '+((this.readonly) ? ' readonly ' : '')+' class="codepeoplecalculatedfield field '+this.size+((this.required)?" required":"")+'" type="'+( ( this.hidefield ) ? 'hidden' : 'text' )+'" value="'+this.predefined+'"/>'+( ( !this.hidefield ) ? '<span class="uh">'+this.userhelp+'</span>' : '' )+'</div><div class="clearer"></div></div>';
				},
            after_show:function()
				{
	                // Add equations
					var me = this,
						configuration = { "suffix" : me.suffix, "prefix" : me.prefix, "groupingsymbol" : me.groupingsymbol, "decimalsymbol" : me.decimalsymbol },
						dependencies = [];

					$.each( me.dependencies, function( i, d )
						{
							d.rule = d.rule.replace( /^\s+/, '').replace( /\s+$/, '');
							if( d.rule != '' && d.fields.length ){

								var fields = [];
								$.each( d.fields, function( j, f ){
									if( f != '' )
									{
										fields.push( f );
									}
								});

								if( fields.length ){
									dependencies.push( { 'rule' : d.rule, 'fields' : fields } );
								}
							}
						});

					me.dependencies = dependencies;
					var eq = me.eq;
					eq = eq.replace(/\n/g, ' ').replace(/fieldname(\d+)/g, "fieldname$1"+me.form_identifier).replace( /form_identifier/g, '\''+this[ 'form_identifier' ]+'\'').replace( /;\s*\)/g, ')').replace(/;\s*$/, '');

					if( !/^\s*$/.test(eq) )
                    {
                        $.fbuilder.calculator.addEquation( me.name, eq, configuration, dependencies, me.form_identifier );
                    }

                    // Events
			        var e = $( '[id="'+me.name+'"]' );
                    e.bind(
                        'calcualtedfield_changed',
                        {obj: me},
                        function( evt ){
							if( $.fbuilder[ 'calculator' ].getDepList( evt.data.obj.name, evt.data.obj.val(), evt.data.obj.dependencies ) )
                            {
								$.fbuilder.showHideDep(
                                                {
                                                    'formIdentifier' : evt.data.obj.form_identifier,
                                                    'fieldIdentifier': evt.data.obj.name,
                                                    'throwEvent' 	 : false
                                                }
                                            );
                            }
                        }
                    );
				},
			showHideDep: function( toShow, toHide, hiddenByContainer )
				{
					var me   	= this,
						item 	= $( '#'+me.name ),
						identifier = me.form_identifier,
						isHidden= ( typeof toHide[ me.name ] != 'undefined' || typeof hiddenByContainer[ me.name ] != 'undefined' ),
						d, dep,
						clearRef = function(id){
							if( typeof toShow[ id ] != 'undefined' )
							{
								delete toShow[ id ][ 'ref' ][ me.name ];
								if( $.isEmptyObject(toShow[ id ][ 'ref' ]) )
								delete toShow[ id ];
							}
						},
						hideField = function(id){
							$( '#'+id ).closest( '.fields' ).hide();
							$( '[id*="'+id+'"]:not(.ignore)' ).addClass( 'ignore' );
							toHide[ id ] = {};
						},
						result 	= [];

					try
					{
						d = item.attr( 'dep' );
						if( typeof d != 'undefined' && !/^\s*$/.test( d ) )
						{
							d = d.split( ',' );
							for ( i=0; i<d.length; i++ )
							{
								dep = d[i]+identifier;
								if( isHidden ) clearRef(dep);
								if( typeof toShow[ dep ] == 'undefined' )
								{
									if ( !isHidden )
									{
										$( '#' + dep ).closest( '.fields' ).show();
										$( '[id*="'+dep+'"].ignore' ).removeClass( 'ignore' );
										toShow[ dep ] = { 'ref': {}};
										toShow[ dep ][ 'ref' ][ me.name ]  = 1;
										if( typeof toHide[ dep ] != 'undefined' )
										{
											result.push( dep );
											delete toHide[ dep ];
										}
									}
									else if( typeof toHide[ dep ] == 'undefined')
									{
										hideField(dep);
										result.push( dep );
									}
								}
							}
						}
						d = item.attr( 'notdep' );
						if( typeof d != 'undefined' && !/^\s*$/.test( d ) )
						{
							d = d.split( ',' );
							for ( i=0; i<d.length; i++ )
							{
								dep = d[i]+identifier;
								clearRef(dep);

								if (
									typeof toShow[ dep ] == 'undefined' &&
									typeof toHide[ dep ] == 'undefined'
								)
								{
									hideField(dep);
									result.push( dep );
								}
							}
						}
					}
					catch(e){}
					return result;
				},
			val : function()
				{
					var e = $( '[id="' + this.name + '"]:not(.ignore)' );
					if( e.length )
					{
						var v = $.trim( e.val() );

						v = v.replace( new RegExp( $.fbuilder[ 'escape_symbol' ](this.prefix), 'g' ), '' )
						     .replace( new RegExp( $.fbuilder[ 'escape_symbol' ](this.suffix), 'g' ), '' );

						return $.fbuilder.parseVal( v, this.groupingsymbol, this.decimalsymbol );
					}
					return 0;
				}
		}
	);

	/*
	* Extend the window object with the methods of obj, the prefix is used to avoid redefine window methods
	*/
	$.fbuilder[ 'extend_window' ]  = function( prefix, obj)
		{
			for( method in obj )
			{
				window[ prefix+method ] = (function( m )
					{
						return function()
							{
								return m.obj[ m.method_name ].apply( m.obj, arguments );
							};
					})({ "method_name" : method, 'obj' : obj });
			}
		};

	// Calculate Field code
	$.fbuilder[ 'calculator' ] = (function()
		{
				// Used to validate the equations results
				var validators = [];

                // Loading available modules
				if( typeof $.fbuilder[ 'modules' ] != 'undefined' )
				{
					var modules = $.fbuilder[ 'modules' ];
					for( var module in modules )
					{
						if( typeof modules[ module ][ 'callback' ] != 'undefined' )
						{
							modules[ module ][ 'callback' ]();
						}

						if( typeof modules[ module ][ 'validator' ] != 'undefined' )
						{
							validators.push( modules[ module ][ 'validator' ] );
						}
					}
				}

				// Private function to validate the equation results
				_validate_result = function( v )
					{
						if( validators.length )
						{
							var h = validators.length;
							while( h-- )
							{
								if( validators[ h ]( v ) )
								{
									return true;
								}
							}
						}
						else
						{
							return true;
						}

						return false;
					};

				// Private function, the variable names in the equations are replaced by its values, return the equation result or false if error
				_calculate = function( eq, suffix, __ME__ )
					{
						var _match,
							field_regexp = new RegExp( '(fieldname\\d+'+suffix+')([\\D\\b])'),
							__ME__ = (__ME__) ? $.fbuilder['forms'][suffix].getItem( __ME__ ).val() : '';

						eq = '(' + eq + ')';

						while ( _match = field_regexp.exec( eq ) )
						{
							var field = $.fbuilder[ 'forms' ][ suffix ].getItem( _match[1] ),
								v = '';
							if( field )
							{
								v = field.val();
								if( $.isNumeric( v ) ) v = '('+v+')';
								else if( typeof v == 'object' && typeof window.JSON != 'undefined' ) v = JSON.stringify( v );
							}
							eq = eq.replace( _match[0], v+''+_match[2] ); // Replace the variable name by value
						}
						try
						{
							var r = eval( eq.replace( /^\(/, '' ).replace( /\)$/, '' ).replace( /\b__ME__\b/g, __ME__ ) ); // Evaluate the final equation
							return ( typeof r != 'undefined' && _validate_result( r ) ) ? r : false;
						}
						catch(e)
						{
							return false;
						}
					};

				_checkValueThrowingEquation = function( t )
					{
						if( typeof t.attr( 'data-timeout' ) != 'undefined' ) clearTimeout( t.attr( 'data-timeout' ) );
						if( typeof t.attr( 'data-previousvalue' ) == 'undefined' ) t.attr( 'data-previousvalue', t.val() );
						else
						{
							if( t.val() == t.attr( 'data-previousvalue' ) )
							{
								t.removeAttr( 'data-timeout' );
								obj.Calculate( t[0] );
								return;
							}
							t.attr( 'data-previousvalue',  t.val() );
						}
						t.attr( 'data-timeout',  setTimeout( _checkValueThrowingEquation, 500, t ) );
					};

				// The public object
                var CalcFieldClss = function(){};
				CalcFieldClss.prototype = {
					processing_queue : false, // Flag indicating the queued equations are being processed
					// object where attributes names are the forms identifiers, and their values the queue of equations
					queued_equations : {},
					addEquation : function( calculated_field, equation, configuration, dependencies, form_identifier )
						{
							var equation_result = $('[id="'+calculated_field+'"]');
							if(equation_result.length)
							{
								var form = equation_result[0].form,
									equationObj, field,
									regexp = new RegExp( '(fieldname\\d+)_' ),
									match;

								if( typeof form.equations == 'undefined' ) form['equations'] = [];
								var  i, j=-1, h = form.equations.length;

								// Avoid insert the equation multiple times to the form
								for( i = 0 ; i < h; i++ )
								{
									if( form.equations[ i ].result == calculated_field ) break;
									if( form.equations[ i ].equation.match( calculated_field ) ){ j = i; break; }
								}

								// The equation hasn't been inserted previously
								if( i == h || j != -1)
								{
									equationObj = {'result':calculated_field, 'equation':equation, 'conf':configuration, 'dep':dependencies, 'identifier':form_identifier};
									form.equations.splice(i, 0, equationObj);
									while ( match = regexp.exec( equation ) )
									{
										field = $.fbuilder[ 'forms' ][ form_identifier ].getItem( match[1]+form_identifier );
										if( field )
										{
											if( typeof field.usedInEquations == 'undefined' ) field.usedInEquations = [];
											field.usedInEquations.push( equationObj );
										}
										equation = equation.replace( new RegExp( match[0], 'g' ), '' );
									}
								}
							}

						},
					enqueueEquation : function( form_identifier, equations )
						{
							if( typeof this.queued_equations[ form_identifier ] == 'undefined' )
								this.queued_equations[ form_identifier ] = [];
							var queue = this.queued_equations[ form_identifier ], f;

							for( var i = 0, h = equations.length; i < h; i++ )
							{
								f = -1;
								for( var j = 0, k = queue.length; j < k; j++ )
								{
									if( queue[ j ].result == equations[ i ].result ) break;
									if( queue[ j ].equation.match( equations[ i ].result ) ){ f = j; break; }
								}
								if( j == k || f != -1)
								{
									queue.splice(j, 0, equations[ i ]);
								}
							}
						},
					getDepList : function( calculated_field, value, dependencies ) // Get the list of dependent fields
						{
							var list    = [], // Fields that comply the rules
								list_h  = []; // Fields that don't comply the rules

							// The value is correct and the field has dependencies
							if( value !== false && dependencies.length )
							{
								for( var i = 0, h = dependencies.length; i < h; i++ )
								{
									try
									{
										// Get the rule and evaluate
										var rule = eval(dependencies[i].rule.replace( /value/gi, value ));
										$.each( dependencies[i].fields, function( j, e )
											{
												if( e != '' )
												{
													if( rule )
													{
														var k = $.inArray(e, list_h);
														if( k != -1) list_h.splice( k, 1 );
														if( $.inArray(e, list) == -1) list.push( e );
													}
													else
													{
														if( $.inArray(e, list) == -1) list_h.push( e );
													}
												}
											});

									}
									catch(e)
									{
										continue;
									}
								}
							}

							$('[id="'+calculated_field+'"]').attr( 'dep', list.join(',') ).attr('notdep', list_h.join( ',' ) );
							return list.length || list_h.length;
						},

                    defaultCalc : function( form_identifier, recalculate ) // Evaluate all equations in form
						{
							var form = $( form_identifier ),
								fSec  = form_identifier.match( /_\d+$/ )[0],
								dep  = false;

							// The form exists and has equations
							if( form.length )
							{
								if( typeof form[0].equations != 'undefined' )
								{
									this.queued_equations[ fSec ] = form[0].equations.slice( 0 );
									this.processQueue( fSec );
								}
								$( form ).trigger( 'cpcff_default_calc' );
							}
						},

                    Calculate : function ( field )
						{
							if( field.id == undefined ) return;
							var id 	 = field.id,
								fSec  = id.match( /(_\d+)?_\d+$/ ),
								item,
								me   = this;

							if( fSec )
							{
								fSec = ( typeof fSec[1] != 'undefined' ) ? fSec[1] : fSec[0];
								item = $.fbuilder[ 'forms' ][ fSec ].getItem( id );
								if( item && typeof item['usedInEquations'] != 'undefined' )
								{
									me.enqueueEquation( fSec, item.usedInEquations );
									me.processQueue( fSec );
								}
							}
						},
					processQueue : function( fSec )
						{
							if( this.processing_queue ) return;
							this.processing_queue = true;

							if( typeof this.queued_equations[ fSec ] != 'undefined' )
							{
								var queue = this.queued_equations[ fSec ], eq_obj;
								while( queue.length )
								{
									eq_obj = queue.shift();
									var field = $( '[id="' + eq_obj.result+'"]' ),
										result = _calculate( eq_obj.equation,  eq_obj.identifier,  eq_obj.result),
										bk =  field.data('bk');

									field.val( ( result !== false ) ? this.format( result, eq_obj.conf) : '' );
									if(bk != field.val())
									{
										field.trigger( 'calcualtedfield_changed' );
										field.change();
									}
									field.data('bk',field.val());
								}
							}

							this.processing_queue = false;
						},
                    format : function( value,  config )
						{
                            if( !/^\s*$/.test( value ) )
                            {
                                if( $.isNumeric( value )  && !/[+\-]?(?:0|[1-9]\d*)(?:\.\d*)?(?:[eE][+\-]?\d+)/.test( value ) )
                                {

                                    var symbol = ( value < 0 ) ? '-' : '',
                                        parts = value.toString().replace( "-", "" ).split("."),
                                        counter = 0,
                                        str = '';

                                    if(config.groupingsymbol)
                                    {
                                        for( var i = parts[0].length-1; i >= 0; i--){
                                            counter++;
                                            str = parts[0][i] + str;
                                            if( counter%3 == 0 && i != 0 ) str = config.groupingsymbol + str;

                                        }
                                        parts[0] = str;
                                    }

                                    value = symbol+parts.join( config.decimalsymbol );
                                }

                                if( config.prefix )
                                {
                                    value = config.prefix + value;
                                }
                                if( config.suffix )
                                {
                                    value += config.suffix;
                                }
                            }
							return value;
						},

                    unformat : function( field )
						{

							var escape_symbol = $.fbuilder.escape_symbol;

							var eq = field[0].form.equations,
								v = field.val();

							for(var i = 0, h = eq.length; i < h; i++)
							{
								if(eq[i].result == field[0].id)
								{
									var c = eq[i].conf; // Configuration object

									if( c.prefix && !/^\s*$/.test( c.prefix ) )
									{
										v = v.replace( new RegExp( "^" + escape_symbol( c.prefix ) ), '' );
									}

									if( c.suffix && !/^\s*$/.test( c.suffix ) )
									{
										v = v.replace( new RegExp( escape_symbol( c.suffix ) + "$" ), '' );
									}

									if( !/[+\-]?(?:0|[1-9]\d*)(?:\.\d*)?(?:[eE][+\-]?\d+)/.test( v ) )
									{
										if( c.groupingsymbol && !/^\s*$/.test( c.groupingsymbol ) )
										{
											v = v.replace( new RegExp( escape_symbol( c.groupingsymbol ), 'g' ), '' );
										}

										if( c.decimalsymbol && !/^\s*$/.test( c.decimalsymbol ) )
										{
											v = v.replace( new RegExp( escape_symbol( c.decimalsymbol ), 'g' ), '.' );
										}
									}
								}
							}
							return v;
						}
				};

				var obj = new CalcFieldClss();

				// Associate events to the document for throw the corresponding equations
                $( document ).bind('keyup change blur', function(evt)
					{
                        // If evalequations = 0 the equations shouldn't be evaluated dynamically
                        var evalequations = $( evt.target ).closest( 'form' ).attr( 'data-evalequations' ),
							t = $( evt.target );

                        if(
							typeof evalequations != 'undefined' &&
							evalequations*1 == 0 &&
							!( t.hasClass( 'codepeoplecalculatedfield' ) && evt.type == 'change' )
						)
                        {
                            return;
                        }

                        if( evt.type == 'keyup' )
						{
							// The key out of range
							if(evt.keyCode && (evt.keyCode >= 33 && evt.keyCode <= 40)) return;
							_checkValueThrowingEquation( t );
						}
						else
						{
							if( t.hasClass( 'depItem' ) || ( t.prop( 'tagName' ) == 'INPUT' && t.attr( 'type' ).toLowerCase() == 'text' && evt.type != 'change' ) )
							{
								return;
							}
							obj.Calculate( t[0] );
						}
					});

				//Associate an event to the document waiting for the showHideDepEvent and recalculate all equations
				$(document).bind( 'showHideDepEvent', function( evt, form_identifier )
					{
                        // If evalequations = 0 the equations shouldn't be evaluated dynamically
                        var fId = '#'+form_identifier, evalequations = $( fId ).attr( 'data-evalequations' );
                        if( typeof evalequations == 'undefined' || evalequations*1 == 1 ) obj.defaultCalc( fId );
                    });
                return obj; // Return the public object
            }
        )();