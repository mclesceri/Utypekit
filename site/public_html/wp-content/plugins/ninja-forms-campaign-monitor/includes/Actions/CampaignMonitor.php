<?php if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'NF_Abstracts_ActionNewsletter' ) ) return;

/**
 * Class NF_CampaignMonitor_Actions_CampaignMonitor
 */
final class NF_CampaignMonitor_Actions_CampaignMonitor extends NF_Abstracts_ActionNewsletter
{
    /**
     * @var string
     */
    protected $_name  = 'campaign-monitor';

    /**
     * @var array
     */
    protected $_tags = array();

    /**
     * @var string
     */
    protected $_timing = 'normal';

    /**
     * @var int
     */
    protected $_priority = '10';

    /**
     * Constructor
     */
    public function __construct()
{
    parent::__construct();

    $this->_nicename = __( 'Campaign Monitor', 'ninja-forms-campaign-monitor' );
    unset( $this->_settings[ 'campaign-monitornewsletter_list_groups' ] );

}

    /*
    * PUBLIC METHODS
    */
    public function save( $action_settings )
    {
    
    }

    public function process( $action_settings, $form_id, $data )
    {
        if( ! $this->is_opt_in( $data ) ) return $data;

        $name = $action_settings[ 'first_name' ] . ' ' . $action_settings[ 'last_name' ];

        /* 
         * Get our newsletter list info
         */
        $list_id = $action_settings[ 'newsletter_list' ];
        $lists = get_option( 'nf_cm_lists', array() );

        $member_data = array(
            'EmailAddress' => $action_settings[ 'email' ],
            'Name'         => $name,
            'Resubscribe'  => true
        );
        $this_action = Ninja_Forms()->form( $form_id )->get_action( $action_settings[ 'id' ] );

        /*
         * If our list is saved in our option, set our api key and add any custom fields.
         */
        if ( isset ( $lists[ $list_id ] ) ) {
            $api_key = $lists[ $list_id ][ 'api_key' ];
            /*
             * Add custom fields to our $member_data array.
             */

            if ( isset ( $lists[ $list_id ][ 'fields' ] ) ) {
                foreach( $lists[ $list_id ][ 'fields' ] as $field ) {
                    if ( 'first_name' == $field[ 'value' ] ) continue;
                    if ( 'last_name' == $field[ 'value' ] ) continue;
                    if ( 'email' == $field[ 'value' ] ) continue;

                    $field_key = str_replace( '{field:', '', $this_action->get_setting( $field[ 'value' ] ) );
                    $field_key = str_replace( '}', '', $field_key );

                    foreach( $data[ 'fields' ] as $passed_field ) {
                        if ( $field_key == $passed_field[ 'key' ] ) {
                            if ( 'listcheckbox' == $passed_field[ 'type' ] || 'listmultiselect' == $passed_field[ 'type' ] ) {
                                $user_value = explode( ',', $action_settings[ $field[ 'value' ] ] );
                                foreach ( $user_value as $val ) {
                                    $member_data[ 'CustomFields' ][] = array(
                                        'Key'       => $field[ 'value' ],
                                        'Value'     => $val
                                    );                                    
                                }
                            } else {
                                $member_data[ 'CustomFields' ][] = array(
                                    'Key'       => $field[ 'value' ],
                                    'Value'     => $action_settings[ $field[ 'value' ] ]
                                );
                            }
                        }
                    }
                }
            }
        } else { // Use default subscription settings and API key.
            $api_key = trim( Ninja_Forms()->get_setting( 'ninja_forms_cm_api') );
        }

        $response = NF_CampaignMonitor()->subscribe( $api_key, $list_id, $member_data );

        $data[ 'actions' ][ 'campaign_monitor' ][ 'response' ] = $response;
        $data[ 'actions' ][ 'campaign_monitor' ][ 'member_data' ] = $member_data;

        return $data;
    }

    protected function is_opt_in( $data )
    {
        $opt_in = TRUE;
        foreach( $data[ 'fields' ]as $field ){

            if( 'campaign-monitor-optin' != $field[ 'type' ] ) continue;

            if( ! $field[ 'value' ] ) $opt_in = FALSE;
        }
        return $opt_in;
    }

    public function get_lists()
    {
        return NF_CampaignMonitor()->get_lists();
    }
}
