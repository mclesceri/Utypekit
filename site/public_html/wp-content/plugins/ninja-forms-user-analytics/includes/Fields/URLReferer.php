<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_UserAnalytics_Fields_URLReferer extends NF_Fields_Hidden
{
    protected $_name = 'user-analytics-referer';

    protected $_section = 'user-analytics';

    protected $_type = 'user-analytics-referer';
    
    protected $_icon = 'link';
    
    protected $_templates = array( 'hidden', 'wrap-user-analytics' );

    protected $_wrap_template = 'wrap-user-analytics';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'URL Referer', 'ninja-forms-ua' );
        unset( $this->_settings[ 'default' ] );
    }
    
    public function localize_settings( $settings, $form ) {
        /**
         * Set the field value based on the HTTP_REFERER supplied by the server info.
         */
        $url_referer = NF_UserAnalytics()->get_url_referer();
        $settings['value'] = empty($url_referer) ? 'n/a' : $url_referer;
        
        return $settings;
    }
}
