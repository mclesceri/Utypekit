<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'CPCFF_reCAPTCHA' ) )
{
    class CPCFF_reCAPTCHA extends CPCFF_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-recaptcha-20151106";
		protected $name = "CFF - reCAPTCHA";
		protected $description;

		public function get_addon_settings()
		{
			if( isset( $_REQUEST[ 'cpcff_recaptcha' ] ) )
			{
				check_admin_referer( 'session_id_'.CP_SESSION::session_id(), '_cpcff_nonce' );
				update_option( 'cpcff_recaptcha_sitekey', trim( $_REQUEST[ 'cpcff_recaptcha_sitekey' ] ) );
				update_option( 'cpcff_recaptcha_secretkey', trim( $_REQUEST[ 'cpcff_recaptcha_secretkey' ] ) );
				update_option( 'cpcff_recaptcha_invisible', isset($_REQUEST[ 'cpcff_recaptcha_invisible' ] ) ? 1 : 0 );
				update_option( 'cpcff_recaptcha_check_twice', isset($_REQUEST[ 'cpcff_recaptcha_check_twice' ] ) ? 1 : 0 );
			}
			?>
			<form method="post">
				<div id="metabox_basic_settings" class="postbox" >
					<h3 class='hndle' style="padding:5px;"><span><?php print $this->name; ?></span></h3>
					<div class="inside">
						<table cellspacing="0" style="width:100%;">
							<tr>
								<td style="white-space:nowrap;width:200px;"><?php _e('Site Key', 'calculated-fields-form');?>:</td>
								<td>
									<input type="text" name="cpcff_recaptcha_sitekey" value="<?php echo ( ( $key = get_option( 'cpcff_recaptcha_sitekey' ) ) !== false ) ? $key : ''; ?>"  style="width:80%;" />
								</td>
							</tr>
							<tr>
								<td style="white-space:nowrap;width:200px;"><?php _e('Secret Key', 'calculated-fields-form');?>:</td>
								<td>
									<input type="text" name="cpcff_recaptcha_secretkey" value="<?php echo ( ( $key = get_option( 'cpcff_recaptcha_secretkey' ) ) !== false ) ? $key : ''; ?>" style="width:80%;" />
								</td>
							</tr>
							<tr>
								<td>
								</td>
								<td>
									<input type="checkbox" name="cpcff_recaptcha_invisible" <?php echo ( get_option( 'cpcff_recaptcha_invisible' ) ) ? 'CHECKED' : ''; ?> />
									<?php _e('Is it a key for invisible reCAPTCHA?','calculated-fields-form');?>
								</td>
							</tr>
							<tr>
								<td>
								</td>
								<td>
									<input type="checkbox" name="cpcff_recaptcha_check_twice" <?php echo ( get_option( 'cpcff_recaptcha_check_twice' ) ) ? 'CHECKED' : ''; ?> />
									<?php _e('Check reCAPTCHA in both sides, client and server','calculated-fields-form');?><br>
									<em><?php _e('If there is any issue with the sessions in your server, please, untick the checkbox','calculated-fields-form');?></em>
								</td>
							</tr>

						</table>
						<input type="submit" name="Save settings" />
					</div>
					<input type="hidden" name="cpcff_recaptcha" value="1" />
					<input type="hidden" name="_cpcff_nonce" value="<?php echo wp_create_nonce( 'session_id_'.CP_SESSION::session_id() ); ?>" />
				</div>
			</form>
			<?php
		}

		/************************ ADDON CODE *****************************/
        /************************ ATTRIBUTES *****************************/

		private $_recaptcha_inserted = false;
		private $_recaptcha_callback = false;
		private $_im_flag    = false; // I'm
		private $_sitekey 	= '';
		private $_secretkey = '';
		private $_invisible = false;
		private	$_check_twice = false;

        /************************ CONSTRUCT *****************************/

        function __construct()
        {
			$this->description = __("The add-on allows to protect the forms with reCAPTCHA service of Google", 'calculated-fields-form');

            // Check if the plugin is active
			if( !$this->addon_is_active() ) return;
			if( !is_admin() )
			{
				if( $this->apply_addon() !== false )
				{
					// If reCAPTCHA is enabled do not include the common captcha in the form
					add_filter( 'cpcff_get_option', array( &$this, 'get_form_options' ), 10, 3 );

					// If the reCAPTCHA is being validated with AJAX in the doValidate routine
					if( isset( $_REQUEST[ 'cpcff_recaptcha_response' ] ) )
					{
						if(
							!empty($_REQUEST[ 'cpcff_id' ]) &&
							$this->validate_form( trim( $_REQUEST[ 'cpcff_recaptcha_response' ] ), intval(@$_REQUEST[ 'cpcff_id' ] ) )
						)
						{
							print 'ok';
						}
						else
						{
							print 'captchafailed';
						}
						exit;
					}

					// Inserts the SCRIPT tag to import the reCAPTCHA on webpage
					add_action( 'wp_footer', array( &$this, 'insert_script' ), 99 );

					// Inserts the reCAPTCHA field in the form
					add_filter( 'cpcff_the_form', array( &$this, 'insert_recaptcha'), 99, 2 );

					// Validate the form's submission
					add_filter( 'cpcff_valid_submission', array( &$this, 'validate_form' ) );

					// Insert the JS code to validate the recaptcha code through AJAX
					add_action( 'cpcff_script_after_validation', array( &$this, 'validate_form_script'), 1, 2 );
				}
			}
        } // End __construct

        /************************ PRIVATE METHODS *****************************/

		/**
		 * Check if the API keys have been defined and return the pair of keys or false
		 */
        private function apply_addon()
		{
			if(
				( $sitekey   = get_option( 'cpcff_recaptcha_sitekey' ) ) !== false && !empty( $sitekey ) &&
				( $secretkey = get_option( 'cpcff_recaptcha_secretkey' ) ) !== false && !empty( $secretkey )
			)
			{
				$this->_sitekey   = $sitekey;
				$this->_secretkey = $secretkey;
				$this->_invisible = get_option( 'cpcff_recaptcha_invisible' );
				$this->_check_twice = get_option( 'cpcff_recaptcha_check_twice' );
				return true;
			}
			return false;

		} // End apply_addon

		/************************ PUBLIC METHODS  *****************************/

		/**
         * Check if the reCAPTCHA is used in the form, and inserts the SCRIPT tag that includes its code
         */
        public function	insert_script( $params )
		{
			if( $this->_recaptcha_inserted )
			{
				if( !$this->_recaptcha_callback )
				{
					print '
					<script type="text/javascript">
						var cff_reCAPTCHA_callback = function(){
							jQuery( ".g-recaptcha" ).each(
								function()
								{
									grecaptcha.render( this, {"sitekey" : "'.$this->_sitekey.'"'.(($this->_invisible) ? ', "size":"invisible"':'').' });
									jQuery(this).closest("form").find(\'[id*="captchaimg"],[id*="hdcaptcha_cp_calculated_fields_form_post"]\').closest(".fields").remove();
								}
							);
						};
					</script>';
					$this->_recaptcha_callback = true;
				}
				print '<script src="//www.google.com/recaptcha/api.js?onload=cff_reCAPTCHA_callback&render=explicit" async defer></script>';
			}
		} // End insert_script

		/**
         * Check if the reCAPTCHA is used in the form, and inserts the reCAPTCHA tag
         */
        public function	insert_recaptcha( $form_code, $id )
		{

			$this->_im_flag = true;
			$is_captcha_enabled = cp_calculatedfieldsf_get_option('cv_enable_captcha', true, $id );
			$this->_im_flag = false;

			if( $is_captcha_enabled == false || $is_captcha_enabled == 'false' )
			{
				return $form_code;
			}
			$this->_recaptcha_inserted = true;
			return str_replace( '<!--add-ons-->', '<!--add-ons--><div style="margin-top:20px;" class="g-recaptcha" data-sitekey="'.$this->_sitekey.'" '.(($this->_invisible)?'data-size="invisible"':'').'></div>', $form_code );
		} // End insert_recaptcha

		/**
         * Insert the JS code into the doValidate function for checking the reCAPTCHA code with AJAX
         */
        public function validate_form_script( $sequence, $formid )
		{
			$this->_im_flag = true;
			$is_captcha_enabled = cp_calculatedfieldsf_get_option('cv_enable_captcha', true, $formid );
			$this->_im_flag = false;

			if( $is_captcha_enabled == false || $is_captcha_enabled == 'false' )
			{
				return;
			}

			global $cpcff_default_texts_array;
			$cpcff_texts_array = cp_calculatedfieldsf_get_option( 'vs_all_texts', $cpcff_default_texts_array, $formid );
			$cpcff_texts_array = array_replace_recursive(
				$cpcff_default_texts_array,
				is_string( $cpcff_texts_array ) ? unserialize( $cpcff_texts_array ) : $cpcff_texts_array
			);

		?>
			var recaptcha = $dexQuery( '[name="cp_calculatedfieldsf_pform<?php print $sequence; ?>"] [name="g-recaptcha-response"]' );
			if(
				recaptcha.length == 0 ||
				/^\s*$/.test( recaptcha.val() )
			)
			{

				var grecaptcha_e = $dexQuery( '[name="cp_calculatedfieldsf_pform<?php print $sequence; ?>"] [class="g-recaptcha"]' );
				if(grecaptcha_e.length && grecaptcha_e.attr( 'data-size' ) == 'invisible')
					grecaptcha.execute();
				else
					alert('<?php echo( $cpcff_texts_array[ 'captcha_required_text' ][ 'text' ] ); ?>');
				return false;
			}
		<?php
			if($this->_check_twice)
			{
		?>
			else
			{
				if(
					typeof validation_rules['<?php print esc_js( $this->addonID); ?>'] == 'undefined'||
					validation_rules['<?php print esc_js( $this->addonID); ?>'] == false
				)
				{
					validation_rules['<?php print esc_js( $this->addonID); ?>'] = false;
					$dexQuery.ajax({
						type: "GET",
						url:  "<?php echo cp_calculatedfieldsf_get_site_url(); ?>",
						data: {
							ps: "<?php echo $sequence; ?>",
							cpcff_recaptcha_response: recaptcha.val(),
							cpcff_id:<?php print $formid; ?>
						},
						success:function(result){
							if (result.indexOf("captchafailed") != -1)
							{
								alert('<?php echo( $cpcff_texts_array[ 'incorrect_captcha_text' ][ 'text' ] ); ?>');
							}
							else
							{
								validation_rules['<?php print esc_js( $this->addonID); ?>'] = true;
								processing_form();
							}
						}
					});
				}
			}
		<?php
			} // End if _check_twice
		} // End validate_form_script

		/**
         * Check if the reCAPTCHA is valid and return a boolean
         */
        public function	validate_form( $recaptcha_response = '', $id='' )
		{
			$this->_im_flag = true;
			$is_captcha_enabled = cp_calculatedfieldsf_get_option('cv_enable_captcha', true, $id );
			$this->_im_flag = false;

			if( $is_captcha_enabled == false || $is_captcha_enabled == 'false' )
			{
				return true;
			}

			// If was enabled the twice validation and the reCAPTCHA was validated with AJAX
			if(
				CP_SESSION::get_var('cpcff_recaptcha_i_am_human') !== false
			)
			{
				CP_SESSION::unset_var('cpcff_recaptcha_i_am_human');
				return true;
			}

			// The reCAPTCHA value is received in the form's submission
			if( isset( $_POST[ 'g-recaptcha-response' ] ) )
			{
				$recaptcha_response = $_POST[ 'g-recaptcha-response' ];
			}

			if( !empty( $recaptcha_response ) )
			{
				$response = wp_remote_post(
					'https://www.google.com/recaptcha/api/siteverify',
					array(
						'body' => array(
							'secret' 	=> $this->_secretkey,
							'response' 	=> $recaptcha_response
						)
					)
				);

				if( !is_wp_error( $response ) )
				{
					$response = json_decode( $response[ 'body' ] );
					if( !is_null( $response ) && isset( $response->success ) && $response->success )
					{
						CP_SESSION::set_var('cpcff_recaptcha_i_am_human', 1);
						return true;
					}

				}

			}
			return false;

		} // End cpcff_valid_submission

		/**
         * Corrects the form options
         */
        public function get_form_options( $value, $field, $id )
        {

			if( !$this->_im_flag && $field == 'cv_enable_captcha' && $this->apply_addon() !== false ){
				return 0;
			}
            return $value;
		} // End get_form_options

    } // End Class

    // Main add-on code
    $cpcff_recaptcha_obj = new CPCFF_reCAPTCHA();

	// Add addon object to the objects list
	global $cpcff_addons_objs_list;
	$cpcff_addons_objs_list[ $cpcff_recaptcha_obj->get_addon_id() ] = $cpcff_recaptcha_obj;
}
?>