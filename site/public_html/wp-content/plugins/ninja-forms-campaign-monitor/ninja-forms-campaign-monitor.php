<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Plugin Name: Ninja Forms - Campaign Monitor
 * Plugin URI: https://ninjaforms.com/extensions/campaign-monitor/
 * Description: Sign users up for your Campaign Monitor newsletter when submitting Ninja Forms
 * Version: 3.0.5
 * Author: The WP Ninjas
 * Author URI: https://ninjaforms.com/
 * Text Domain: ninja-forms-campaign-monitor
 *
 * Copyright 2016 The WP Ninjas.
 */

if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

    include 'deprecated/ninja-forms-campaign-monitor.php';

} else {

    if ( ! class_exists( 'CS_REST_Clients' ) ) {
        include_once 'includes/Libraries/vendor/csrest_clients.php';
        include_once 'includes/Libraries/vendor/csrest_lists.php';
        include_once 'includes/Libraries/vendor/csrest_subscribers.php';        
    }

    /**
     * Class NF_CampaignMonitor
     */
    final class NF_CampaignMonitor
    {
        const VERSION = '3.0.5';
        const SLUG    = 'campaign-monitor';
        const NAME    = 'Campaign Monitor';
        const AUTHOR  = 'The WP Ninjas';
        const PREFIX  = 'NF_CampaignMonitor';

        /**
         * @var Campaign Monitor Api Call
         */
        private $_api;

        /**
         * @var NF_CampaignMonitor
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
         * Main Plugin Instance
         *
         * Insures that only one instance of a plugin class exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 3.0
         * @static
         * @static var array $instance
         * @return NF_CampaignMonitor Highlander Instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof NF_CampaignMonitor)) {
                self::$instance = new NF_CampaignMonitor();

                self::$dir = plugin_dir_path(__FILE__);

                self::$url = plugin_dir_url(__FILE__);

                /*
                 * Register our autoloader
                 */
                spl_autoload_register(array(self::$instance, 'autoloader'));

                new NF_CampaignMonitor_Admin_Settings();
            }
            return self::$instance;
        }

        public function __construct()
        {
            /*
             * Required for all Extensions.
             */
            add_action( 'admin_init', array( $this, 'setup_license') );

            /*
             * Optional. If your extension creates a new field interaction or display template...
             */
            add_filter( 'ninja_forms_register_fields', array( $this, 'register_fields' ) );

            /*
             * Optional. If your extension processes or alters form submission data on a per form basis...
             */
            add_filter( 'ninja_forms_register_actions', array( $this, 'register_actions' ) );

            /*
             * Add our script to the forms->settings page so that we can hide/show the "multiple keys" section.
             */
            add_filter( 'ninja_forms_plugin_settings', array( $this, 'enqueue_settings_script' ) );
        }

        /**
         * Optional. If your extension creates a new field interaction or display template...
         */
        public function register_fields($actions)
        {
            $actions[ 'campaign-monitor-optin' ] = new NF_CampaignMonitor_Fields_OptIn(); // includes/Fields/CampaignMonitorExample.php

            return $actions;
        }

        /**
         * Optional. If your extension processes or alters form submission data on a per form basis...
         */
        public function register_actions($actions)
        {
            $actions[ 'campaign-monitor' ] = new NF_CampaignMonitor_Actions_CampaignMonitor(); // includes/Actions/CampaignMonitorExample.php

            return $actions;
        }

        /*
         * Optional methods for convenience.
         */

        public function autoloader($class_name)
        {
            if (class_exists($class_name)) return;

            if ( false === strpos( $class_name, self::PREFIX ) ) return;

            $class_name = str_replace( self::PREFIX, '', $class_name );
            $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
            $class_file = str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

            if (file_exists($classes_dir . $class_file)) {
                require_once $classes_dir . $class_file;
            }
        }

        public function subscribe( $api_key, $list_id, $data )
        {
            $api = new CS_REST_Subscribers( $list_id, $api_key );

            $subscribe = $api->add( $data );

            if( $subscribe->was_successful() ) {
                return true;
            }
            return false;
        }

        /**
         * @return array
         */
        public function get_lists()
        {
            if( ! $this->api() ) return array();

            $lists = array();

            /*
             * We could have multiple keys defined, so we need to make an API fetch request to each.
             */
            for ( $x = 1; $x < 4; $x++ ) { 

                if ( ! $this->api( $x ) ) continue;

                $result = $this->api( $x )->get_lists();

                if( '401' == $result->http_status_code ) return array();

                $y = ( 1 == $x ) ? '' : $x;

                $api_key = trim( Ninja_Forms()->get_setting( 'ninja_forms_cm_api' . $y ) );

                foreach( $result->response as $list ) {
                    $lists[ $list->ListID ] = $this->parse_list( $api_key, $list );
                }
            }

            update_option( 'nf_cm_lists', $lists );

            return $lists;
        }

        public function parse_list( $api_key, $list ) {
            $list_array = array();

            $list_api = new CS_REST_Lists( $list->ListID, $api_key );
            $custom_fields = $list_api->get_custom_fields();

            $fields = array(
                array(
                    'label'         => __( 'First Name', 'ninja-forms-campaign-monitor' ),
                    'value'         => 'first_name',
                ),
                array(
                    'label'         => __( 'Last Name', 'ninja-forms-campaign-monitor' ),
                    'value'         => 'last_name',
                ),
                array(
                    'label'         => __( 'Email', 'ninja-forms-campaign-monitor' ),
                    'value'         => 'email',
                ),
            );


            if ( ! empty ( $custom_fields->response ) ) {
                foreach( $custom_fields->response as $field ) {
                    $fields[] = array(
                        'label'     => $field->FieldName,
                        'value'     => $field->Key,
                    );
                }
            }

            return array(
                'api_key'           => $api_key,
                'label'             => $list->Name,
                'value'             => $list->ListID,
                'fields'            => $fields,
            );
        }


        /**
         * We could have multiple CM keys defined in our plugin settings.
         *
         * Because of this, our API function now accepts an integer 2-4 in order to access those other keys.
         * @return Campaign Monitor|CS_REST_Clients
         */
        public function api( $x = 1 )
        {

            if ( 1 == $x ) {
                $x = '';
            }

            $api_key = trim( Ninja_Forms()->get_setting( 'ninja_forms_cm_api' . $x ) );

            $client_id = trim( Ninja_Forms()->get_setting( 'ninja_forms_cm_client' . $x ) );

            if ( empty ( $api_key ) || empty ( $client_id ) ) {
                return false;
            }

            $this->_api = new CS_REST_Clients( $client_id, $api_key );

            return $this->_api;
        }
        
        /**
         * Template
         *
         * @param string $file_name
         * @param array $data
         */
        public static function template( $file_name = '', array $data = array() )
        {
            if( ! $file_name ) return;

            extract( $data );

            include self::$dir . 'includes/Templates/' . $file_name;
        }
        
        /**
         * Config
         *
         * @param $file_name
         * @return mixed
         */
        public static function config( $file_name )
        {
            return include self::$dir . 'includes/Config/' . $file_name . '.php';
        }

        /*
         * Required methods for all extension.
         */

        public function setup_license()
        {
            if ( ! class_exists( 'NF_Extension_Updater' ) ) return;

            new NF_Extension_Updater( self::NAME, self::VERSION, self::AUTHOR, __FILE__, self::SLUG );
        }

        public function enqueue_settings_script( $groups ) {
            wp_enqueue_script( 'backbone-marionette', Ninja_Forms::$url . 'assets/js/lib/backbone.marionette.min.js', array( 'jquery', 'backbone' ) );
            wp_enqueue_script( 'nfcm-settings', self::$url . 'assets/js/settings.js', array( 'jquery' ) );
            return $groups;
        }
    }

    /**
     * The main function responsible for returning The Highlander Plugin
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     * @since 3.0
     * @return {class} Highlander Instance
     */
    function NF_CampaignMonitor()
    {
        return NF_CampaignMonitor::instance();
    }

    NF_CampaignMonitor();
}

