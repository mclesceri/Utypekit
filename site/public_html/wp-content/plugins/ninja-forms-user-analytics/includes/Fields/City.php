<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_UserAnalytics_Fields_City extends NF_Fields_Hidden
{
    protected $_name = 'user-analytics-city';

    protected $_section = 'user-analytics';

    protected $_type = 'user-analytics-city';
    
    protected $_icon = 'globe';
    
    protected $_templates = array( 'hidden', 'wrap-user-analytics' );

    protected $_wrap_template = 'wrap-user-analytics';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'City', 'ninja-forms-ua' );
        unset( $this->_settings[ 'default' ] );
    }
}