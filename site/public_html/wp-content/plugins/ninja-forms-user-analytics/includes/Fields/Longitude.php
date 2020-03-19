<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_UserAnalytics_Fields_Longitude extends NF_Fields_Hidden
{
    protected $_name = 'user-analytics-longitude';

    protected $_section = 'user-analytics';

    protected $_type = 'user-analytics-longitude';
    
    protected $_icon = 'map-marker';
    
    protected $_templates = array( 'hidden', 'wrap-user-analytics' );

    protected $_wrap_template = 'wrap-user-analytics';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Longitude', 'ninja-forms-ua' );
        unset( $this->_settings[ 'default' ] );
    }
}