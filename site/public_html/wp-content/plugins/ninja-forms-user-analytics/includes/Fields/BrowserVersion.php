<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_UserAnalytics_Fields_BrowserVersion extends NF_Fields_Hidden
{
    protected $_name = 'user-analytics-browser-version';

    protected $_section = 'user-analytics';

    protected $_type = 'user-analytics-browser-version';
    
    protected $_icon = 'chrome';
    
    protected $_templates = array( 'hidden', 'wrap-user-analytics' );

    protected $_wrap_template = 'wrap-user-analytics';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Browser Version', 'ninja-forms-ua' );
        unset( $this->_settings[ 'default' ] );
    }
}