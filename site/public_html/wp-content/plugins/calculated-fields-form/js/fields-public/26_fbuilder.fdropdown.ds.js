	$.fbuilder.controls[ 'fdropdownds' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fdropdownds' ].prototype,
		$.fbuilder.controls[ 'fdropdown' ].prototype,
		$.fbuilder.controls[ 'datasource' ].prototype,
		{
			ftype:"fdropdownds",
			defaultSelection:"",
			show:function()
				{
					this.choices = [];
					this.choicesVal = [];
					return $.fbuilder.controls[ 'fdropdown' ].prototype.show.call( this );
				},
			after_show : function()
				{
					var me = this, first_time = true;
					$.fbuilder.controls[ 'datasource' ].prototype.getData.call( this, function( data )
						{
							var str = '',
								e 	= $( '#' + me.name );
							if( typeof data['error'] != 'undefined' )
							{
								alert( data.error );
							}
							else
							{
								var t, v;
								for( var i = 0, h = data.data.length; i < h; i++ )
								{
									v = ( ( typeof data.data[ i ][ 'value' ] != 'undefined' ) ? data.data[ i ][ 'value' ] : '' );
									t = ( ( typeof data.data[ i ][ 'text' ] != 'undefined' )  ? data.data[ i ][ 'text' ]  :  v );

									str += '<option value="' + $.fbuilder.htmlEncode( v ) + '" vt="' + $.fbuilder.htmlEncode((me.toSubmit == 'text') ? t : v) +'">' + t + '</option>';
								}
							}
							e.html( str );
							if( first_time )
							{
								first_time = false;
								$.fbuilder.controls[ 'datasource' ].prototype.setDefault.call( me );
							}
							e.change();
						}
					);
				},
			setVal : function( v )
				{
					this.defaultSelection = v;
					$.fbuilder.controls[ 'fdropdown' ].prototype.setVal.call( this, v );
				}
	});