add_filter( 'ninja_forms_upgrade_settings', 'NF_CampaignMonitor_Upgrade' );
function NF_CampaignMonitor_Upgrade( $data ){
    if( ! 1 == $data['settings']['campaign_monitor_signup_form'] )
        return $data;

    $action = array(
        'active'            => '1',
        'name'              => __( 'Campaign Monitor', 'ninja-forms-campaign-monitor' ),
        'type'              => 'campaign-monitor',
        'newsletter_list'   => $data['settings']['ninja_forms_cm_list'],
    );

    foreach( $data['fields'] as $field ){
        if( isset( $field['data']['first_name'] ) && 1 == $field['data']['first_name'] ){
            $action['first_name'] = '{field:' . $field['id'] . '}';
            break;
       }
    }

    foreach( $data['fields'] as $field ){
        if( isset( $field['data']['last_name'] ) && 1 == $field['data']['last_name'] ){
            $action['last_name'] = '{field:' . $field['id'] . '}';
            break;
        }
    }

    foreach( $data['fields'] as $field ){
        if( isset( $field['data']['email'] ) && 1 == $field['data']['email'] ){
            $action['email'] = '{field:' . $field['id'] . '}';
            break;
        }
    }

    $action = array($action);

    $data['actions'] = array_merge( $data['actions'], $action );
    
    return $data;
}


