<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'CPCFF_WooCommerce' ) )
{
    class CPCFF_WooCommerce extends CPCFF_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-woocommerce-20150309";
		protected $name = "CFF - WooCommerce";
		protected $description;

		/************************ ADDON CODE *****************************/
        /************************ ATTRIBUTES *****************************/

        private $form = array(); // Form data
        private $first_time = true; // Control attribute to avoid read multiple times the form associated to the product

        /************************ CONSTRUCT *****************************/

        function __construct()
        {
			$this->description = __("The add-on allows integrate the forms with WooCommerce products", 'calculated-fields-form');

            // Check if the plugin is active
			if( !$this->addon_is_active() ) return;

			// Check if WooCommerce is active in the website
            $active_plugins = (array) get_option( 'active_plugins', array() );

            if ( is_multisite() )
            {
                $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
            }

            if( !( in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) ) )
            {
                return;
            }

            // Load resources, css and js
            add_action( 'woocommerce_before_single_product', array( &$this, 'enqueue_scripts' ), 10 );
            add_action( 'woocommerce_before_cart', array( &$this, 'enqueue_cart_resources' ), 10 );
            add_action( 'woocommerce_before_checkout_form', array( &$this, 'enqueue_cart_resources' ), 10 );

			// Addon display
            add_action('woocommerce_before_add_to_cart_button', array(&$this, 'display_form'), 10);

            // Corrects the form options
            add_filter( 'cpcff_get_option', array( &$this, 'get_form_options' ), 10, 3 );

            // Filters for cart actions
			add_filter('woocommerce_add_cart_item_data', array(&$this, 'add_cart_item_data'), 10, 2);
			add_filter('woocommerce_get_item_data', array(&$this, 'get_cart_item_data'), 10, 2);
			add_filter('woocommerce_get_cart_item_from_session', array(&$this, 'get_cart_item_from_session'), 10, 2);
            add_filter('woocommerce_add_cart_item', array(&$this, 'add_cart_item'), 10, 1);
			add_action('woocommerce_add_order_item_meta', array(&$this, 'add_order_item_meta'), 10, 3);
			// add_filter('woocommerce_order_get_items', array(&$this, 'get_order_items'), 10 );

            add_filter('woocommerce_after_order_itemmeta', array(&$this, 'extra_order_item_details'), 10 );
            add_filter('woocommerce_order_status_completed', array(&$this, 'order_status_completed'), 10 );

            // Filters for the Calculated Fields Form
            add_action( 'cpcff_redirect', array( &$this, 'cpcff_redirect'), 10 );

			// The init hook
			add_action( 'admin_init', array( &$this, 'init_hook' ), 1 );

			if( is_admin() )
			{
				// Delete forms
				add_action( 'cpcff_delete_form', array(&$this, 'delete_form') );
			}

        } // End __construct

        /************************ PRIVATE METHODS *****************************/
        /**
         * Check if the add-on can be applied to the product
         */
        private function apply_addon( $id = false )
        {
            global $post;

            $this->form = array();

            if( $id ) $post_id = $id;
            elseif( isset( $_REQUEST[ 'woocommerce_cpcff_product' ] ) ) $post_id = $_REQUEST[ 'woocommerce_cpcff_product' ];
            elseif( isset( $post ) ) $post_id = $post->ID;

            if( isset( $post_id ) )
            {
                $tmp = get_post_meta( $post_id, 'woocommerce_cpcff_form', true );
                if( !empty( $tmp ) ) $this->form[ 'id' ] = $tmp;
            }

            return !empty( $this->form );

        }

		/************************ PUBLIC METHODS  *****************************/

		public function add_cart_item_data( $cart_item_meta, $product_id ) {
			if(
				!isset( $cart_item_meta[ 'cp_cff_form_data' ] ) &&
				( $cp_cff_form_data = CP_SESSION::get_var( 'cp_cff_form_data' ) ) !== false
			)
            {
                $cart_item_meta[ 'cp_cff_form_data' ] = $cp_cff_form_data;
            }
            return $cart_item_meta;

        } // End add_cart_item_data

        public function get_cart_item_from_session( $cart_item, $values ) {
			if( isset( $values[ 'cp_cff_form_data' ] ) ) {
				$cart_item['cp_cff_form_data'] = $values['cp_cff_form_data'];
                $this->add_cart_item( $cart_item );
			}
			return $cart_item;

		} // End get_cart_item_from_session

		function get_cart_item_data( $values, $cart_item ) {
			global $wpdb;

			// Adjust price if required based in the cpcff_data
			if( isset($cart_item[ 'cp_cff_form_data' ] ) )
            {
                $data_id = $cart_item[ 'cp_cff_form_data' ];
                if( !empty( $data_id ) )
                {
					$data = $wpdb->get_var( $wpdb->prepare( "SELECT data FROM ".CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME." WHERE id=%d", $data_id ) );
					$activate_summary = get_post_meta( $cart_item[ 'product_id' ], 'woocommerce_cpcff_activate_summary', true );
					if( !empty( $activate_summary ) && function_exists( 'cp_calculatedfieldsf_form_result' ) )
					{
						$summary_title = get_post_meta( $cart_item[ 'product_id' ], 'woocommerce_cpcff_summary_title', true );
						if( empty( $summary_title ) ) $summary_title = '';

						$summary = get_post_meta( $cart_item[ 'product_id' ], 'woocommerce_cpcff_summary', true );
						if( empty( $summary ) ) $summary = '<%INFO%>';
						elseif($summary !== strip_tags($summary)) $summary = str_replace(array("\n","\r"), '', $summary);

						$result = (cp_calculatedfieldsf_form_result( array(), $summary, $data_id));
						$values[] = array( 'name' => ( ( !empty( $summary_title ) ) ? $summary_title : '' ) , 'value' => $result );
					}
					else
					{
						$data = preg_replace( array( "/\n+/", "/:+\s*/" ), array( "\n", ":" ), $data );
						$data_arr = explode( "\n", $data );
						foreach( $data_arr as $data_item )
						{
							if( !empty( $data_item ) )
							{
								$data_item = explode( ":", $data_item );
								if( count($data_item) == 2 )
								{
									$values[] = array(
													'name' 	=> stripcslashes( $data_item[ 0 ] ),
													'value' => stripcslashes( $data_item[ 1 ] )
												);
								}
							}
						}
					}
				}
            }
			CP_SESSION::unset_var( 'cp_cff_form_data' );
			return $values;
        } // End add_cart_item

        //Helper function, used when an item is added to the cart as well as when an item is restored from session.
		function add_cart_item( $cart_item ) {
			global $wpdb;

			// Adjust price if required based in the cpcff_data
			if( isset($cart_item[ 'cp_cff_form_data' ] ) )
            {
				// Modify the hyperlink of name product name and thumbnail in the cart page.
				add_filter( 'woocommerce_cart_item_permalink', array( &$this, 'woocommerce_cart_item_permalink' ), 10, 3 );

                $tmp = get_post_meta( $cart_item[ 'product_id' ], 'woocommerce_cpcff_calculate_price', true );
                if( !empty( $tmp ) )
                {
					$minimum_price = get_post_meta( $cart_item[ 'product_id' ], 'woocommerce_cpcff_minimum_price', true );
					$weight_field = get_post_meta( $cart_item[ 'product_id' ], 'woocommerce_cpcff_weight_field', true );
					$length_field = get_post_meta( $cart_item[ 'product_id' ], 'woocommerce_cpcff_length_field', true );
					$width_field = get_post_meta( $cart_item[ 'product_id' ], 'woocommerce_cpcff_width_field', true );
					$height_field = get_post_meta( $cart_item[ 'product_id' ], 'woocommerce_cpcff_height_field', true );

                    $data_id = $cart_item[ 'cp_cff_form_data' ];
                    $data = $wpdb->get_var( $wpdb->prepare( "SELECT paypal_post FROM ".CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME." WHERE id=%d", $data_id ) );
                    $paypal_data = unserialize( $data );
                    $price = preg_replace( '/[^\d\.\,]/', '', $paypal_data[ 'final_price' ] );
					$price = (!empty($minimum_price)) ? max($price, $minimum_price) : $price;
                    $cart_item[ 'data' ]->set_price($price);

					if(
						!empty($weight_field)
					)
					{
						$weight_field = trim($weight_field);
						if(isset($paypal_data[$weight_field]))
						{
							$weight = $paypal_data[$weight_field];
							$weight = preg_replace('/[^\d\.]/','',$weight);
							$cart_item[ 'data' ]->set_weight(floatval(@$weight));
						}
					}

					if(
						!empty($length_field)
					)
					{
						$length_field = trim($length_field);
						if(isset($paypal_data[$length_field]))
						{
							$length = $paypal_data[$length_field];
							$length = preg_replace('/[^\d\.]/','',$length);
							$cart_item[ 'data' ]->set_length(floatval(@$length));
						}
					}

					if(
						!empty($height_field)
					)
					{
						$height_field = trim($height_field);
						if(isset($paypal_data[$height_field]))
						{
							$height = $paypal_data[$height_field];
							$height = preg_replace('/[^\d\.]/','',$height);
							$cart_item[ 'data' ]->set_height(floatval(@$height));
						}
					}

					if(
						!empty($width_field)
					)
					{
						$width_field = trim($width_field);
						if(isset($paypal_data[$width_field]))
						{
							$width = $paypal_data[$width_field];
							$width = preg_replace('/[^\d\.]/','',$width);
							$cart_item[ 'data' ]->set_width(floatval(@$width));
						}
					}

					/** Modifies the prices defined by FANCY PRODUCT DESIGNER **/
					if(isset( $cart_item['fpd_data']) && isset( $cart_item['fpd_data']['fpd_product_price']))
						$cart_item['fpd_data']['fpd_product_price'] = $price;

					if( property_exists( $cart_item[ 'data' ], 'regular_price') )
						$cart_item[ 'data' ]->regular_price = $price;
					elseif( method_exists($cart_item[ 'data' ], 'set_regular_price') )
						$cart_item[ 'data' ]->set_regular_price($price);
				}
            }
            return $cart_item;

		} // End add_cart_item

		function woocommerce_cart_item_permalink( $permalink, $cart_item, $cart_item_key )
		{
			$add = '';
			if( !empty( $cart_item[ 'cp_cff_form_data' ] ) )
			{
				$add = ((strpos($permalink, '?') === false ) ? '?' : '&' ).'cp_cff_wc='.$cart_item_key;
			}
			return $permalink.$add;
		} // End woocommerce_cart_item_permalink

        /**
         * Avoid redirect the Calculated Fields Form to the thanks page.
         */
        function cpcff_redirect()
        {
			if( isset( $_REQUEST[ 'product' ] ) || isset( $_REQUEST[ 'woocommerce_cpcff_product' ] ) ) return false;
            return true;
        }

        public function get_order_items( $data )
		{
			foreach( $data as $k => $d )
			{
				if( isset( $d[ 'item_meta_array' ] ) )
				{
					foreach( $d[ 'item_meta_array' ] as $k1 => $d1 )
					{
						if( $d1->key == __( 'Data' ) )
						{
							$data[ $k ][ 'item_meta_array' ][ $k1 ]->value = strip_tags( preg_replace( '/\\s+\\-\\s+$/', '', str_replace('<br />', ' - ', $d1->value ) ) );
						}
					}
				}
			}

			return $data;
		} // End get_order_items

		public function order_status_completed( $id )
		{
			$order = new WC_Order( $id );
			$items = $order->get_items();
			foreach( $items as $item_id => $item )
			{
				$extra_details = get_post_meta( $item_id, 'woocommerce_cpcff_order_details', true );
				if( !empty( $extra_details ) && !empty( $extra_details[ 'cff_params' ] ) )
				{
					/**
					 * Action called after process the data received by PayPal.
					 * To the function is passed an array with the data collected by the form.
					 */
					do_action( 'cpcff_payment_processed', $extra_details[ 'cff_params' ] );
				}
			}

		} // End order_status_completed

		/**
		 * Includes extra details in the order items
		 */
		public function extra_order_item_details( $item_id )
		{
			$extra_details = get_post_meta( $item_id, 'woocommerce_cpcff_order_details', true );

			if( empty( $extra_details ) || empty( $extra_details[ 'data' ] )) return;
			?>
			<div class="order_data_column">
				<h4><?php _e( 'Extra Details' ); ?></h4>
				<div><?php echo $extra_details[ 'data' ]; ?></div>
			</div>
            <?php
		} // End extra_order_item_details

		public function add_order_item_meta( $item_id, $values, $cart_item_key )
        {
	        global $wpdb;
            $data_id = $values[ 'cp_cff_form_data' ];

            if( $this->apply_addon( $values[ 'data' ]->id ) )
            {
				$woocommerce_cpcff_order_details = array();

			    $data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME." WHERE id=%d", $data_id ) );
				if( !empty( $data->paypal_post ) && ( $dataArr = @unserialize( $data->paypal_post ) ) !== false )
				{
					$woocommerce_cpcff_order_details[ 'cff_params' ] = $dataArr;

					foreach( $dataArr as $fieldname => $value )
					{
						if( strpos( $fieldname, '_url' ) !== false )
						{
							$_fieldname = str_replace( '_url', '', $fieldname );
							$_value     = $dataArr[ $_fieldname ];
							$_values 	= explode( ',', $_value );
							$_replacement = array();

							if( count( $_values ) == count( $value ) )
							{
								foreach( $_values as $key => $_fileName )
								{
									$_fileName = trim( $_fileName );
									$_replacement[] = '<a href="'.$value[ $key ].'" target="_blank">'.$_fileName.'</a>';
								}
							}
							if( !empty( $_replacement ) )
							{
								$data->data = str_replace( $_value, implode( ', ', $_replacement ) , $data->data );
							}
						}
					}
				}

				$data->data = preg_replace( "/\n+/", "<br />", $data->data );

				// If was defined a summary associated to the product add it as metadata,
				$activate_summary = get_post_meta( $values[ 'product_id' ], 'woocommerce_cpcff_activate_summary', true );
				if( !empty( $activate_summary ) && function_exists( 'cp_calculatedfieldsf_form_result' ) )
				{
					$summary = get_post_meta( $values[ 'product_id' ], 'woocommerce_cpcff_summary', true );
					if( empty( $summary ) ) $summary = '<%INFO%>';
					elseif($summary !== strip_tags($summary)) $summary = str_replace(array("\n","\r"), '', $summary);

					$metadata_label = get_post_meta( $values[ 'product_id' ], 'woocommerce_cpcff_summary_title', true );
					if(!empty($metadata_label)) $metadata_label = trim($metadata_label);

					$metadata = cp_calculatedfieldsf_form_result( array(), $summary, $data_id);
					$woocommerce_cpcff_order_details['data'] = $data->data;
				}
				else
				{
					$metadata = $data->data;
				}

				add_post_meta( $item_id, 'woocommerce_cpcff_order_details', $woocommerce_cpcff_order_details, true );
				wc_add_order_item_meta( $item_id, __((!empty($metadata_label)) ? $metadata_label : 'Data'), $metadata, true );
            }

        } // End add_order_item_meta

        /**
         * Display the form associated to the product
         */
        public function display_form()
        {
            global $post, $woocommerce;

            if ( $this->apply_addon() ) {

				$product = null;
				if (function_exists('get_product')) {
					$product = get_product($post->ID);
				} else {
					$product = new WC_Product($post->ID);
				}

                $form_content = cp_calculatedfieldsf_filter_content( $this->form );

				// Initialize form fields
				if(
					(
						CP_SESSION::get_var( 'cp_cff_form_data' ) !== false &&
						!empty( $_REQUEST[ 'cp_calculatedfieldsf_id' ] ) &&
						!empty( $_REQUEST[ 'cp_calculatedfieldsf_pform_psequence' ] )
					) ||
					(
						!empty( $_REQUEST[ 'cp_cff_wc' ] )
					)
				)
				{
					global $wpdb;

					$pform_psequence = ( !empty( $_REQUEST[ 'cp_calculatedfieldsf_pform_psequence' ] ) ) ? $_REQUEST[ 'cp_calculatedfieldsf_pform_psequence' ] : '1';

					if( !empty( $_REQUEST[ 'cp_cff_wc' ] ) )
					{
						$cart = WC()->cart->get_cart();
						if( !empty( $cart[ $_REQUEST[ 'cp_cff_wc' ] ] ) && !empty( $cart[ $_REQUEST[ 'cp_cff_wc' ] ][ 'cp_cff_form_data' ] ) )
						{
							$result = $wpdb->get_row( $wpdb->prepare( "SELECT form_data.paypal_post AS paypal_post FROM ".CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME." AS form_data WHERE form_data.id=%d", $cart[ $_REQUEST[ 'cp_cff_wc' ] ][ 'cp_cff_form_data' ] ) );
						}
					}
					elseif(
						CP_SESSION::get_var( 'cp_cff_form_data' ) !== false &&
						!empty( $_REQUEST[ 'cp_calculatedfieldsf_id' ] )
					)
					{
						$result = $wpdb->get_row( $wpdb->prepare( "SELECT form_data.paypal_post AS paypal_post FROM ".CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME." AS form_data WHERE form_data.id=%d AND form_data.formid=%d", CP_SESSION::get_var( 'cp_cff_form_data' ),  $_REQUEST[ 'cp_calculatedfieldsf_id' ] ) );
					}

					if( !empty( $result ) )
					{
						$arr = array();
						$submitted_data = unserialize( $result->paypal_post );
						foreach( $submitted_data as $key => $val )
						{
							if( preg_match( '/^fieldname\d+$/', $key ) )
							{
								$arr[ $key ] = $val;
							}
						}
				?>
						<script>
							cpcff_default  = ( typeof cpcff_default != 'undefined' ) ? cpcff_default : {};
							cpcff_default[ <?php
								echo preg_replace(
									'/[^\d]/',
									'',
									$pform_psequence
								);
							?> ] = <?php echo json_encode( $arr ); ?>;
						</script>
				<?php
					}
				}
				CP_SESSION::unset_var( 'cp_cff_form_data' );

                // Remove the form tags
                if( preg_match( '/<form[^>]*>/', $form_content, $match ) )
                {
                    $form_content = str_replace( $match[ 0 ], '', $form_content);
                    $form_content = preg_replace( '/<\/form>/', '', $form_content);
                }

                $tmp = get_post_meta( $post->ID, 'woocommerce_cpcff_calculate_price', true );
                $request_cost = ( !empty( $tmp ) ) ? cp_calculatedfieldsf_get_option( 'request_cost', false, $this->form[ 'id' ] ) : false;

                echo '<div class="cpcff-woocommerce-wrapper">'
                     .$form_content
                     .( ( method_exists( $woocommerce, 'nonce_field' ) ) ? $woocommerce->nonce_field('add_to_cart') : '' )
                     .'<input type="hidden" name="woocommerce_cpcff_product" value="'.$post->ID.'" />'
                     .( ( $request_cost ) ? '<input type="hidden" name="woocommerce_cpcff_field" value="'.$request_cost.'" /><input type="hidden" name="woocommerce_cpcff_form" value="'.$this->form[ 'id' ].'">' : '' )
                     .'</div>';

                $add_to_cart_value = '';
				if ($product->is_type('variable')) :
					$add_to_cart_value = 'variation';
				elseif ($product->has_child()) :
					$add_to_cart_value = 'group';
				else :
					$add_to_cart_value = $product->id;
				endif;

                if (!function_exists('get_product')) {
					//1.x only
					if( method_exists( $woocommerce, 'nonce_field' ) ) $woocommerce->nonce_field('add_to_cart');
					echo '<input type="hidden" name="add-to-cart" value="' . $add_to_cart_value . '" />';
				} else {
					echo '<input type="hidden" name="add-to-cart" value="' . $post->ID . '" />';
				}
			}

			echo '<div class="clear"></div>';

        } // End display_form

        /**
         * Enqueue all resources: CSS and JS files, required by the Addon
         */
        public function enqueue_scripts()
        {
            if( $this->apply_addon() )
            {
                wp_enqueue_style ( 'cpcff_wocommerce_addon_css', plugins_url('/woocommerce.addon/css/styles.css', __FILE__) );
                wp_enqueue_script( 'cpcff_wocommerce_addon_js', plugins_url('/woocommerce.addon/js/scripts.js',  __FILE__), array( 'jquery' ) );
            }

        } // End enqueue_scripts

        public function enqueue_cart_resources()
        {
            wp_enqueue_style ( 'cpcff_wocommerce_addon_cart_css', plugins_url('/woocommerce.addon/css/styles.cart.css', __FILE__) );
        } // End enqueue_cart_resources

        /**
         * Corrects the form options
         */
        public function get_form_options( $value, $field, $id )
        {
            if( $this->apply_addon() )
            {
                switch( $field )
                {
                    case 'fp_return_page':
                        return $_SERVER[ 'REQUEST_URI' ];
                    case 'cv_enable_captcha':
                        return 0;
                    break;
                    case 'cache':
                        return '';
                    case 'enable_paypal':
                        return 0;
                }
            }
            return $value;

        } // End get_form_options

        /************************ METHODS FOR PRODUCT PAGE  *****************************/

        public function init_hook()
        {
            add_meta_box('cpcff_woocommerce_metabox', __("Calculated Fields Form", 'calculated-fields-form'), array(&$this, 'metabox_form'), 'product', 'normal', 'high');
            add_action('save_post', array(&$this, 'save_data'));
        } // End init_hook

        public function metabox_form()
        {
            global $post, $wpdb;

            $id = get_post_meta( $post->ID, 'woocommerce_cpcff_form', true );
            $active = get_post_meta( $post->ID, 'woocommerce_cpcff_calculate_price', true );
            $minimum_price = get_post_meta( $post->ID, 'woocommerce_cpcff_minimum_price', true );
			$weight_field = get_post_meta( $post->ID, 'woocommerce_cpcff_weight_field', true );
			$length_field = get_post_meta( $post->ID, 'woocommerce_cpcff_length_field', true );
			$width_field = get_post_meta( $post->ID, 'woocommerce_cpcff_width_field', true );
			$height_field = get_post_meta( $post->ID, 'woocommerce_cpcff_height_field', true );
            $activate_summary = get_post_meta( $post->ID, 'woocommerce_cpcff_activate_summary', true );
            $summary_title = get_post_meta( $post->ID, 'woocommerce_cpcff_summary_title', true );
            $summary = get_post_meta( $post->ID, 'woocommerce_cpcff_summary', true );
			?>
            <table class="form-table">
				<tr>
					<td>
						<?php _e('Enter the ID of the form', 'calculated-fields-form');?>:
					</td>
                    <td>
						<select name="woocommerce_cpcff_form">
							<option value=""><?php print esc_html( __( 'Select a form', 'calculated-fields-form' ) ); ?></option>
						<?php
							$forms_list = $wpdb->get_results( "SELECT id, form_name FROM ".$wpdb->prefix.CP_CALCULATEDFIELDSF_FORMS_TABLE );
							foreach( $forms_list as $form )
							{
								$selected = ( !empty( $id ) && $form->id == $id ) ? 'SELECTED' : '';
								print '<option value="'.$form->id.'" '.$selected.'>'.esc_html( $form->form_name ).' ('.$form->id.')</option>';
							}
						?>
						</select>
                    </td>
                </tr>
                <tr>
					<td style="white-space:nowrap;">
						<?php _e('Calculate the product price through the form', 'calculated-fields-form');?>:
					</td>
                    <td style="width:100%;">
                        <input type="checkbox" name="woocommerce_cpcff_calculate_price" <?php print( ( !empty( $active ) ) ? 'checked' : '' ); ?> />
					</td>
				</tr>
				<tr>
					<td>
						<?php _e('Minimum price allowed (numbers only)', 'calculated-fields-form');?>:
					</td>
					<td>
						<input type="text" name="woocommerce_cpcff_minimum_price" value="<?php print( esc_attr( ( !empty( $minimum_price ) ) ? $minimum_price : '' ) ); ?>">
                    </td>
                </tr>
				<tr>
					<td style="white-space:nowrap;vertical-align:top;">
						<?php _e('Field for weight', 'calculated-fields-form');?>:
					</td>
                    <td style="width:100%;">
                        <input type="text" name="woocommerce_cpcff_weight_field" value="<?php print esc_attr( $weight_field ); ?>" placeholder="fieldname#" /><br />
						<em><?php _e('If the product\'s weight is determined through the form', 'calculated-fields-form');?></em>
					</td>
				</tr>
				<tr>
					<td style="white-space:nowrap;vertical-align:top;">
						<?php _e('Field for length', 'calculated-fields-form');?>:
					</td>
                    <td style="width:100%;">
                        <input type="text" name="woocommerce_cpcff_length_field" value="<?php print esc_attr( $length_field ); ?>" placeholder="fieldname#" /><br />
						<em><?php _e('If the product\'s length is determined through the form', 'calculated-fields-form');?></em>
					</td>
				</tr>
				<tr>
					<td style="white-space:nowrap;vertical-align:top;">
						<?php _e('Field for width', 'calculated-fields-form');?>:
					</td>
                    <td style="width:100%;">
                        <input type="text" name="woocommerce_cpcff_width_field" value="<?php print esc_attr( $width_field ); ?>" placeholder="fieldname#" /><br />
						<em><?php _e('If the product\'s width is determined through the form', 'calculated-fields-form');?></em>
					</td>
				</tr>
				<tr>
					<td style="white-space:nowrap;vertical-align:top;">
						<?php _e('Field for height', 'calculated-fields-form');?>:
					</td>
                    <td style="width:100%;">
                        <input type="text" name="woocommerce_cpcff_height_field" value="<?php print esc_attr( $height_field ); ?>" placeholder="fieldname#" /><br />
						<em><?php _e('If the product\'s height is determined through the form', 'calculated-fields-form');?></em>
					</td>
				</tr>
				<tr style="border-top:2px solid #DDD;border-left:2px solid #DDD;border-right:2px solid #DDD;">
					<td colspan="2">
						<?php _e('The summary section is optional. It is possible to use the special tags supported by the notification emails.', 'calculated-fields-form');?>
					</td>
				</tr>
				<tr style="border-left:2px solid #DDD;border-right:2px solid #DDD;">
					<td>
						<?php _e('Activate the summary', 'calculated-fields-form');?>:
					</td>
					<td>
						<input type="checkbox" name="woocommerce_cpcff_activate_summary" <?php print( ( !empty( $activate_summary ) ) ? 'CHECKED' : '' ); ?> />
                    </td>
                </tr>
				<tr style="border-left:2px solid #DDD;border-right:2px solid #DDD;">
					<td>
						<?php _e('Summary title', 'calculated-fields-form');?>:
					</td>
					<td>
						<input type="text" name="woocommerce_cpcff_summary_title" value="<?php print( esc_attr( ( !empty( $summary_title ) ) ? $summary_title : '' ) ); ?>" style="width:100%;">
                    </td>
                </tr>
				<tr style="border-bottom:2px solid #DDD;border-left:2px solid #DDD;border-right:2px solid #DDD;">
					<td>
						<?php _e('Summary', 'calculated-fields-form');?>:
					</td>
					<td>
						<textarea name="woocommerce_cpcff_summary" style="resize: vertical; min-height: 70px; width:100%;"><?php print ( esc_textarea( ( !empty( $summary ) ) ? $summary : '' ) ); ?></textarea>
					</td>
                </tr>

            </table>
			<?php

        } // End metabox_form

        public function save_data()
        {
            global $post;

            if( !empty( $post ) && is_object( $post ) && $post->post_type == 'product' )
            {
                delete_post_meta( $post->ID, 'woocommerce_cpcff_form' );
                delete_post_meta( $post->ID, 'woocommerce_cpcff_calculate_price' );
                delete_post_meta( $post->ID, 'woocommerce_cpcff_minimum_price' );
                delete_post_meta( $post->ID, 'woocommerce_cpcff_weight_field' );
                delete_post_meta( $post->ID, 'woocommerce_cpcff_length_field' );
                delete_post_meta( $post->ID, 'woocommerce_cpcff_width_field' );
                delete_post_meta( $post->ID, 'woocommerce_cpcff_height_field' );
                delete_post_meta( $post->ID, 'woocommerce_cpcff_activate_summary' );
                delete_post_meta( $post->ID, 'woocommerce_cpcff_summary' );
                delete_post_meta( $post->ID, 'woocommerce_cpcff_summary_title' );

                if( isset( $_REQUEST[ 'woocommerce_cpcff_form' ] ) )
                {
                    add_post_meta( $post->ID, 'woocommerce_cpcff_form', $_REQUEST[ 'woocommerce_cpcff_form' ], true );
                    add_post_meta( $post->ID, 'woocommerce_cpcff_weight_field', trim( $_REQUEST[ 'woocommerce_cpcff_weight_field' ] ), true );
                    add_post_meta( $post->ID, 'woocommerce_cpcff_length_field', trim( $_REQUEST[ 'woocommerce_cpcff_length_field' ] ), true );
                    add_post_meta( $post->ID, 'woocommerce_cpcff_width_field', trim( $_REQUEST[ 'woocommerce_cpcff_width_field' ] ), true );
                    add_post_meta( $post->ID, 'woocommerce_cpcff_height_field', trim( $_REQUEST[ 'woocommerce_cpcff_height_field' ] ), true );
                    add_post_meta( $post->ID, 'woocommerce_cpcff_minimum_price', trim( $_REQUEST[ 'woocommerce_cpcff_minimum_price' ] ), true );
                    add_post_meta(
                        $post->ID,
                        'woocommerce_cpcff_calculate_price',
                        ( empty( $_REQUEST[ 'woocommerce_cpcff_calculate_price' ] ) ) ? false : true,
                        true
                    );
                    add_post_meta( $post->ID, 'woocommerce_cpcff_activate_summary', ( !empty( $_REQUEST[ 'woocommerce_cpcff_activate_summary' ] ) ) ? 1 : 0, true );
                    add_post_meta( $post->ID, 'woocommerce_cpcff_summary_title', trim( $_REQUEST[ 'woocommerce_cpcff_summary_title' ] ), true );
					add_post_meta( $post->ID, 'woocommerce_cpcff_summary', trim( $_REQUEST[ 'woocommerce_cpcff_summary' ] ), true );
				}
            }
        }

		/**
		 *	Delete the form from the addon's table
		 */
        public function delete_form( $formid)
		{
			global $wpdb;
			$wpdb->delete(
				$wpdb->postmeta,
				array('meta_key' => 'woocommerce_cpcff_form', 'meta_value' => $formid),
				array('%s','%d')
			);
		} // delete_form
    } // End Class

    // Main add-on code
    $cpcff_woocommerce_obj = new CPCFF_WooCommerce();

	// Add addon object to the objects list
	global $cpcff_addons_objs_list;
	$cpcff_addons_objs_list[ $cpcff_woocommerce_obj->get_addon_id() ] = $cpcff_woocommerce_obj;
}
?>