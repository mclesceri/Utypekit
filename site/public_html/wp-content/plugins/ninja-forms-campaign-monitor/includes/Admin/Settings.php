<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_CampaignMonitor_Admin_Settings
 */
final class NF_CampaignMonitor_Admin_Settings
{
    public function __construct()
    {
        add_filter( 'ninja_forms_plugin_settings', array( $this, 'plugin_settings' ), 10, 1 );
        add_filter( 'ninja_forms_plugin_settings_groups', array( $this, 'plugin_settings_groups' ), 10, 1 );
        add_filter( 'ninja_forms_check_setting_ninja_forms_cm_client', array($this, 'validate_ninja_forms_ninja_forms_cm_client'), 10, 1);
    }

    public function plugin_settings( $settings )
    {
        $settings[ 'campaign_monitor' ] = NF_CampaignMonitor()->config( 'PluginSettings' );
        return $settings;
    }

    public function plugin_settings_groups( $groups )
    {
        $groups = array_merge( $groups, NF_CampaignMonitor()->config( 'PluginSettingsGroups' ) );
        return $groups;
    }

    public function validate_ninja_forms_ninja_forms_cm_client( $setting )
    {
        if ( 'ninja_forms[ninja_forms_cm_client]' == $setting[ 'id' ] && empty( $setting[ 'value' ] ) ) return $setting;
        if ( 'ninja_forms[ninja_forms_cm_api]' == $setting[ 'id' ] && empty( $setting[ 'value' ] ) ) return $setting;
        
        $response = NF_CampaignMonitor()->api()->get_lists();

        if( 200 == $response->http_status_code ){
            return $setting;
        } else{
            // TODO: Log Error, $e->getMessage(), for System Status Report
            $setting[ 'errors' ][] = __( 'One of the Campaign Monitor API keys you have entered appears to be invalid.', 'ninja-forms-constant-contact');
        }
        return $setting;
    }


} // End Class NF_CampaignMonitor_Admin_Settings
