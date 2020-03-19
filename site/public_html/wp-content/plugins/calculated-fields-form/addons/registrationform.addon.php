<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'CPCFF_RegistrationForm' ) )
{
    class CPCFF_RegistrationForm extends CPCFF_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-registrationform-20170306";
		protected $name = "CFF - User Registration Form";
		protected $description;

		public function get_addon_form_settings( $formid )
		{
			global $wpdb;
			if( isset( $_REQUEST[ 'cpcff_registration_form' ] ) )
			{
				// Save the addon settings
				$settings = array(
					'enabled'	=> (isset($_REQUEST[ 'cpcff_registration_form' ][ 'enabled' ])) ? 1 : 0,

					'fields' 	=> array(
						'user_login' 	=> (isset($_REQUEST[ 'cpcff_registration_form' ][ 'user_login' ])) ? trim( $_REQUEST[ 'cpcff_registration_form' ][ 'user_login' ] ) : '',
						'user_pass' 	=> (isset($_REQUEST[ 'cpcff_registration_form' ][ 'user_pass' ])) ? trim( $_REQUEST[ 'cpcff_registration_form' ][ 'user_pass' ] ) : '',
						'user_nicename' => (isset($_REQUEST[ 'cpcff_registration_form' ][ 'user_nicename' ])) ? trim( $_REQUEST[ 'cpcff_registration_form' ][ 'user_nicename' ] ) : '',
						'user_email' 	=> (isset($_REQUEST[ 'cpcff_registration_form' ][ 'user_email' ])) ? trim( $_REQUEST[ 'cpcff_registration_form' ][ 'user_email' ] ) : '',
						'user_url' 		=> (isset($_REQUEST[ 'cpcff_registration_form' ][ 'user_url' ])) ? trim( $_REQUEST[ 'cpcff_registration_form' ][ 'user_url' ] ) : '',
						'display_name'	=> (isset($_REQUEST[ 'cpcff_registration_form' ][ 'display_name' ])) ? trim( $_REQUEST[ 'cpcff_registration_form' ][ 'display_name' ] ) : ''
					),

					'usermeta' 	=>array(),

					'user_role' 	=> (isset($_REQUEST[ 'cpcff_registration_form' ][ 'user_role' ])) ? trim($_REQUEST[ 'cpcff_registration_form' ][ 'user_role' ]) : '',

					'notification'  => (isset($_REQUEST[ 'cpcff_registration_form' ][ 'notification' ])) ? strtolower(trim($_REQUEST[ 'cpcff_registration_form' ][ 'notification' ])) : 'none',

					'messages' => array(
						'login_required' 	=> (isset($_REQUEST[ 'cpcff_registration_form' ][ 'login_required' ])) ? trim($_REQUEST[ 'cpcff_registration_form' ][ 'login_required' ]) :'',
						'login_exists'		=> (isset($_REQUEST[ 'cpcff_registration_form' ][ 'login_exists' ])) ? trim($_REQUEST[ 'cpcff_registration_form' ][ 'login_exists' ]) :'',
						'login_too_long' 	=> (isset($_REQUEST[ 'cpcff_registration_form' ][ 'login_too_long' ])) ? trim($_REQUEST[ 'cpcff_registration_form' ][ 'login_too_long' ]) :'',
						'nicename_too_long' => (isset($_REQUEST[ 'cpcff_registration_form' ][ 'nicename_too_long' ])) ? trim($_REQUEST[ 'cpcff_registration_form' ][ 'nicename_too_long' ]) :'',
						'email_required'	=> (isset($_REQUEST[ 'cpcff_registration_form' ][ 'email_required' ])) ? trim($_REQUEST[ 'cpcff_registration_form' ][ 'email_required' ]) :'',
						'email_exists'		=> (isset($_REQUEST[ 'cpcff_registration_form' ][ 'email_exists' ])) ? trim($_REQUEST[ 'cpcff_registration_form' ][ 'email_exists' ]) :'',
						'invalid_email'		=> (isset($_REQUEST[ 'cpcff_registration_form' ][ 'invalid_email' ])) ? trim($_REQUEST[ 'cpcff_registration_form' ][ 'invalid_email' ]) :''
					)
				);

				if(
					isset($_REQUEST[ 'cpcff_registration_form' ][ 'usermeta' ]) &&
					is_array($_REQUEST[ 'cpcff_registration_form' ][ 'usermeta' ]) &&

					isset($_REQUEST[ 'cpcff_registration_form' ][ 'usermeta_field' ]) &&
					is_array($_REQUEST[ 'cpcff_registration_form' ][ 'usermeta_field' ])
				)
				{
					$usermeta = $_REQUEST[ 'cpcff_registration_form' ][ 'usermeta' ];
					$usermeta_field = $_REQUEST[ 'cpcff_registration_form' ][ 'usermeta_field' ];

					for( $i = 0; $i < count($usermeta); $i++ )
					{
						if(
							($um = trim($usermeta[$i])) != '' &&
							($umf = trim($usermeta_field[$i])) != ''
						)
						{
							$settings['usermeta'][$um] = $umf;
						}
					}
				}

				$wpdb->delete(
					$wpdb->prefix.$this->db_table,
					array('formid' => $formid),
					'%d'
				);

				$wpdb->insert(
					$wpdb->prefix.$this->db_table,
					array(
						'formid' => $formid,
						'data' => serialize($settings)
					),
					array( '%d', '%s' )
				);
			}
			else
			{
				$settings = $this->_get_form_settings( $formid );
				if(empty($settings)) $settings = array();
			}
			?>
			<div id="metabox_basic_settings" class="postbox" >
				<h3 class='hndle' style="padding:5px;"><span><?php print $this->name; ?></span></h3>
				<div class="inside">
					<style>
						@media screen and (min-width:760px)
						{
							.registrationform-column{float:left;width:49.9%;}
						}
					</style>
					<div class="registrationform-column">
						<table cellspacing="0">
							<tr>
								<td valign="top"><?php _e('Enabled', 'calculated-fields-form');?>:</td>
								<td style="padding-bottom:10px;">
									<input type="checkbox" name="cpcff_registration_form[enabled]" <?php
										if(!empty($settings['enabled'])) echo "CHECKED";
									?> /> <em><?php _e('Register a new user through the form', 'calculated-fields-form'); ?></em>
								</td>
							</tr>
							<tr>
								<td valign="top"><?php _e('Notification', 'calculated-fields-form');?>:</td>
								<td style="padding-bottom:10px;">
									<input type="radio" name="cpcff_registration_form[notification]" value="none" <?php
										if(empty($settings['notification']) || $settings['notification'] == 'none') echo "CHECKED";
									?> /> <?php _e('Do not send any notification', 'calculated-fields-form'); ?><br />
									<input type="radio" name="cpcff_registration_form[notification]" value="user" <?php
										if(!empty($settings['notification']) && $settings['notification'] == 'user') echo "CHECKED";
									?> /> <?php _e('Send notifications only to the users', 'calculated-fields-form'); ?><br />
									<input type="radio" name="cpcff_registration_form[notification]" value="admin" <?php
										if(!empty($settings['notification']) && $settings['notification'] == 'admin') echo "CHECKED";
									?> /> <?php _e('Send notifications only to the website administrator', 'calculated-fields-form'); ?><br />
									<input type="radio" name="cpcff_registration_form[notification]" value="both" <?php
										if(!empty($settings['notification']) && $settings['notification'] == 'both') echo "CHECKED";
									?> /> <?php _e('Send notifications to both', 'calculated-fields-form'); ?>
								</td>
							</tr>
							<tr>
								<td valign="top"><?php _e('User email field(required)', 'calculated-fields-form');?>:</td>
								<td>
									<input type="text" name="cpcff_registration_form[user_email]" value="<?php echo esc_attr( (!empty($settings['fields']['user_email'])) ? $settings['fields']['user_email'] : ''); ?>"  placeholder="fieldname#" />
								</td>
							</tr>
							<tr>
								<td valign="top"><?php _e('User login field(required)', 'calculated-fields-form');?>:</td>
								<td>
									<input type="text" name="cpcff_registration_form[user_login]" value="<?php echo esc_attr( (!empty($settings['fields']['user_login'])) ? $settings['fields']['user_login'] : ''); ?>"  placeholder="fieldname#" />
								</td>
							</tr>
							<tr>
								<td valign="top"><?php _e('User password field', 'calculated-fields-form');?>:</td>
								<td>
									<input type="text" name="cpcff_registration_form[user_pass]" value="<?php echo esc_attr( (!empty($settings['fields']['user_pass'])) ? $settings['fields']['user_pass'] : ''); ?>"  placeholder="fieldname#" />
								</td>
							</tr>
							<tr>
								<td valign="top"><?php _e('User nicename field', 'calculated-fields-form');?>:</td>
								<td>
									<input type="text" name="cpcff_registration_form[user_nicename]" value="<?php echo esc_attr( (!empty($settings['fields']['user_nicename'])) ? $settings['fields']['user_nicename'] : ''); ?>"  placeholder="fieldname#" />
								</td>
							</tr>
							<tr>
								<td valign="top"><?php _e('Display name field', 'calculated-fields-form');?>:</td>
								<td>
									<input type="text" name="cpcff_registration_form[display_name]" value="<?php echo esc_attr( (!empty($settings['fields']['display_name'])) ? $settings['fields']['display_name'] : ''); ?>"  placeholder="fieldname#" />
								</td>
							</tr>
							<tr>
								<td valign="top"><?php _e('User url field', 'calculated-fields-form');?>:</td>
								<td style="padding-bottom:10px;">
									<input type="text" name="cpcff_registration_form[user_url]" value="<?php echo esc_attr( (!empty($settings['fields']['user_url'])) ? $settings['fields']['user_url'] : ''); ?>"  placeholder="fieldname#" />
								</td>
							</tr>
							<tr>
								<td valign="top"><?php _e('User role', 'calculated-fields-form');?>:</td>
								<td style="padding-bottom:10px;">
									<select name="cpcff_registration_form[user_role]">
									<?php
										// Get the roles list
										global $wp_roles;
										if ( !isset( $wp_roles ) )
										{
											$wp_roles = new WP_Roles();
										}
										$roles = $wp_roles->get_names();

										foreach( $roles as $_role_value => $_role_name )
										{
											$_selected = '';
											if(
												!empty( $settings[ 'user_role' ] ) &&
												$_role_value == $settings[ 'user_role' ]
											)
											{
												$_selected = 'SELECTED';
											}
											print '<option value="'.$_role_value.'" '.$_selected.'>'.$_role_name.'</option>';
										}
									?>
									</select>
								</td>
							</tr>
						</table>
						<h3><?php _e( 'User metada', 'calculated-fields-form' ); ?></h3>
						<table cellspacing="0" class="cpcff_formregistration_usermeta">
							<?php
								if(
									!empty($settings['usermeta']) &&
									is_array($settings['usermeta'])
								)
								{
									foreach( $settings['usermeta'] as $usermeta => $usermeta_field)
									{
										print '
											<tr>
												<td><input type="text" name="cpcff_registration_form[usermeta][]" value="'.esc_attr($usermeta).'" /></td>
												<td><input type="text" name="cpcff_registration_form[usermeta_field][]" value="'.esc_attr($usermeta_field).'" placeholder="fieldname#" /></td>
												<td><input type="button" value="'.__('Delete', 'calculated-fields-form').'" onclick="cpcff_formregistration_delete_usermeta(this);" /></td>
											</tr>
										';
									}
								}
							?>
						</table>
						<p>
							<input type="button" class="button-primary" value="<?php _e('Add metada'); ?>" onclick="cpcff_formregistration_add_usermeta();">
						</p>
						<script type="text/template" id="cpcff_registration_form_usermeta_tpl">
							<tr>
								<td><input type="text" name="cpcff_registration_form[usermeta][]" value="" /></td>
								<td><input type="text" name="cpcff_registration_form[usermeta_field][]" value="" placeholder="fieldname#" /></td>
								<td><input type="button" value="<?php _e('Delete', 'calculated-fields-form'); ?>" onclick="cpcff_formregistration_delete_usermeta(this);" /></td>
							</tr>
						</script>
						<script>
							function cpcff_formregistration_delete_usermeta(e)
							{
								jQuery(e).closest('tr').remove();
							}
							function cpcff_formregistration_add_usermeta()
							{
								jQuery('.cpcff_formregistration_usermeta').append(jQuery('#cpcff_registration_form_usermeta_tpl').html());
							}
							cpcff_formregistration_add_usermeta();
						</script>
					</div>
					<div class="registrationform-column">
						<table cellspacing="0" style="width:100%;">
							<tr>
								<td>
									<h3><?php _e( 'Error messages', 'calculated-fields-form' ); ?></h3>
									<p><?php _e('Login required', 'calculated-fields-form'); ?></p>
									<p><textarea name="cpcff_registration_form[login_required]" style="width:100%;height:60px;"><?php print esc_textarea( (isset($settings['messages']['login_required'])) ? $settings['messages']['login_required'] : '' ); ?></textarea></p>

									<p><?php _e('Login exists', 'calculated-fields-form'); ?></p>
									<p><textarea name="cpcff_registration_form[login_exists]" style="width:100%;height:60px;"><?php print esc_textarea( (isset($settings['messages']['login_exists'])) ? $settings['messages']['login_exists'] : '' ); ?></textarea></p>

									<p><?php _e('Login too long', 'calculated-fields-form'); ?></p>
									<p><textarea name="cpcff_registration_form[login_too_long]" style="width:100%;height:60px;"><?php print esc_textarea( (isset($settings['messages']['login_too_long'])) ? $settings['messages']['login_too_long'] : '' ); ?></textarea></p>

									<p><?php _e('Email required', 'calculated-fields-form'); ?></p>
									<p><textarea name="cpcff_registration_form[email_required]" style="width:100%;height:60px;"><?php print esc_textarea( (isset($settings['messages']['email_required'])) ? $settings['messages']['email_required'] : '' ); ?></textarea></p>

									<p><?php _e('Invalid email', 'calculated-fields-form'); ?></p>
									<p><textarea name="cpcff_registration_form[invalid_email]" style="width:100%;height:60px;"><?php print esc_textarea( (isset($settings['messages']['invalid_email'])) ? $settings['messages']['invalid_email'] : '' ); ?></textarea></p>

									<p><?php _e('Email exists'); ?></p>
									<p><textarea name="cpcff_registration_form[email_exists]" style="width:100%;height:60px;"><?php print esc_textarea( (isset($settings['messages']['email_exists'])) ? $settings['messages']['email_exists'] : '' ); ?></textarea></p>

									<p><?php _e('Nicename too long'); ?></p>
									<p><textarea name="cpcff_registration_form[nicename_too_long]" style="width:100%;height:60px;"><?php print esc_textarea( (isset($settings['messages']['nicename_too_long'])) ? $settings['messages']['nicename_too_long'] : '' ); ?></textarea></p>
								</td>
							</tr>
						</table>
					</div>
					<div style="clear:both;"></div>
				</div>
			</div>
			<?php
		}

		/************************ ADDON CODE *****************************/
        /************************ ATTRIBUTES *****************************/

		private $db_table = 'cp_calculated_fields_registration_form';

        /************************ CONSTRUCT *****************************/

        function __construct()
        {
			$this->description = __("The add-on allows to create an User Registration Form", 'calculated-fields-form' );

			// Check if the plugin is active
			if( !$this->addon_is_active() ) return;

			// Creates the user
			add_action( 'cpcff_process_data_before_insert', array( &$this, 'create_new_user' ), 1, 3 );

			// Insert the JS code to validate the required user data
			add_action( 'cpcff_script_after_validation', array( &$this, 'javascript_validation_code'), 1, 2 );

			if( is_admin() )
			{
				// Delete forms
				add_action( 'cpcff_delete_form', array(&$this, 'delete_form') );

				// Clone forms
				add_action( 'cpcff_clone_form', array(&$this, 'clone_form'), 10, 2 );

				// Export addon data
				add_action( 'cpcff_export_addons', array(&$this, 'export_form'), 10, 2 );

				// Import addon data
				add_action( 'cpcff_import_addons', array(&$this, 'import_form'), 10, 2 );
			}
			else
			{
				add_action('init', array(&$this, 'validate'),1);
			}
        } // End __construct

        /************************ PROTECTED METHODS *****************************/

		/**
         * Creates the database tables
         */
        protected function update_database()
		{
			global $wpdb;
			$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix.$this->db_table." (
					formid INT NOT NULL,
					data TEXT NOT NULL,
					PRIMARY KEY (formid)
				)
				CHARACTER SET utf8
				COLLATE utf8_general_ci;";

			$wpdb->query($sql);
		} // End update_database

        /************************ PRIVATE METHODS *****************************/

		/**
		 * Read the form's settings from the database and unserialize it
		 */
		private function _get_form_settings( $formid )
		{
			global $wpdb;
			$result = $wpdb->get_row( $wpdb->prepare('SELECT data FROM '.$wpdb->prefix.$this->db_table.' WHERE formid=%d', $formid) );
			if(!is_null($result))
			{
				if( ($settings = @unserialize($result->data)) != false ) return $settings;
			}

			return array(
					'enabled'	=> 0,
					'fields' 	=> array(
						'user_login' 	=> '',
						'user_pass' 	=> '',
						'user_nicename' => '',
						'user_email' 	=> '',
						'user_url' 		=> '',
						'display_name'	=> ''
					),
					'usermeta' 		=>array(),
					'user_role' 	=> '',
					'notification'  => 'none',
					'messages' => array(
						'login_required' 	=> 'Login required',
						'login_exists'		=> 'The login is associated to another user',
						'login_too_long' 	=> 'The user login is too long (no more than 60 characters)',
						'nicename_too_long' => 'The nicename is too long (no more than 50 characters)',
						'email_required'	=> 'Email required',
						'email_exists'		=> 'The email address is associated to another user',
						'invalid_email'		=> 'Invalid Email'
					)
				);

		} // End  _get_form_settings

		private function _remove_passwords(&$params, &$str, $fields)
		{
			foreach( $fields as $name => $field)
			{
				if( $field->ftype == 'fpassword' )
				{
					$_to_remove = '/'.preg_quote($field->title.': '.$params[ $name ]).'(\\n)*/';
					$str = preg_replace($_to_remove, '', $str);
					unset($params[ $name ]);
				}
			}
		} // End _remove_passwords

		private function _replace_fields_on_settings($settings, $params)
		{
			foreach($settings['fields'] as $attr => $fieldname)
			{
				if(
					preg_match('/^fieldname\d+$/i',$fieldname) &&
					!empty($params[$fieldname])
				)
				{
					$settings['fields'][$attr] = trim($params[$fieldname]);
				}
				else
				{
					$settings['fields'][$attr] = '';
				}

			}

			foreach($settings['usermeta'] as $attr => $fieldname)
			{
				if(
					preg_match('/^fieldname\d+$/i',$fieldname) &&
					!empty($params[$fieldname])
				)
				{
					$settings['usermeta'][$attr] = trim($params[$fieldname]);
				}
				else
				{
					$settings['usermeta'][$attr] = '';
				}
			}

			if( empty($settings['user_role']) || wp_roles()->is_role($settings['user_role']) == null ) $settings['user_role'] =get_option('default_role', 'subscriber');

			if(
				empty($settings['notification']) ||
				!in_array($settings['notification'], array('admin','user','both'))
			) $settings['notification'] = false;

			return $settings;
		} // End _replace_fields_on_settings

		private function _is_valid( $settings, $_check_attr_lengths = false )
		{

			if( empty($settings['fields']['user_login']) )
			{
				return $settings['messages']['login_required'];
			}

			if( $_check_attr_lengths )
			{
				if(60 < strlen($settings['fields']['user_login']))
					return $settings['messages']['login_too_long'];
				if(
					!empty($settings['fields']['user_nicename']) &&
					50 < strlen($settings['fields']['user_nicename'])
				)
					return $settings['messages']['nicename_too_long'];
			}

			if( empty($settings['fields']['user_email']) )
			{
				return $settings['messages']['email_required'];
			}
			elseif( !is_email($settings['fields']['user_email']) )
			{
				return $settings['messages']['invalid_email'];
			}

			if(username_exists($settings['fields']['user_login']))
			{
				return $settings['messages']['login_exists'];
			}

			if(email_exists($settings['fields']['user_email']))
			{
				return $settings['messages']['email_exists'];
			}

			return true;
		}
		/************************ PUBLIC METHODS  *****************************/

		/**
		 * Creates a new user with the data submitted by the form
		 */
		public function create_new_user( &$params, &$str, $fields )
		{
			if(!empty($params['formid']))
			{
				$settings = $this->_get_form_settings( $params[ 'formid' ] );

				if($settings['enabled'])
				{
					$settings = $this->_replace_fields_on_settings($settings, $params);
					$this->_remove_passwords($params, $str, $fields);
					if( ($_is_valid = $this->_is_valid($settings)) === true)
					{
						if( empty($settings['fields']['user_pass']) )
							$settings['fields']['user_pass'] = wp_generate_password();

						// Create User
						$args = array('role' => $settings['user_role']);
						foreach($settings['fields'] as $field => $value)
							$args[$field] = $value;

						$args['user_login'] = substr($args['user_login'],0,60);
						if(!empty($args['user_nicename'])) $args['user_nicename'] = substr($args['user_nicename'],0,50);

						$user_id = wp_insert_user($args);
						if(!is_wp_error($user_id))
						{
							// Set the rest of metadata
							$args = array();
							foreach( $settings['usermeta'] as $meta_key => $meta_value)
								update_user_meta($user_id, $meta_key, $meta_value);

							if($settings['notification'] !== false)
								wp_new_user_notification($user_id, null, $settings['notification']);
						}
					}
					else
					{
						die($_is_valid);
					}
				}
			}
		} // End create_new_user

		/**
		 *	Delete the form from the addon's table
		 */
        public function delete_form( $formid)
		{
			global $wpdb;
			$wpdb->delete( $wpdb->prefix.$this->db_table, array('formid' => $formid), '%d' );
		} // delete_form

		/**
         * Insert the JS code into the doValidate function for checking the user's data
         */
		public function javascript_validation_code( $sequence, $formid )
		{
			$settings = $this->_get_form_settings( $formid );
			if(
				$settings[ 'enabled' ] &&

				isset( $settings[ 'fields' ][ 'user_login' ] ) &&
				( $user_login = trim( $settings[ 'fields' ][ 'user_login' ] ) ) != '' &&
				preg_match( '/^fieldname\d+$/', $user_login) &&

				isset( $settings[ 'fields' ][ 'user_email' ] ) &&
				( $user_email = trim( $settings[ 'fields' ][ 'user_email' ] ) ) != '' &&
				preg_match( '/^fieldname\d+$/', $user_email)
			)
			{
				$user_nicename = '';
				if(
					isset($settings['fields']['user_nicename']) &&
					( $user_nicename = trim( $settings[ 'fields' ][ 'user_nicename' ] ) ) != '' &&
					preg_match( '/^fieldname\d+$/', $user_nicename)
				)
				{
					$user_nicename = 'user_nicename = $dexQuery( \'[name="'.$user_nicename.$sequence.'"]\' ),';
				}
			?>
			var <?php print $user_nicename?> user_login = $dexQuery( '[name="<?php print $user_login.$sequence; ?>"]' ),
				user_email = $dexQuery( '[name="<?php print $user_email.$sequence; ?>"]' );
			if(
				user_login.length &&
				user_email.length
			)
			{
				var data  = {
					'cpcff_validate_form_registration': <?php print $formid; ?>,
					'user_login':user_login.val(),
					'user_email':user_email.val(),
					'user_nicename' : (typeof user_nicename != 'undefined' && user_nicename.length) ? user_nicename.val() : ''
				};
				validation_rules['<?php print esc_js( $this->addonID); ?>'] = false;
				$dexQuery.ajax({
					type: "GET",
					url:  "<?php echo cp_calculatedfieldsf_get_site_url(); ?>",
					data: data,
					success: function(result){
						result = new String(result);
						if (result != 'true' && result != '1')
						{
							alert(result);
						}
						else
						{
							validation_rules['<?php print esc_js( $this->addonID); ?>'] = true;
							processing_form();
						}
					}
				});
			}
			<?php
			}
		} // End javascript_validation_code

		public function validate()
		{
			if(
				isset( $_REQUEST['cpcff_validate_form_registration'] ) &&
				( $formid = trim($_REQUEST['cpcff_validate_form_registration']) ) != ''
			)
			{
				$formid = intval(@$formid);
				$tmp = $this->_get_form_settings($formid);
				if($tmp['enabled'])
				{
					$settings = array(
						'fields' => array(
							'user_login' 	=> (isset($_REQUEST['user_login'])) ? trim($_REQUEST['user_login']) : '',
							'user_email' 	=> (isset($_REQUEST['user_email'])) ? trim($_REQUEST['user_email']) : '',
							'user_nicename' => (isset($_REQUEST['user_nicename'])) ? trim($_REQUEST['user_nicename']) : ''
						),
						'messages' => (!empty($tmp['messages'])) ? $tmp['messages'] : array()
					);
					print $this->_is_valid($settings,true);
					exit;
				}
			}
		} // End validate

		/**
		 *	Clone the form's row
		 */
		public function clone_form( $original_form_id, $new_form_id )
		{
			global $wpdb;

			$form_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->db_table." WHERE formid=%d", $original_form_id ), ARRAY_A);

			if(!empty($form_row))
			{
				$form_row["formid"] = $new_form_id;
				$wpdb->insert( $wpdb->prefix.$this->db_table, $form_row);
			}
		} // End clone_form

		/**
		 *	It is called when the form is exported to export the addons data too.
		 *  Receive an array with the other addons data, and the form's id for filtering.
		 */
		public function export_form($addons_array, $formid)
		{
			global $wpdb;
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->db_table." WHERE formid=%d", $formid ), ARRAY_A );
			if(!empty( $row ))
			{
				unset($row['formid']);
				$addons_array[ $this->addonID ] = $row;
			}
			return $addons_array;
		} // End export_form

		/**
		 *	It is called when the form is imported to import the addons data too.
		 *  Receive an array with all the addons data, and the new form's id.
		 */
		public function import_form($addons_array, $formid)
		{
			global $wpdb;
			if(isset($addons_array[$this->addonID]))
			{
				$addons_array[$this->addonID]['formid'] = $formid;
				$wpdb->insert(
					$wpdb->prefix.$this->db_table,
					$addons_array[$this->addonID]
				);
			}
		} // End import_form

    } // End Class

    // Main add-on code
    $cpcff_registrationform_obj = new CPCFF_RegistrationForm();

	// Add addon object to the objects list
	global $cpcff_addons_objs_list;
	$cpcff_addons_objs_list[ $cpcff_registrationform_obj->get_addon_id() ] = $cpcff_registrationform_obj;
}
?>