	$.fbuilder.controls[ 'fPhoneds' ]=function(){};
	$.extend( 
		$.fbuilder.controls[ 'fPhoneds' ].prototype,
		$.fbuilder.controls[ 'fPhone' ].prototype,
		$.fbuilder.controls[ 'datasource' ].prototype,
		{
			ftype:"fPhoneds",
			show:function()
				{
					return $.fbuilder.controls[ 'fPhone' ].prototype.show.call( this );
				},
			after_show : function()
				{
					var me = this, first_time = true;
                    $.fbuilder.controls[ 'fPhone' ].prototype.after_show.call( me );
					$.fbuilder.controls[ 'datasource' ].prototype.getData.call( me, function( data )
						{ 
							var p = $.trim( me.dformat.replace(/[^\s#]/g, '' ).replace( /\s+/g, ' ' ) ).split( ' ' ), 
								h = p.length, e = [], v = '', r = '', vArr;
							
							if( typeof data['error'] != 'undefined' )
							{
								alert( data.error );
							}
							else
							{
								if( data.data.length )
									v = data.data[ 0 ].value;
							}
							
							if( first_time )
							{
								first_time = false;
								if( typeof me.defaultSelection != 'undefined') v = me.defaultSelection;
							}
							
							v = v.replace( /\s+/, '' );
							for( var i = 0; i<h; i++ ){ r += '(.{' + p[ i ].length + '})'; }
							
							if( r != '')
							{
								vArr = ( new RegExp( r ) ).exec( v );
								if( vArr ){ e = vArr.slice(1); }
							}	
							
							for( var i = 0; i < h; i++ )
							{
								$( '#' + me.name + '_' + i ).val( ( typeof e[ i ]  != 'undefined' ) ? e[ i ] : '' ).change();
							}
							
						}
					);
				},
			setVal : function( v )
				{
					this.defaultSelection = v;
					$.fbuilder.controls[ 'fPhone' ].prototype.setVal.call( this, v );
				}
		}
	);