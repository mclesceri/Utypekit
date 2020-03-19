<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_UserAnalytics_Fields_Country extends NF_Fields_Hidden
{
    protected $_name = 'user-analytics-country';

    protected $_section = 'user-analytics';

    protected $_type = 'user-analytics-country';
    
    protected $_icon = 'globe';
    
    protected $_templates = array( 'hidden', 'wrap-user-analytics' );

    protected $_wrap_template = 'wrap-user-analytics';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Country', 'ninja-forms-ua' );
        unset( $this->_settings[ 'default' ] );
    }
}