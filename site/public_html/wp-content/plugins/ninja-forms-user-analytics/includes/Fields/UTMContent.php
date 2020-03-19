<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_UserAnalytics_Fields_UTMContent extends NF_UserAnalytics_Fields_UTMField
{
    protected $_name = 'user-analytics-utm-content';

    protected $_type = 'user-analytics-utm-content';
    
    protected $url_param = 'utm_content';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'UTM Content', 'ninja-forms-ua' );
    }
}