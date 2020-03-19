<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'CPCFF_Users' ) )
{
    class CPCFF_Users extends CPCFF_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-users-20151013";
		protected $name = "CFF - Users Permissions";
		protected $description;

		public function get_addon_form_settings( $form_id )
		{
			if( isset( $_REQUEST[ 'cpcff_user_registered' ] ) )
			{
				// Save the addon settings
				$settings = array(
					'registered' => ( $_REQUEST[ 'cpcff_user_registered' ] == 1 ) ? 1 : 0,
					'unique'	 => ( empty( $_REQUEST[ 'cpcff_user_unique' ] ) ) ? 0 : 1,
					'messages'	 => array(
						'unique_mssg' 		=> stripcslashes( $_REQUEST[ 'cpcff_user_messages' ][ 'unique_mssg' ] ),
						'privilege_mssg' 	=> stripcslashes( $_REQUEST[ 'cpcff_user_messages' ][ 'privilege_mssg' ] )
					),
					'user_ids'	 => ( !empty($_REQUEST[ 'cpcff_user_ids' ]) ) ? $_REQUEST[ 'cpcff_user_ids' ] : array(),
					'user_roles' => ( !empty($_REQUEST[ 'cpcff_user_roles' ]) ) ? $_REQUEST[ 'cpcff_user_roles' ] : array(),
					'actions'    => array(
						'delete' => ( !empty( $_REQUEST[ 'cpcff_user_actions' ] ) && !empty( $_REQUEST[ 'cpcff_user_actions' ][ 'delete' ] ) ) ? 1 : 0,
						'edit' 	 => ( !empty( $_REQUEST[ 'cpcff_user_actions' ] ) && !empty( $_REQUEST[ 'cpcff_user_actions' ][ 'edit' ] ) ) ? 1 : 0
					),
  					'summary' => stripcslashes( trim( $_REQUEST[ 'cpcff_user_summary' ] ) )
				);
				update_option( $this->var_name.'_'.$form_id, $settings );
			}
			else
			{
				$settings = $this->get_form_settings( $form_id, array() );
				if( empty( $settings ) )
				{
					$settings = array(
						'registered' => false,
						'unique'	 => false,
						'messages'	 => array(
							'unique_mssg' 		=> "The form can be submitted only one time by user",
							'privilege_mssg' 	=> "You don't have sufficient privileges to access the form"
						),
						'user_ids'	 => array(),
						'user_roles' => array(),
						'actions'    => array(
							'delete' => 1,
							'edit' 	 => 1
						),
						'summary' => ''
					);
				}
			}
			?>
			<div id="metabox_basic_settings" class="postbox" >
				<h3 class='hndle' style="padding:5px;"><span><?php print $this->name; ?></span></h3>
				<div class="inside">
					<table cellspacing="0">
						<tr>
							<td style="white-space:nowrap;width:200px; vertical-align:top;font-weight:bold;"><?php _e('Display the form for', 'calculated-fields-form');?>:</td>
							<td>
								<input type="radio" name="cpcff_user_registered" value="1" <?php if( !empty( $settings[ 'registered' ] ) ) print 'CHECKED'; ?> /> <?php _e( 'Registered users only', 'calculated-fields-form' ); ?><br />
								<input type="radio" name="cpcff_user_registered" value="0" <?php if( empty( $settings[ 'registered'  ] ) ) print 'CHECKED'; ?> /> <?php _e( 'Anonymouse users', 'calculated-fields-form' ); ?>
							</td>
						</tr>
					</table>
					<h3><?php _e( 'For registered users only', 'calculated-fields-form' ); ?></h3>
					<table cellspacing="0">
						<tr>
							<td style="white-space:nowrap;width:200px;vertical-align:top;font-weight:bold;"><?php _e( 'The form may be submitted', 'calculated-fields-form' ); ?>:</td>
							<td>
								<input type="checkbox" name="cpcff_user_unique" value="1" <?php if( !empty( $settings[ 'unique' ] ) ) print 'CHECKED'; ?> /> <?php _e( 'only one time by user' );?>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="padding-top:20px;"><strong><?php _e('The form will be available only for users with the roles', 'calculated-fields-form');?>:</strong></td>
						</tr>
						<tr>
							<td style="white-space:nowrap;width:200px;vertical-align:top;font-weight:bold;"><?php _e( 'Roles', 'calculated-fields-form' ); ?>:</td>
							<td>
								<select MULTIPLE name="cpcff_user_roles[]"  style="min-width:350px;">
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
											!empty( $settings[ 'user_roles' ] ) &&
											is_array( $settings[ 'user_roles' ] ) &&
											in_array( $_role_value, $settings[ 'user_roles' ] )
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
						<tr>
							<td colspan="2" style="padding-top:20px;">
								<strong><?php _e('Or for the specific users', 'calculated-fields-form');?>:</strong><br />
								<em><?php _e("The forms are always available for the website's administrators",'calculated-fields-form'); ?></em>
							</td>
						</tr>
						<tr>
							<td style="white-space:nowrap;width:200px;vertical-align:top;font-weight:bold;"><?php _e( 'Users', 'calculated-fields-form' ); ?>:</td>
							<td>
								<select MULTIPLE name="cpcff_user_ids[]" style="min-width:350px;">
								<?php
									// Get the users list
									$users = get_users( array( 'fields' => array( 'ID', 'display_name' ), 'orderby' => 'display_name' ) );

									foreach( $users as $_user )
									{
										$_selected = '';
										if(
											!empty( $settings[ 'user_ids' ] ) &&
											is_array( $settings[ 'user_ids' ] ) &&
											in_array( $_user->ID, $settings[ 'user_ids' ] )
										)
										{
											$_selected = 'SELECTED';
										}
										print '<option value="'.$_user->ID.'" '.$_selected.'>'.$_user->display_name.'</option>';
									}

								?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="padding-top:20px;">
								<strong><?php _e('Actions allowed over the forms submissions by the users', 'calculated-fields-form');?>:</strong><br />
								<?php _e('Uses the corresponding shortcodes to insert the forms submissions in the users profile', 'calculated-fields-form');?>
							</td>
						</tr>
						<tr>
							<td style="white-space:nowrap;width:200px;vertical-align:top;font-weight:bold;"><?php _e( 'Actions', 'calculated-fields-form' ); ?>:</td>
							<td>
								<input type="checkbox" name="cpcff_user_actions[edit]" value="1" <?php if( !empty( $settings[ 'actions' ] ) && !empty( $settings[ 'actions' ][ 'edit' ] ) ) print 'CHECKED'  ?> /> <?php _e('Edit the submitted data (Really is created a new entry, and the previous one is deactivated, but it is yet accessible for the administrators from the messages section)', 'calculated-fields-form'); ?><br />
								<input type="checkbox" name="cpcff_user_actions[delete]" value="1" <?php if( !empty( $settings[ 'actions' ] ) && !empty( $settings[ 'actions' ][ 'delete' ] ) ) print 'CHECKED'  ?> /> <?php _e('Delete the submitted data (The submissions are disabled. The submissions are deleted only from the messages section)', 'calculated-fields-form'); ?>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="padding-top:20px;">
								<strong><?php _e('Error messages', 'calculated-fields-form');?>:</strong><br />
								<?php _e('The messages are displayed instead of the form: if the user has no sufficient privileges, or if the form may be submitted only one time by registered user, and it has been submitted', 'calculated-fields-form');?>
							</td>
						</tr>
						<tr>
							<td style="white-space:nowrap;width:200px;vertical-align:top;font-weight:bold;"><?php _e( 'Messages', 'calculated-fields-form' ); ?>:</td>
							<td>
								<?php _e('The user has no sufficient privileges' );?>:<br />
								<textarea name="cpcff_user_messages[privilege_mssg]" cols="80" rows="6" ><?php if( !empty( $settings[ 'messages' ] ) && isset( $settings[ 'messages' ][ 'privilege_mssg' ] ) ) print esc_textarea( $settings[ 'messages' ][ 'privilege_mssg' ] ); ?></textarea><br />

								<?php _e('The user has no sufficient privileges' );?>:<br />
								<textarea name="cpcff_user_messages[unique_mssg]"  cols="80" rows="6" ><?php if( !empty( $settings[ 'messages' ] ) && isset( $settings[ 'messages' ][ 'unique_mssg' ] ) ) print esc_textarea( $settings[ 'messages' ][ 'unique_mssg' ] ); ?></textarea>
							</td>
						</tr>
					</table>
					<div>The add-on includes a new shortcode: <strong>[CP_CALCULATED_FIELDS_USER_SUBMISSIONS_LIST]</strong>, to display the list of submissions belonging to an user. If the shortcode is inserted without attributes, the list of submissions will include those entries associated to the logged user. This shortcode accepts two attributes: id, for the user's id, and login, for the username (the id attribute has precedence over the login), in whose case the addon will list the submissions of the user selected,  furthermore it is possible restrict the list to a specific form using the attribute: form="#", where # should be replaced by the form's id.</div>
					<table cellspacing="0">
						<tr>
							<td style="white-space:nowrap;width:200px; vertical-align:top;font-weight:bold;"><?php _e('Summary', 'calculated-fields-form');?>:</td>
							<td>
								<textarea name="cpcff_user_summary" cols="80" rows="6" ><?php if( !empty( $settings[ 'summary' ] ) ) print esc_textarea( $settings[ 'summary' ] ); ?></textarea><br />
								Used with the previous shortcode.
							</td>
						</tr>
					</table>
				</div>
			</div>
			<?php
		}

		/************************ ADDON CODE *****************************/
        /************************ ATTRIBUTES *****************************/

		private $var_name 			= 'cp_cff_addon_users';
		private $post_user_table 	= 'cp_calculated_fields_user_submission';
		private $events_by_page 	= 10;
		private $forms_settings		= array();

        /************************ CONSTRUCT *****************************/

        function __construct()
        {
			$this->description = __("The add-on allows restrict the form to: registered users, users with specific roles, or specific users. Furthermore, allows to associate the submitted information with the submitter, if it is a registered user", 'calculated-fields-form' );

            // Check if the plugin is active
			if( !$this->addon_is_active() ) return;

			// Check for the existence of the 'refresh_opener' parameter
			if( isset( $_REQUEST[ 'refresh_opener' ] ) )
			{
				?>
				<script>
					window.opener.location.reload();
					window.close();
				</script>
				<?php
				exit;
			}

			// Check if the submission is being edited
			add_action( 'init', array( &$this, 'edit_submission' ), 1 );

			// Insert the entry in the database users-submission
			add_action( 'cpcff_process_data', array( &$this, 'insert_update' ) );

			// Decides if includes the form or a message
			add_filter( 'cpcff_the_form', array( &$this, 'the_form' ), 10, 2 );

			// Replace the shortcode with the list of submissions
			add_shortcode( 'CP_CALCULATED_FIELDS_USER_SUBMISSIONS_LIST', array( &$this, 'replace_shortcode' ) );

			if( is_admin() )
			{
				// Deletes an user-submission entry if the administrator deletes it
				add_action( 'cpcff_delete_submission', array( &$this, 'delete' ), 10, 1 );

				/************************ MESSAGES & CSV SECTION ************************/

				// Insert new headers in the  messages section
				add_action( 'cpcff_messages_filters', array( &$this, 'messages_filters'), 10 );

				// Modifies the query for filtering messages to includes the users information
				add_filter( 'cpcff_messages_query', array( &$this, 'messages_query' ), 10, 1 );
				add_filter( 'cpcff_csv_query', array( &$this, 'messages_query' ), 10, 1 );

				// Insert new headers in the  messages section
				add_action( 'cpcff_messages_list_header', array( &$this, 'messages_header'), 10 );

				// Add the users data to the messages
				add_action( 'cpcff_message_row_data', array( &$this, 'messages_data'), 10, 1 );

				// Delete forms
				add_action( 'cpcff_delete_form', array(&$this, 'delete_form') );

				// Clone forms
				add_action( 'cpcff_clone_form', array(&$this, 'clone_form'), 10, 2 );

				// Export addon data
				add_action( 'cpcff_export_addons', array(&$this, 'export_form'), 10, 2 );

				// Import addon data
				add_action( 'cpcff_import_addons', array(&$this, 'import_form'), 10, 2 );
			}
        } // End __construct

        /************************ PROTECTED METHODS *****************************/

		/**
         * Creates the database tables
         */
        protected function update_database()
		{
			global $wpdb;
			$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix.$this->post_user_table." (
					submissionid INT NOT NULL,
					userid INT NOT NULL,
					active TINYINT(1) NOT NULL,
					PRIMARY KEY (userid,submissionid)
				)
				CHARACTER SET utf8
				COLLATE utf8_general_ci;";

			$wpdb->query($sql);
		} // End update_database

        /************************ PRIVATE METHODS *****************************/

		/**
		 * Get the forms settings. Checks if the form's settings has been read previously, or get the value from the options
		 * $form_id, integer with the form's id
		 * Returns the form's settings
		 */
		private function get_form_settings( $form_id, $default = false )
		{
			if( empty( $this->forms_settings[ $form_id ] ) )
			{
				$this->forms_settings[ $form_id ] = get_option( $this->var_name.'_'.$form_id, array() );
				if( empty( $this->forms_settings[ $form_id ] ) && $default !== false )
				{
					$this->forms_settings[ $form_id ] = $default;
				}
				elseif( empty( $this->forms_settings[ $form_id ][ 'actions' ] ) )
				{
					$this->forms_settings[ $form_id ][ 'actions' ] = array( 'delete' => false, 'edit' => false );
				}
			}
			return $this->forms_settings[ $form_id ];

		} // End get_form_settings

		/**
		 * Generates an HTML table with all the submissions
		 */
		private function user_messages_list( $events, $forms )
		{
			$cellstyle   = 'border:1px solid #F0F0F0;border-top:0;border-left:0;';
			$actionstyle = 'cursor:pointer;color:#00a0d2;';

			$str = '
			<div id="dex_printable_contents">
				<table cellspacing="0" style="border:0;" class="users-permissions-submissions-list">
					<thead style="padding-left:7px;font-weight:bold;white-space:nowrap;" class="the-header">
						<tr>
							<th  style="'.$cellstyle.'width:40px;">'.__( 'Id', 'calculated-fields-form' ).'</th>
							<th  style="'.$cellstyle.'">'.__( 'Form', 'calculated-fields-form' ).'</th>
							<th  style="'.$cellstyle.'">'.__( 'Date', 'calculated-fields-form' ).'</th>
							<th  style="'.$cellstyle.'border-right:0;">'.__( 'Options', 'calculated-fields-form' ).'</th>
						</tr>
					</thead>
					<tbody id="the-list">
			';

			for( $i = 0; $i < count( $events ); $i++ )
			{
				$this->get_form_settings( $events[ $i ]->formid );

				// Check if the submission will be deleted, and if the form has been configured to allow delete the submissions
				if(
					!empty( $_REQUEST[ 'cpcff_addon_user_delete' ] ) &&
					$_REQUEST[ 'cpcff_addon_user_delete' ] == $events[ $i ]->id &&
					!empty( $this->forms_settings[ $events[ $i ]->formid ] ) &&
					$this->forms_settings[ $events[ $i ]->formid ][ 'actions' ][ 'delete' ]
				)
				{
					$this->deactivate( $_REQUEST[ 'cpcff_addon_user_delete' ] );
					continue;
				}

				$str .= '
					<tr class="form-'.$events[ $i ]->formid.' row-1">
						<td style="'.$cellstyle.'font-weight:bold;">'.$events[$i]->id.'</td>
						<td style="'.$cellstyle.'">'.( ( !empty( $forms[ $events[ $i ]->formid ] ) ) ? $forms[ $events[ $i ]->formid ][ 'name' ] : '' ).'</td>
						<td style="'.$cellstyle.'">'.substr($events[$i]->time,0,16).'</td>
						<td style="'.$cellstyle.'border-right:0;white-space:nowrap;">
				';

				if(
					!empty( $this->forms_settings[ $events[ $i ]->formid ] )
				)
				{
					if( $this->forms_settings[ $events[ $i ]->formid ][ 'actions' ][ 'delete' ] )
					{
						$str .= '<span style="'.$actionstyle.'margin-right:5px;" onclick="cpcff_addon_user_deleteMessage('.$events[$i]->id.')">['.__( 'Delete', 'calculated-fields-form' ).']</span>';
					}

					if( $this->forms_settings[ $events[ $i ]->formid ][ 'actions' ][ 'edit' ] )
					{
						$str .= '<span style="'.$actionstyle.'" onclick="cpcff_addon_user_editMessage('.$events[$i]->id.')">['.__( 'Update', 'calculated-fields-form' ).']</span>';
					}
				}
				$str .= '
						</td>
					</tr>
					<tr class="form-'.$events[ $i ]->formid.' row-2">
						<td colspan="4" style="'.$cellstyle.'border-right:0;">';

				$paypal_post = @unserialize( $events[ $i ]->paypal_post );
				if(
					empty( $this->forms_settings[ $events[ $i ]->formid ][ 'summary' ] ) ||
					!function_exists( '_cp_calculatedfieldsf_replace_vars' ) ||
					$paypal_post == false ||
					empty( $forms[ $events[ $i ]->formid ] )
				)
				{
					$str .= str_replace( array( '\"', "\'", "\n" ), array( '"', "'", "<br />" ), $events[$i]->data );
					// Add links
					if( $paypal_post !== false )
					{
						foreach( $paypal_post as $_key => $_value )
						{
							if( strpos( $_key, '_url' ) )
							{
								if( is_array( $_value ) )
								{
									foreach( $_value as $_url )
									{
										$str .= '<p><a href="'.esc_attr( $_url ).'" target="_blank">'.$_url.'</a></p>';
									}
								}
							}
						}
					}
				}
				else
				{
					if( empty( $forms[ $events[ $i ]->formid ][ 'fields' ] ) )
					{
						$raw_form_str = cp_calculatedfieldsf_cleanJSON( $forms[ $events[ $i ]->formid ][ 'structure' ] );
						$form_data = json_decode( $raw_form_str );

						$fields = array();
						foreach($form_data[0] as $item)
						{
							$fields[$item->name] = $item;
						}
						$fields[ 'ipaddr' ] = $events[ $i ]->ipaddr;
						$forms[ $events[ $i ]->formid ][ 'fields' ] = $fields;
					}

					$replaced_values = _cp_calculatedfieldsf_replace_vars( $forms[ $events[ $i ]->formid ][ 'fields' ], $paypal_post, $this->forms_settings[ $events[ $i ]->formid ][ 'summary' ], $events[$i]->data, 'html',  $events[ $i ]->id ) ;

					$str .= $replaced_values[ 'message' ];
				}

				$str .= '
						</td>
					</tr>
				';
			}

			$str .= '
					</tbody>
				</table>
			</div>
			';

			// The javascript code
			$str .= '
				<script>
					function cpcff_addon_user_deleteMessage( submission )
					{
						if (confirm("'.esc_attr__( 'Do you want to delete the item?', 'calculated-fields-form' ).'"))
						{
							jQuery("#cpcff_addon_user_delete_form").remove();
							jQuery("body").append( "<form id=\'cpcff_addon_user_delete_form\' method=\'POST\'><input type=\'hidden\' name=\'cpcff_addon_user_delete\' value=\'"+submission+"\'></form>" );
							jQuery("#cpcff_addon_user_delete_form").submit();
						}
					}
					function cpcff_addon_user_editMessage( submission )
					{
						var w = screen.width*0.8,
							h = screen.height*0.7,
							l = screen.width/2 - w/2,
							t = screen.height/2 - h/2,
							new_window = window.open("", "formpopup", "resizeable,scrollbars,width="+w+",height="+h+",left="+l+",top="+t);

						jQuery("#cpcff_addon_user_edit_form").remove();
						jQuery("body").append( "<form id=\'cpcff_addon_user_edit_form\' method=\'POST\'  target=\'formpopup\'><input type=\'hidden\' name=\'cpcff_addon_user_edit\' value=\'"+submission+"\'></form>" );
						jQuery("#cpcff_addon_user_edit_form").submit();
					}
				</script>
			';
			return $str;
		} // End user_messages_list

		private function edit_submission_aux( $submission_id, $with_form = 1 )
		{
			// Edit submission. Checks if the submission belongs to the user, and if the user can edit it.
			global $wpdb;
			$str = '';

			// Get logged user
			$user_obj = wp_get_current_user();

			if( $user_obj->ID != 0 )
			{
				if( in_array( 'administrator',  $user_obj->roles ) )
				{
					// Get the form's id
					$form_data = $wpdb->get_row( $wpdb->prepare( "SELECT formid, paypal_post FROM ".CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME." WHERE id=%d", $submission_id ) );
				}
				else
				{
					// Get the form id if exists and the submission belongs to the user
					$form_data = $wpdb->get_row( $wpdb->prepare( "SELECT submission.formid, submission.paypal_post FROM ".$wpdb->prefix.$this->post_user_table." as submission_user, ".CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME." as submission WHERE submission_user.submissionid=%d AND submission_user.userid=%d AND submission.id=submission_user.submissionid AND submission_user.active=1", array( $submission_id, $user_obj->ID ) ) );
				}

				if( !is_null( $form_data ) )
				{

					$form_id = $form_data->formid;
					$submission_data = unserialize( $form_data->paypal_post );

					// Checks if the user can edit the submitted data.
					$this->get_form_settings( $form_id );
					if( $this->forms_settings[ $form_id ][ 'actions' ][ 'edit' ] )
					{
						$_form_index = 1;
						// Get the submitted data and generate a JSON object
						$str .= '<script>if(typeof cpcff_default == "undefined") cpcff_default = {};
						cpcff_default['.$_form_index.'] = '.json_encode( $submission_data ).';
						</script>';
						if( $with_form )
						{
							$html_content = cp_calculatedfieldsf_filter_content( array( 'id' => $form_id ) );
							$str .= $html_content;
						}
					}
				}
			}

			return $str;

		} // End edit_submission_aux
		/************************ PUBLIC METHODS  *****************************/

		/**
		 * Checks if the submission is being edited,
		 * if it corresponds to the logged user,
		 * and if the edition action is associated to the form.
		 * Finally, displays the form with the submissions data.
		 */
		public function edit_submission()
		{
			// Edit submission. Checks if the submission belongs to the user, and if the user can edit it.
			if( isset( $_REQUEST[ 'cpcff_addon_user_edit' ] ) )
			{
				$submission_id = intval( trim( @$_REQUEST[ 'cpcff_addon_user_edit' ] ) );
				$str = $this->edit_submission_aux( $submission_id );
				if( !empty( $str ) )
				{
					print '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>';
					$str = preg_replace( '/<\/form>/i', '<input type="hidden" name="cpcff_submission_id" value="'.$submission_id.'"><input type="hidden" name="cpcff_refresh_list" value="1" /></form>', $str );
					print $str;
					wp_footer();
					print '</body></html>';
					exit;
				}
			}
		} // End edit_submission

		/**
		 * Checks the settings, and decides if display the form or the message
		 * $html_content, the HTML code of form, styles and scripts if corresponds
		 * $form_id, integer number_format
		 *
		 * Returns the same $html_content, or a message if the form is not available
		 */
		public function the_form( $html_content, $form_id )
		{
			global $wpdb;

			$settings = $this->get_form_settings( $form_id );
			if( !empty( $settings[ 'registered' ] ) )
			{

				$user_obj = wp_get_current_user();

				if( $user_obj->ID == 0 )
				{
					$error_mssg = 'privilege_mssg';
				}
				else
				{
					$roles = $user_obj->roles;

					// The current user is an administrator
					if( in_array( 'administrator', $roles ) )
					{
						return $html_content;
					}

					if(
						!empty( $settings[ 'unique' ] ) &&
						( $submission_id = intval( @$wpdb->get_var( $wpdb->prepare( 'SELECT addon.submissionid FROM '.$wpdb->prefix.$this->post_user_table.' as addon, '.CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME.' as submissions WHERE addon.userid=%d AND addon.submissionid=submissions.id AND submissions.formid=%d ORDER BY addon.submissionid DESC', array( $user_obj->ID, $form_id ) ) ) ) ) !== 0
					)
					{
						// Check if the user has permissions for edition and insert the pre-populated form
						if( $settings[ 'actions' ][ 'edit' ] )
						{
							$str  = $this->edit_submission_aux( $submission_id, 0 );
							$html_content = preg_replace( '/<\/form>/i', '<input type="hidden" name="cpcff_submission_id" value="'.$submission_id.'" /></form>', $html_content );
							return $html_content.$str;
						}
						else
						{
							$error_mssg = 'unique_mssg';
						}
					}
					else
					{
						// The form is restricted by roles and the current user has at least one of them
						if(
							!empty( $settings[ 'user_roles' ] ) &&
							count( array_intersect( $settings[ 'user_roles' ], $roles ) )
						)
						{
							return $html_content;
						}

						// The form is restricted by users and the current user is in the list
						if(
							!empty( $settings[ 'user_ids' ] ) &&
							in_array( $user_obj->ID, $settings[ 'user_ids' ] )
						)
						{
							return $html_content;
						}

						// The form is restricted by users or roles and the current user does not satisfy the conditions
						if(
							!empty( $settings[ 'user_ids' ] ) ||
							!empty( $settings[ 'user_roles' ] )
						)
						{
							$error_mssg = 'privilege_mssg';
						}
					}
				}

				if( !empty( $error_mssg ) )
				{
					return ( !empty( $settings[ 'messages' ] ) && !empty( $settings[ 'messages' ][ $error_mssg ] ) ) ? $settings[ 'messages' ][ $error_mssg ] : '';
				}
			}
			return $html_content;
		} // End the_form

		/**
		 * Used to modify the URL of the thank you page if the submission is being edited
		 */
		public function get_option( $value, $field )
		{
			if( $field == 'fp_return_page' )
			{
				$value .= ( ( strpos( $value, '?' ) === false ) ? '?' : '&' ).'refresh_opener=1';
			}
			return $value;
		} // End get_option

		/**
         * Associate the submitted information to the user
         */
        public function	insert_update( $params )
		{
			global $wpdb;
			if( isset( $params[ 'itemnumber' ] ) )
			{
				$user_obj = wp_get_current_user();
				// Option available only for logged users
				if( $user_obj->ID != 0 )
				{
					$user_id = $user_obj->ID;

					if( isset( $_REQUEST[ 'cpcff_submission_id' ] ) )
					{
						$user_submission = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM '.$wpdb->prefix.$this->post_user_table.' WHERE submissionid=%d', $_REQUEST[ 'cpcff_submission_id' ] ) );

						// If the submission is being edited the orginal submission is deactivated
						if(
							!empty( $user_submission ) &&
							( in_array( 'administrator',  $user_obj->roles ) || $user_obj->ID == $user_submission->userid )
						)
						{
							$user_id = $user_submission->userid;
							$wpdb->update(
								$wpdb->prefix.$this->post_user_table,
								array( 'active' => 0 ),
								array( 'submissionid' => $_REQUEST[ 'cpcff_submission_id' ] ),
								'%d', '%d'
							);
						}

						if( !empty( $_REQUEST[ 'cpcff_refresh_list' ] ) )
						{
							// Add a filter hook to modify the URL to the thank you page
							add_filter( 'cpcff_get_option', array( &$this, 'get_option' ), 10, 2 );
						}
					}

					@$wpdb->insert(
						$wpdb->prefix.$this->post_user_table,
						array( 'submissionid' => $params[ 'itemnumber' ], 'userid' => $user_id, 'active' => 1 ),
						array( '%d', '%d',  '%d')
					);
				}
			}
		} // End insert

		/**
         * Deactivate an user-submission entry
         */
        public function	deactivate( $submission_id )
		{
			global $wpdb;
			@$wpdb->update(
				$wpdb->prefix.$this->post_user_table,
				array( 'active' => 0),
				array( 'submissionid' => $submission_id),
				'%d',
				'%d'
			);
		} // End deactivate

		/**
         * Delete an user-submission entry
         */
        public function	delete( $submission_id )
		{
			global $wpdb;
			@$wpdb->delete(
				$wpdb->prefix.$this->post_user_table,
				array( 'submissionid' => $submission_id),
				'%d'
			);
		} // End delete

		/**
		 * Replaces the shorcode to display the list of submission related with an user
		 */
		public function replace_shortcode( $atts )
		{
			if( !empty( $atts[ 'id' ] ) || !empty( $atts[ 'login' ] ) )
			{
				if(
					!empty( $atts[ 'id' ] ) &&
					( $_user_id = intval( @$atts[ 'id' ] ) ) !== 0 &&
					get_user_by( 'ID', $_user_id ) !== false
				)
				{
					$user_id = $_user_id;
				}
				elseif(
					!empty( $atts[ 'login' ] ) &&
					( $_user_obj = get_user_by( 'login', trim( $atts[ 'login' ] ) ) ) !== false
				)
				{
					$user_id = $_user_obj->ID;
				}
			}
			else
			{
				$user_id = get_current_user_id();
			}

			if( !empty( $user_id ) )
			{
				global $wpdb;
				$formid = (!empty($atts[ 'form' ])) ? intval(@($atts[ 'form' ])) : 0;
				$current_page = 0;
				if( isset( $_GET[ 'events_page' ] ) )
				{
					$current_page = @intval( $_GET[ 'events_page' ] );
					unset( $_GET[ 'events_page' ] );
				}
				$events_by_page = max( $this->events_by_page, 1 );

				$events = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT SQL_CALC_FOUND_ROWS * FROM ".CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME." as submission, ".$wpdb->prefix.$this->post_user_table." as user_submission WHERE ".((!empty($formid)) ? "submission.formid=".$formid." AND " : "")." submission.id=user_submission.submissionid AND user_submission.userid=%d AND user_submission.active=1 ORDER BY `time` DESC LIMIT %d,%d",
						$user_id,
						max($current_page - 1, 0 )*$events_by_page,
						$events_by_page
					)
				);

				// Get total records for pagination
				$total = $wpdb->get_var( "SELECT FOUND_ROWS()" );
				$total_pages = ceil($total/$events_by_page);

				if( $total )
				{
					$_forms = $wpdb->get_results( "SELECT id,form_name, form_structure FROM ".$wpdb->prefix.CP_CALCULATEDFIELDSF_FORMS_TABLE." AS formsettings".((!empty($formid)) ? " WHERE formsettings.id=".$formid : "") );
					$forms = array();
					foreach($_forms as $_form )
					{
						$forms[ $_form->id ] = array( 'name' => $_form->form_name, 'structure' => $_form->form_structure );
					}

					$_GET[ 'events_page' ] = '%_%';
					$base_url = str_replace( '%25', '%', $_SERVER[ 'HTTP_HOST' ] . preg_replace( '/\\?.*/', '', $_SERVER[ 'REQUEST_URI' ] ).'?'.http_build_query( $_GET ) );
					$page_links = paginate_links(
									array(
										'base'         	=> $base_url,
										'format'       	=> '%#%',
										'total'        	=> $total_pages,
										'current'      	=> $current_page,
										'show_all'     	=> True,
										'end_size'     	=> 1,
										'mid_size'     	=> 2,
										'prev_next'    	=> True,
										'prev_text'    	=> __('&laquo; Previous'),
										'next_text'    	=> __('Next &raquo;'),
										'type'         	=> 'plain',
										'add_args'     => False
									)
								);

					return 	$page_links.$this->user_messages_list( $events, $forms ) .$page_links;
				}
				else
				{
					return '<div>'.__( 'The list of submissions is empty', 'calculated-fields-form' ).'</div>';
				}
			}
			else
			{
				return '';
			}
		} // End replace_shortcode

		/************************ MESSAGES & CSV SECTION ************************/

		/**
         * Modifies the query of messages for including the information of users
         */
        public function	messages_query( $query )
		{
			global $wpdb;

			if( preg_match( '/DISTINCT/i', $query ) == 0 )
			{
				$query = preg_replace( '/SELECT/i', 'SELECT DISTINCT ', $query );
			}

			$query = preg_replace( '/WHERE/i', ' LEFT JOIN ('.$wpdb->prefix.$this->post_user_table.' as user_submission LEFT JOIN '.$wpdb->users.' as user ON user_submission.userid=user.ID) ON user_submission.submissionid='.CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME.'.id WHERE', $query );

			if(
				!empty( $_REQUEST[ 'cpcff_addon_user_username' ] ) &&
				($username = trim($_REQUEST[ 'cpcff_addon_user_username' ])) !== ''
			)
			{
				$username = '%'.$username.'%';
				$query = preg_replace(
					'/WHERE/i',
					$wpdb->prepare(
						'WHERE (user.user_login LIKE %s OR user.user_nicename LIKE %s) AND ',
						array( $username, $username )
					),
					$query
				);
			}
			return $query;
		} // End messages_query

		/**
         * Print new <TH> tags for the header section for the table of messages.
         */
        public function	messages_header()
		{
			print '<TH style="padding-left:7px;font-weight:bold;">'.__( 'Registered User', 'calculated-fields-form' ).'</TH>';
		} // End messages_header

		/**
         * Print new <TD> tags with the users data in the table of messages.
         */
        public function	messages_data( $data )
		{
			$str = '';
			$data = (array)$data;
			if( !empty( $data[ 'userid' ] ) )
			{
				$str = '<a href="'.get_edit_user_link( $data[ 'userid' ] ).'" target="_blank">'.$data[ 'display_name' ].'</a>';
			}
			print '<TD>'.$str.'</TD>';
		} // End messages_data

		/**
         * Includes new fields for filtering in the messages section
         */
        public function	messages_filters()
		{
			print '<div style="display:inline-block; white-space:nowrap; margin-right:20px;">'.__( 'Username', 'calculated-fields-form' ).': <input type="text" id="cpcff_addon_user_username" name="cpcff_addon_user_username" value="'.esc_attr( ( !empty( $_REQUEST[ 'cpcff_addon_user_username' ] ) ) ? $_REQUEST[ 'cpcff_addon_user_username' ] : '' ).'" /></div>';

		} // End messages_filters

		/**
		 *	Delete the form from the addon's table
		 */
        public function delete_form($formid)
		{
			delete_option( $this->var_name.'_'.$formid );
		} // delete_form

		/**
		 *	Clone the form's row
		 */
		public function clone_form( $original_form_id, $new_form_id )
		{
			global $wpdb;
			$settings = $this->get_form_settings($original_form_id);
			if(!empty($settings))
			{
				update_option( $this->var_name.'_'.$new_form_id, $settings );
			}
		} // End clone_form

		/**
		 *	It is called when the form is exported to export the addons data too.
		 *  Receive an array with the other addons data, and the form's id for filtering.
		 */
		public function export_form($addons_array, $formid)
		{
			$settings = $this->get_form_settings($formid);
			if(!empty($settings))
			{
				$addons_array[ $this->addonID ] = $settings;
			}
			return $addons_array;
		} // End export_form

		/**
		 *	It is called when the form is imported to import the addons data too.
		 *  Receive an array with all the addons data, and the new form's id.
		 */
		public function import_form($addons_array, $formid)
		{
			if(isset($addons_array[$this->addonID]))
				update_option( $this->var_name.'_'.$formid, $addons_array[$this->addonID] );

		} // End import_form

    } // End Class

    // Main add-on code
    $cpcff_users_obj = new CPCFF_Users();

	// Add addon object to the objects list
	global $cpcff_addons_objs_list;
	$cpcff_addons_objs_list[ $cpcff_users_obj->get_addon_id() ] = $cpcff_users_obj;
}
?>