<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_UserAnalytics_Fields_UTMMedium extends NF_UserAnalytics_Fields_UTMField
{
    protected $_name = 'user-analytics-utm-medium';

    protected $_type = 'user-analytics-utm-medium';
    
    protected $url_param = 'utm_medium';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'UTM Medium', 'ninja-forms-ua' );
    }
}