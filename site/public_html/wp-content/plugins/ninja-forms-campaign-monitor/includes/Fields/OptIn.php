<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_CampaignMonitor_Fields_OptIn
 */
class NF_CampaignMonitor_Fields_OptIn extends NF_Abstracts_FieldOptIn
{
    protected $_name = 'campaign-monitor-optin';

    protected $_section = 'common';

    protected $_type = 'campaign-monitor-optin';

    protected $_templates = 'checkbox';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Campaign Monitor OptIn', 'ninja-forms-campaign-monitor' );
    }
}