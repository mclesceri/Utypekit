<?php
/*
 * Plugin Name: Ninja Forms - User Analytics
 * Plugin URI: https://ninjaforms.com/extensions/user-analytics/
 * Description: Add user analytics to Ninja Forms.
 * Version: 3.0.0
 * Author: BFTrick
 * Author URI: http://www.speakinginbytes.com
 * Text Domain: ninja-forms-user-analytics
 *
 * Copyright 2018 Patrick Rauland.
 */

if ( ! defined( 'ABSPATH' ) ) exit;
 
if ( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

    include plugin_dir_path( __FILE__ ) . 'deprecated/ninja-forms-user-analytics.php';

} else {

    /**
     * Class NF_UserAnalytics brings User Analytics fields to Ninja Forms.
     */
    final class NF_UserAnalytics
    {
        const VERSION = '3.0.0';
        const SLUG    = 'user-analytics';
        const NAME    = 'User Analytics';
        const AUTHOR  = 'Never5';
        const PREFIX  = 'NF_UserAnalytics';

        /**
         * @var NF_UserAnalytics
         * @since 3.0
         */
        private static $instance;

        /**
         * Plugin Directory
         *
         * @since 3.0
         * @var string $dir
         */
        public static $dir = '';

        /**
         * Plugin URL
         *
         * @since 3.0
         * @var string $url
         */
        public static $url = '';
        
        /**
         * List of user analytics field classes
         *
         * @var array $fields
         */
        private static $fields = array(
            'user-analytics-browser' => 'NF_UserAnalytics_Fields_Browser',
            'user-analytics-browser-version' => 'NF_UserAnalytics_Fields_BrowserVersion',
            'user-analytics-city' => 'NF_UserAnalytics_Fields_City',
            'user-analytics-country' => 'NF_UserAnalytics_Fields_Country',
            'user-analytics-ip-address' => 'NF_UserAnalytics_Fields_IPAddress',
            'user-analytics-latitude' => 'NF_UserAnalytics_Fields_Latitude',
            'user-analytics-longitude' => 'NF_UserAnalytics_Fields_Longitude',
            'user-analytics-os' => 'NF_UserAnalytics_Fields_OperatingSystem',
            'user-analytics-region' => 'NF_UserAnalytics_Fields_Region',
            'user-analytics-referer' => 'NF_UserAnalytics_Fields_URLReferer',
            'user-analytics-utm-campaign' => 'NF_UserAnalytics_Fields_UTMCampaign',
            'user-analytics-utm-content' => 'NF_UserAnalytics_Fields_UTMContent',
            'user-analytics-utm-medium' => 'NF_UserAnalytics_Fields_UTMMedium',
            'user-analytics-utm-source' => 'NF_UserAnalytics_Fields_UTMSource',
            'user-analytics-utm-term' => 'NF_UserAnalytics_Fields_UTMTerm',
        );

        /**
         * Main Plugin Instance
         *
         * Insures that only one instance of a plugin class exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 3.0
         * @static
         * @static var array $instance
         * @return NF_UserAnalytics Highlander Instance
         */
        public static function instance()
        {
            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof NF_UserAnalytics ) ) {
                self::$instance = new NF_UserAnalytics();

                self::$dir = plugin_dir_path(__FILE__);

                self::$url = plugin_dir_url(__FILE__);

                /*
                 * Register our autoloader
                 */
                spl_autoload_register( array( self::$instance, 'autoloader' ) );
            }
            
            return self::$instance;
        }

        /**
         * Constructor.
         */
        public function __construct()
        {
            /*
             * Required for all Extensions.
             */
            add_action( 'admin_init', array( $this, 'setup_license' ) );

            /**
             * Register all User Analytics fields
             */
            add_filter( 'ninja_forms_field_type_sections', array( $this, 'add_section' ) );
            add_filter( 'ninja_forms_register_fields', array( $this, 'register_fields' ) );
            
            /**
             * Register all User Analytics templates
             */
            add_filter( 'ninja_forms_field_template_file_paths', array( $this, 'register_template_path' ) );
            
            /**
             * Enqueue script if required
             */
            add_action( 'ninja_forms_before_form_display', array( $this, 'enqueue_scripts' ) );
            
            /**
             * Provide host info (based on IP) through AJAX
             */
            add_action( 'wp_ajax_ninjaforms_useranalytics_data', array( $this, 'ajax_host_info' ) );
            add_action( 'wp_ajax_nopriv_ninjaforms_useranalytics_data', array( $this, 'ajax_host_info' ) );
        }
        
        /**
         * Retrieve host info based on IP address.
         * Response is echoed as JSON.
         */
        public function ajax_host_info()
        {
            $response = wp_remote_get( 'http://www.geoplugin.net/json.gp?ip=' . $this->get_ip_address() );

            if ( ! is_wp_error( $response ) ) {
                echo $response['body'];
            }

            exit;
        }
        
        /**
        * Get the URL Referer
        *
        * @return string
        * @since
        */
        public function get_url_referer() {
            $ref = ( ! empty( $_SERVER["HTTP_REFERER"] ) ? $_SERVER["HTTP_REFERER"] : '' );

            return sanitize_text_field( $ref );
        }
        
        /**
         * Get IP address of the user.
         *
         * @return string IP address of user
         * @since 1.2.1
         */
        public function get_ip_address()
        {
            // if HTTP_X_FORWARDED_FOR key is present we should use it
            if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
                if ( strpos( $_SERVER['HTTP_X_FORWARDED_FOR'], ',' ) > 0 ) {
                    $addr = explode( ",", $_SERVER['HTTP_X_FORWARDED_FOR'] );
                    $ip   = trim( $addr[0] );
                } else {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                }
            } else {
                // as a backup use the standard REMOTE_ADDR
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            return $ip;
        }
        
        /**
         * Enqueue scripts if required.
         *
         * @param int $form_id ID of form that is about to be displayed
         */
        public function enqueue_scripts( $form_id )
        {
            /**
             * Loop through all fields of this form to check if it has User Analytics fields.
             */
            $fields = Ninja_Forms()->form( $form_id )->get_fields();
            $active = false;
            foreach ( $fields as $field ) {
                $settings = $field->get_settings();
                $key = 'user-analytics-';
                if ( substr( $settings['type'], 0, strlen( $key ) ) == $key ) {
                    $active = true;
                    break;
                }
            }
        
            /**
             * Enqueue custom script if the form has User Analytics fields.
             */
            if ( $active ) {
                wp_enqueue_script( 'nf-user-analytics', self::$url . 'assets/js/user-analytics.js', array( 'jquery', 'nf-front-end' ), self::VERSION.date('YmdHis') );
                wp_localize_script( 'nf-user-analytics', 'nfua', array(
                    'ajax_url' => admin_url( 'admin-ajax.php' )
                ) );
            }
        }
        
        /**
         * Register the User Analytics templates path.
         *
         * @param array $paths Array of paths to modify
         * @return array Modified array of paths including the path to the User Analaytics templates
         */
        public function register_template_path( $paths )
        {
            $paths[] = self::$dir . 'includes/Templates/';
            
            return $paths;
        }

        /**
         * Add a section for all User Analytics fields.
         *
         * @param array $sections Array of sections to modify
         * @return array Modified array of sections including a section for all User Analytics fields
         */
        public function add_section( $sections )
        {
            $sections[ 'user-analytics' ] = array(
                'id' => 'user-analytics',
                'nicename' => __( 'User Analytics Fields', 'ninja-forms-ua' ),
                'fieldTypes' => array(),
            );

            return $sections;
        }
        
        /**
         * Register all User Analytics fields.
         *
         * @param array $fields Array of fields to modify
         * @return array Modified array of fields including all User Analytics fields
         */
        public function register_fields( $fields )
        {
            foreach ( self::$fields as $field_key => $class_name ) {
                if ( class_exists( $class_name ) ) {
                    $fields[ $field_key ] = new $class_name;
                }
            }

            return $fields;
        }

        /**
         * Autoloader functionality.
         *
         * @param string $class_name Name of class to load
         */
        public function autoloader( $class_name )
        {
            if ( class_exists( $class_name ) ) {
                return;
            }

            if ( false === strpos( $class_name, self::PREFIX ) ) {
                return;
            }

            $class_name = str_replace( self::PREFIX, '', $class_name );
            $classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
            $class_file = str_replace( '_', DIRECTORY_SEPARATOR, $class_name ) . '.php';

            if ( file_exists( $classes_dir . $class_file ) ) {
                require_once $classes_dir . $class_file;
            }
        }

        /*
         * Setup license.
         */
        public function setup_license()
        {
            if ( ! class_exists( 'NF_Extension_Updater' ) ) {
                return;
            }

            new NF_Extension_Updater( self::NAME, self::VERSION, self::AUTHOR, __FILE__, self::SLUG );
        }
        
        /**
         * Get all User Analytics fields.
         * This allows 3rd party plugins to get UA fields.
         *
         * @deprecated 3.0.0
         *
         * @return array Array of user analytics fields in old style format
         */
        public function get_ua_fields()
        {
            _deprecated_function( 'get_ua_fields', '3.0.0' );
            
            $fields = self::$fields;
            array_walk( $fields, function( &$item, $key ) {
                $class_name = $item;
                $label = __( 'Unknown' );
                if ( class_exists( $class_name ) ) {
                    $field = new $class_name;
                    $label = $field->get_nicename();
                }
                $key = str_replace( '-', '_', $key );
                
                $item = array(
                    'name' => $label,
                    'display_function' => "{$key}_display",
                    'sub_edit_function' => "{$key}_sub_edit_display",
                );
            } );
            
            return $fields;
        }
        
        /**
         * Magic method to support deprecated _display and _sub_edit_display functions.
         *
         * @param string $name Name of method being called
         * @param array $arguments Enumerated array containing parameters passed to method being called
         */
        public function __call( $name, $arguments )
        {
            if ( substr( $name, -strlen( '_sub_edit_display' ) ) == '_sub_edit_display' && count( $arguments ) == 2 ) {
                _deprecated_function( $name, '3.0.0' );
            
                $field_id = $arguments[0];
                $data = $arguments[1];
                $class_name = $this->_deprecated_find_class( $name, '_sub_edit_display' );
                if ( $class_name ) {
                    $this->_deprecated_edit_function( $field_id, $data, new $class_name );
                }
            } elseif ( substr( $name, -strlen( '_display' ) ) == '_display' && count( $arguments ) == 2 ) {
                _deprecated_function( $name, '3.0.0' );
            
                $field_id = $arguments[0];
                $data = $arguments[1];
                $class_name = $this->_deprecated_find_class( $name, '_display' );
                if ( $class_name ) {
                    $this->_deprecated_display_function( $field_id, $data, new $class_name );
                }
            }
        }
        
        /**
         * Find field class associated with a deprecated method name.
         *
         * @param string $method_name Name of method
         * @param string $suffix Suffix of method name
         * @return string Name of class if class can be resolved, null otherwise
         *
         * @note This is only here to support the deprecated get_ua_fields() method.
         */
        private function _deprecated_find_class( $method_name, $suffix )
        {
            $key = substr( $method_name, 0, strlen( $method_name ) - strlen( $suffix ) );
            $key = str_replace( '_', '-', $key );
            if ( array_key_exists( $key, self::$fields ) ) {
                $class_name = self::$fields[$key];
                if ( class_exists( $class_name ) ) {
                    return $class_name;
                }
            }
            
            return null;
        }
        
        /**
         * Display form field for editing in old style format.
         *
         * @param mixed $field_id Field identifier supplied by user
         * @param array $data Field data supplied by user
         * @param object $field Resolved field object
         *
         * @note This is only here to support the deprecated get_ua_fields() method.
         */
        private function _deprecated_edit_function( $field_id, $data, $field )
        {
            $key = $field->get_name();
            $label = $field->get_nicename();
        
            ?>
            
            <div class="field-wrap text-wrap label-left">
                <label for="ninja_forms_field_<?php echo $field_id; ?>" id="ninja_forms_field_<?php echo $field_id; ?>"><?php echo $label; ?></label>
                <input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-edit nfua-<?php echo $key; ?>"
                    value="<?php echo $data['default_value'] ?>">
            </div>
            
            <?php
        }
        
        /**
         * Display form field for editing in old style format.
         *
         * @param mixed $field_id Field identifier supplied by user
         * @param array $data Field data supplied by user
         * @param object $field Resolved field object
         *
         * @note This is only here to support the deprecated get_ua_fields() method.
         */
        private function _deprecated_display_function( $field_id, $data, $field )
        {
            $key = $field->get_name();
            $label = $field->get_nicename();
            $value = 'n/a';
            
            if ( $key == 'user-analytics-ip-address' ) {
                $value = $this->get_ip_address();
            } else if ( $key == 'user-analytics-referer' ) {
                $value = $this->get_url_referer();
            } else if ( substr( $key, 0, strlen( 'user-analytics-utm-' ) ) == 'user-analytics-utm-' ) {
                $settings = $field->localize_settings( array(), null );
                if ( is_array( $settings ) && array_key_exists( 'value', $settings ) ) {
                    $value = $settings['value'];
                }
            }
        
            ?>
            
            <input type="hidden" name="ninja_forms_field_<?php echo $field_id; ?>" class="nfua-display nfua-<?php echo $key; ?>" value="<?php echo esc_attr($value); ?>">
            
            <?php
        }
    }

    /**
     * The main function responsible for returning the plugin
     * instance to functions everywhere.
     *
     * Use this function like you would a global variable,
     * except without needing to declare the global.
     *
     * @since 3.0
     * @return NF_UserAnalytics Instance of plugin
     */
    function NF_UserAnalytics()
    {
        return NF_UserAnalytics::instance();
    }

    NF_UserAnalytics();
}
