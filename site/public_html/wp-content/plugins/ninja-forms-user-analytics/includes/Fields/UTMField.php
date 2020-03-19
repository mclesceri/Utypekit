<?php if ( ! defined( 'ABSPATH' ) ) exit;

abstract class NF_UserAnalytics_Fields_UTMField extends NF_Fields_Hidden
{
    protected $_name = 'user-analytics-utm';

    protected $_section = 'user-analytics';

    protected $_type = 'user-analytics-utm';
        
    protected $_icon = 'pie-chart';
    
    protected $_templates = array( 'hidden', 'wrap-user-analytics' );

    protected $_wrap_template = 'wrap-user-analytics';
    
    protected $url_param = null;

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'UTM Field', 'ninja-forms-ua' );
        unset( $this->_settings[ 'default' ] );
    }
    
    public function localize_settings( $settings, $form ) {
        if ( $this->url_param ) {
            $settings['value'] = isset( $_GET[ $this->url_param ] ) ? $_GET[ $this->url_param ] : "n/a" ;
        }
        
        return $settings;
    }
    
    public function get_parent_type()
    {
        return parent::get_type();
    }
}