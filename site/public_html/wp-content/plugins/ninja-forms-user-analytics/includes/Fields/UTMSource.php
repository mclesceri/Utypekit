<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_UserAnalytics_Fields_UTMSource extends NF_UserAnalytics_Fields_UTMField
{
    protected $_name = 'user-analytics-utm-source';

    protected $_type = 'user-analytics-utm-source';
    
    protected $url_param = 'utm_source';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'UTM Source', 'ninja-forms-ua' );
    }
}