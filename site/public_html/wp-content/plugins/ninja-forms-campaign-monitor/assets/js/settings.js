var nfMCSettings = Marionette.Object.extend( {
	initialize: function() {
		jQuery( '#ninja_forms\\[campaign_monitor_multi_keys\\]' ).on( 'change', this.changeSetting );
		this.changeSetting();
	},

	changeSetting: function() {
		for (var i = 4; i >= 0; i--) {
			if ( jQuery( '#ninja_forms\\[campaign_monitor_multi_keys\\]' ).prop( 'checked' ) ) {
				jQuery( '#ninja_forms\\[ninja_forms_cm_api' + i + '\\]' ).closest( 'tbody' ).show();
				jQuery( '#ninja_forms\\[ninja_forms_cm_client' + i + '\\]' ).closest( 'tbody' ).show();
				jQuery( '#row_ninja_forms\\[campaign_monitor_divider' + i + '\\]' ).closest( 'tbody' ).show();
			} else {
				jQuery( '#ninja_forms\\[ninja_forms_cm_api' + i + '\\]' ).closest( 'tbody' ).hide();
				jQuery( '#ninja_forms\\[ninja_forms_cm_client' + i + '\\]' ).closest( 'tbody' ).hide();
				jQuery( '#row_ninja_forms\\[campaign_monitor_divider' + i + '\\]' ).closest( 'tbody' ).hide();
			}
		}
	},

});


jQuery( document ).ready( function( $ ) {
	new nfMCSettings();
} );