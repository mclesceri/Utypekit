<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_UserAnalytics_Fields_IPAddress extends NF_Fields_Hidden
{
    protected $_name = 'user-analytics-ip-address';

    protected $_section = 'user-analytics';

    protected $_type = 'user-analytics-ip-address';
    
    protected $_icon = 'map-marker';
    
    protected $_templates = array( 'hidden', 'wrap-user-analytics' ); 

    protected $_wrap_template = 'wrap-user-analytics';
    
    public function __construct()
    {
        parent::__construct();
        
        $this->_nicename = __( 'IP Address', 'ninja-forms-ua' );
        unset( $this->_settings[ 'default' ] );
    }
    
    public function localize_settings( $settings, $form ) {
        $settings['value'] = NF_UserAnalytics()->get_ip_address();
        
        return $settings;
    }
}
