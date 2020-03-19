<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_UserAnalytics_Fields_UTMTerm extends NF_UserAnalytics_Fields_UTMField
{
    protected $_name = 'user-analytics-utm-term';

    protected $_type = 'user-analytics-utm-term';
    
    protected $url_param = 'utm_term';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'UTM Term', 'ninja-forms-ua' );
    }
}