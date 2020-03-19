<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_UserAnalytics_Fields_OperatingSystem extends NF_Fields_Hidden
{
    protected $_name = 'user-analytics-os';

    protected $_section = 'user-analytics';

    protected $_type = 'user-analytics-os';
    
    protected $_icon = 'desktop';
    
    protected $_templates = array( 'hidden', 'wrap-user-analytics' );

    protected $_wrap_template = 'wrap-user-analytics';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Operating System', 'ninja-forms-ua' );
        unset( $this->_settings[ 'default' ] );
    }
}