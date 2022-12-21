<?php if (!defined('ABSPATH')) {exit;} // Exit if accessed directly

class WWP_Notice_Bar{

    /**=================================================================================================================
     * Class Properties
     =================================================================================================================*/
    /**
     * Property that holds the single main instance of WWP_Notice_Bar.
     *
     * @since 2.1.2
     * @access private
     * @var WWP_Notice_Bar
     */
    private static $_instance;

    /**
     * Propery that holds wwp notice bar lite message.
     * 
     * @since 2.1.2
     * @access private
     */
    private $_notice_bar_lite_message;

    /**
     * Property the holds wwp upgrade link to premium version
     * 
     * @since 2.1.2
     * @access private
     */
    private $_upgrade_link;


    /**=================================================================================================================
     * Public Class Methods
     =================================================================================================================*/
    /**
     * WWP_Notice_Bar constructor.
     *
     * @since 2.1.2
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Notice_Bar model.
     */
    public function __construct($dependencies = array()){

        $this->_notice_bar_lite_message = apply_filters('wwp_notice_bar_lite_message', __('You\'re using Wholesale Prices by Wholesale Suite free version. To unlock more features consider <a href="%s" target="_blank">upgrading to Premium.</a>', 'woocomerce-wholesale-prices'));

        $this->_upgrade_link = apply_filters('wwp_noticebar_lite_upgrade_link','https://wholesalesuiteplugin.com/bundle/?utm_source=wwp&utm_medium=upsell&utm_campaign=litebar');
    }

    /**
     * Ensure that only one instance of WWP_Notice_Bar is loaded or can be loaded (Singleton Pattern).
     *
     * @since 2.1.2
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Notice_Bar model.
     * @return WWP_Notice_Bar
     */
    public static function instance($dependencies)
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self($dependencies);
        }

        return self::$_instance;

    }

    /**
     * Show WWP Notice Bar lite
     * 
     * This will show the WWP notice bar lite in WC Settings page or Wholesale Suite page just bellow headers page title. We will only show this if there are no other premium plugins (WWPP, WWLC, WWOF ) are activated or installed, only WWP.
     * 
     * We can set the $allowed_pages where this will be shown, the default value is 'wwp-settings' and 'wholesale-suite' by a filter e.g. apply_filters('wwp_noticebar_lite_allowed_pages', array('wholesale-suite', 'help-page', 'about-page', 'upgrade-to-premium-page', 'wwc_license_settings').
     * We can also set the $upgrade_link, using a filter 'wwp_noticebar_lite_upgrade_link'
     * 
     * @since 2.1.2
     * @access public
     */
    public function show_wwp_notice_bar_lite(){
        global $pagenow;

        $upgrade_link    = $this->_upgrade_link;
        $message         = $this->_notice_bar_lite_message;

        if( ! $this->has_wws_premiums() && $this->noticebar_lite_has_allowed_pages() ){

            // Show in Wholesale Dashboard, About, Help, and Upgrade to Premium page.
            // Show also on WWS license page if already migrated like in ACFW
            require_once WWP_VIEWS_PATH . 'view-wwp-notice-bar-lite.php';

        }elseif(! $this->has_wws_premiums() && $pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'wc-settings' && isset($_GET['tab']) && $_GET['tab'] == 'wwp_settings'){ 

            // Show in WC Settings > Wholesale Price tab
            require_once WWP_VIEWS_PATH . 'view-wwp-notice-bar-lite.php';

        }elseif(! $this->has_wws_premiums() && $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'shop_order' && isset($_GET['wwpp_fbwr']) && ($_GET['wwpp_fbwr'] == 'all_wholesale_orders' || $_GET['wwpp_fbwr'] == 'wholesale_customer' )){

            // Show in Wholesale > Orders and WooCommerce > Orders, 
            //where orders are filtered by "All Wholesale Orders" and "Wholesale Customers"
            require_once WWP_VIEWS_PATH . 'view-wwp-notice-bar-lite.php';

        }

    }

    /**
     * This will set on what pages the notice bar will be visible
     * 
     * @since 2.1.2
     * @return boolean
     */
    public function noticebar_lite_has_allowed_pages(){
        $allowed_pages   = apply_filters('wwp_noticebar_lite_allowed_pages', array('wholesale-suite', 'help-page', 'about-page'));

        if( isset($_GET['page']) && in_array( $_GET['page'], $allowed_pages) ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Has WWS Premiums
     * 
     * We check if any wws (WWPP, LC, OF) premiums are activated
     * 
     * @since 2.1.2
     * @access public
     * @return boolean Default value is false
     */
    public static function has_wws_premiums(){
        $has_premiums = false;

        // If WWPP or WWOF or WWLC is activated we return true
        if(WWP_Helper_Functions::is_wwpp_active() || WWP_Helper_Functions::is_wwlc_active() || WWP_Helper_Functions::is_wwof_active()){
            $has_premiums = true;
        }

        return $has_premiums;
    }

    /**
     * Execute model
     * 
     * @since 2.1.2
     * @access public
     */
    public function run(){

        add_action('in_admin_header', array($this, 'show_wwp_notice_bar_lite'), 10);  

    }
}