<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_UserAnalytics_Fields_Region extends NF_Fields_Hidden
{
    protected $_name = 'user-analytics-region';

    protected $_section = 'user-analytics';

    protected $_type = 'user-analytics-region';
    
    protected $_icon = 'map-marker';
    
    protected $_templates = array( 'hidden', 'wrap-user-analytics' );

    protected $_wrap_template = 'wrap-user-analytics';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Region (State)', 'ninja-forms-ua' );
        unset( $this->_settings[ 'default' ] );
    }
}