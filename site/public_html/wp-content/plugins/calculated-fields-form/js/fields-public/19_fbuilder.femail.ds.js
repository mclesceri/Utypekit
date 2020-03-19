	$.fbuilder.controls[ 'femailds' ]=function(){};
	$.extend(
		$.fbuilder.controls[ 'femailds' ].prototype,
		$.fbuilder.controls[ 'femail' ].prototype,
		$.fbuilder.controls[ 'datasource' ].prototype,
		{
			ftype:"femailds",
			show:function()
				{
					return $.fbuilder.controls[ 'femail' ].prototype.show.call( this );
				},
			after_show : function()
				{
					var me = this, first_time = true;
					$.fbuilder.controls[ 'datasource' ].prototype.getData.call( this, function( data )
						{ 
							var v = '';
							if( typeof data['error'] != 'undefined' )
							{
								alert( data.error );
							}
							else
							{
								if( data.data.length )
								{
									v = data.data[ 0 ][ 'value' ];
								}
							}	
							if( first_time )
							{
								first_time = false;
								if( typeof me.defaultSelection != 'undefined') v = me.defaultSelection;
							}	
							$( '#' + me.name ).val( v ).change();
						}
					);
				},
			setVal : function( v )
				{
					this.defaultSelection = v;
					$.fbuilder.controls[ 'femail' ].prototype.setVal.call( this, v );
				}	
	});