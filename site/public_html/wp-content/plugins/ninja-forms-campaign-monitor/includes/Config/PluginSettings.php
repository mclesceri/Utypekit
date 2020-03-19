<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'nf_campaign_monitor_plugin_settings', array(
    /*
    |--------------------------------------------------------------------------
    | Campaign Monitor API Key
    |--------------------------------------------------------------------------
    */

    'ninja_forms_cm_api' => array(
        'id'    => 'ninja_forms_cm_api',
        'type'  => 'textbox',
        'label' => __( 'Campaign Monitor API Key', 'ninja-forms-campaign-monitor' ),
        'desc' => sprintf(
            __( 'Enter your Campaign Monitor API key. This can be found under your Account Settings.', 'ninja-forms-campaign-monitor' )
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Campaign Monitor Client ID
    |--------------------------------------------------------------------------
    */

    'ninja_forms_cm_client' => array(
        'id'    => 'ninja_forms_cm_client',
        'type'  => 'textbox',
        'label' => __( 'Campaign Monitor Client ID', 'ninja-forms-campaign-monitor' ),
        'desc' => sprintf(
            __( 'Enter your Campaign Monitor Client ID. The ID can be found in the Client Settings page of the client.', 'ninja-forms-campaign-monitor' )
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Show Extra Keys Checkbox
    |--------------------------------------------------------------------------
    */

    'campaign_monitor_multi_keys' => array(
        'id'    => 'campaign_monitor_multi_keys',
        'type'  => 'checkbox',
        'label' => __( 'Use multiple Campaign Monitor accounts', 'ninja-forms-campaign-monitor' ),
    ),    

    /*
    |--------------------------------------------------------------------------
    | Campaign Monitor API Key
    |--------------------------------------------------------------------------
    */

    'ninja_forms_cm_api2' => array(
        'id'    => 'ninja_forms_cm_api2',
        'type'  => 'textbox',
        'label' => __( 'Campaign Monitor API Key', 'ninja-forms-campaign-monitor' ),
        'desc' => sprintf(
            __( 'Enter your Campaign Monitor API key. This can be found under your Account Settings.', 'ninja-forms-campaign-monitor' )
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Campaign Monitor Client ID
    |--------------------------------------------------------------------------
    */

    'ninja_forms_cm_client2' => array(
        'id'    => 'ninja_forms_cm_client2',
        'type'  => 'textbox',
        'label' => __( 'Campaign Monitor Client ID', 'ninja-forms-campaign-monitor' ),
        'desc' => sprintf(
            __( 'Enter your Campaign Monitor Client ID. The ID can be found in the Client Settings page of the client.', 'ninja-forms-campaign-monitor' )
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Divider
    |--------------------------------------------------------------------------
    */

    'campaign_monitor_divider2' => array(
        'id'    => 'campaign_monitor_divider2',
        'type'  => 'html',
        'label' => '',
        'html' => '<hr />'
    ),

    /*
    |--------------------------------------------------------------------------
    | Campaign Monitor API Key
    |--------------------------------------------------------------------------
    */

    'ninja_forms_cm_api3' => array(
        'id'    => 'ninja_forms_cm_api3',
        'type'  => 'textbox',
        'label' => __( 'Campaign Monitor API Key', 'ninja-forms-campaign-monitor' ),
        'desc' => sprintf(
            __( 'Enter your Campaign Monitor API key. This can be found under your Account Settings.', 'ninja-forms-campaign-monitor' )
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Campaign Monitor Client ID
    |--------------------------------------------------------------------------
    */

    'ninja_forms_cm_client3' => array(
        'id'    => 'ninja_forms_cm_client3',
        'type'  => 'textbox',
        'label' => __( 'Campaign Monitor Client ID', 'ninja-forms-campaign-monitor' ),
        'desc' => sprintf(
            __( 'Enter your Campaign Monitor Client ID. The ID can be found in the Client Settings page of the client.', 'ninja-forms-campaign-monitor' )
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Divider
    |--------------------------------------------------------------------------
    */

    'campaign_monitor_divider3' => array(
        'id'    => 'campaign_monitor_divider3',
        'type'  => 'html',
        'label' => '',
        'html' => '<hr />'
    ),

    /*
    |--------------------------------------------------------------------------
    | Campaign Monitor API Key
    |--------------------------------------------------------------------------
    */

    'ninja_forms_cm_api4' => array(
        'id'    => 'ninja_forms_cm_api4',
        'type'  => 'textbox',
        'label' => __( 'Campaign Monitor API Key', 'ninja-forms-campaign-monitor' ),
        'desc' => sprintf(
            __( 'Enter your Campaign Monitor API key. This can be found under your Account Settings.', 'ninja-forms-campaign-monitor' )
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Campaign Monitor Client ID
    |--------------------------------------------------------------------------
    */

    'ninja_forms_cm_client4' => array(
        'id'    => 'ninja_forms_cm_client4',
        'type'  => 'textbox',
        'label' => __( 'Campaign Monitor Client ID', 'ninja-forms-campaign-monitor' ),
        'desc' => sprintf(
            __( 'Enter your Campaign Monitor Client ID. The ID can be found in the Client Settings page of the client.', 'ninja-forms-campaign-monitor' )
        ),
    ),

));