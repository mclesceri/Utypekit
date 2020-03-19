<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_UserAnalytics_Fields_UTMCampaign extends NF_UserAnalytics_Fields_UTMField
{
    protected $_name = 'user-analytics-utm-campaign';

    protected $_type = 'user-analytics-utm-campaign';
    
    protected $url_param = 'utm_campaign';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'UTM Campaign', 'ninja-forms-ua' );
    }
}