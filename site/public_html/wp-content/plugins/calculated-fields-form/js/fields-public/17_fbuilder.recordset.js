	$.fbuilder.controls[ 'frecordsetds' ]=function(){};
	$.extend(
		$.fbuilder.controls[ 'frecordsetds' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		$.fbuilder.controls[ 'datasource' ].prototype,
		{
			ftype:"frecordsetds",
			records : [],
			show:function(){ return '<input id="'+this.name+'" name="'+this.name+'" class="cpcff-recordset" type="hidden" />'; },
			after_show : function(){
				var me = this;				
				$.fbuilder.controls[ 'datasource' ].prototype.getData.call( this, function( data )
					{
						var v = '';				
						if( typeof data['error'] != 'undefined' )
						{
							alert( data.error );
						}
						else
						{
							me.records = [];
							if( data.data.length )
							{
								
								me.records = data.data;
							}
						}	
						$( '#' + me.name ).trigger( 'change' );
					}
				);
			},
			val: function(){
				var e = $( '[id="' + this.name + '"]:not(.ignore)' );
				if( e.length )
				{
					return this.records;	 
				}
				return [];
			}
	});