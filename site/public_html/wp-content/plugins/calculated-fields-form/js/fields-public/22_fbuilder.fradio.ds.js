	$.fbuilder.controls[ 'fradiods' ]=function(){};
	$.extend(
		$.fbuilder.controls[ 'fradiods' ].prototype,
		$.fbuilder.controls[ 'fradio' ].prototype,
		$.fbuilder.controls[ 'datasource' ].prototype,
		{
			ftype:"fradiods",
			defaultSelection:"",
			show:function()
				{
					return '<div class="fields '+this.csslayout+' cff-radiobutton-field" id="field'+this.form_identifier+'-'+this.index+'"><label>'+this.title+''+((this.required)?"<span class='r'>*</span>":"")+'</label><div class="dfield"><input type="hidden" name="'+this.name+'" id="'+this.name+'" value="" /><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				},
			after_show : function()
				{
					var me = this, 
						first_time = true,
						ignorepb = ($('[id="'+me.name+'"]').closest('.pbreak').is(':visible')) ? '' : ' ignorepb ';
						
					$.fbuilder.controls[ 'datasource' ].prototype.getData.call( this, function( data )
						{ 
							var str = '';
							if( typeof data['error'] != 'undefined' )
							{
								alert( data.error );
							}
							else
							{
								for( var i = 0, h = data.data.length; i < h; i++ )
								{
									var e = data.data[ i ];
									str += '<div class="' + me.layout + '"><label><input name="' + me.name + '" id="' + me.name + '" class="field group ' + ( ( me.required ) ? ' required ' : '' ) + ignorepb + '" value="' + $.fbuilder.htmlEncode( e.value ) + '" vt="' + $.fbuilder.htmlEncode( (me.toSubmit == 'text') ? e.text : e.value ) + '" type="radio" i="' + i + '" /> ' + e.text + '</label></div>';
								}
							}	
							$( '#field' + me.form_identifier + '-' + me.index + ' .dfield' ).html( str );
							if( first_time )
							{
								first_time = false;
								$.fbuilder.controls[ 'datasource' ].prototype.setDefault.call( me );
							}
							$( '#' + me.name ).change();
						}
					);
				},
			setVal : function( v )
				{
					this.defaultSelection = v;
					$.fbuilder.controls[ 'fradio' ].prototype.setVal.call( this, v );
				}	
		}
	);