<?php
if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class WWP_Lead_Capture
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of WWP_Lead_Capture.
     *
     * @since 1.14
     * @access private
     * @var WWP_Lead_Capture
     */
    private static $_instance;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * WWP_Lead_Capture constructor.
     *
     * @since 1.14
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Lead_Capture model.
     */
    public function __construct($dependencies = array())
    {
        // Nothing to see here yet
    }

    /**
     * Ensure that only one instance of WWP_Lead_Capture is loaded or can be loaded (Singleton Pattern).
     *
     * @since 1.14
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Lead_Capture model.
     * @return WWP_Lead_Capture
     */
    public static function instance($dependencies = array())
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self($dependencies);
        }

        return self::$_instance;
    }

    /**
     * View for Wholesale Lead Capture page.
     *
     * @since 1.14
     * @access public
     */
    public function view_wholesale_lead_capture_page()
    {
        require_once WWP_VIEWS_PATH . 'view-wwp-lead-capture.php';
    }

    /**
     * Register new lead capture menu item if WWLC is not installed/active
     *
     * @since 1.14
     * @access public
     */
    public function register_lead_capture_page_menu()
    {
        // Test if lead capture plugin is not installed or if it is, if it's not active
        if (
            !WWP_Helper_Functions::is_wwlc_installed() ||
            (WWP_Helper_Functions::is_wwlc_installed() && !WWP_Helper_Functions::is_wwlc_active())
        ) {
            add_submenu_page(
                'wholesale-suite',
                __('Lead Capture', 'woocommerce-wholesale-prices'),
                __('Lead Capture', 'woocommerce-wholesale-prices'),
                apply_filters('wwp_can_access_admin_menu_cap', 'manage_options'),
                'wwp-lead-capture-page',
                array($this, 'view_wholesale_lead_capture_page'),
                3
            );
        }
    }

    /**
     * Integration of WC Navigation Bar.
     *
     * @since 1.14
     * @access public
     */
    public function wc_navigation_bar()
    {
        if (function_exists('wc_admin_connect_page')) {
            wc_admin_connect_page(
                array(
                    'id'        => 'wwp-lead-capture-page',
                    'screen_id' => 'wholesale_page_wwp-lead-capture-page',
                    'title'     => __('Wholesale Lead Capture', 'woocommerce-wholesale-prices'),
                )
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Execute Model
    |--------------------------------------------------------------------------
     */

    /**
     * Execute model.
     *
     * @since 1.14
     * @access public
     */
    public function run()
    {
        // Add a new submenu under the WooCommerce menu for Lead Capture
        add_action('admin_menu', array($this, 'register_lead_capture_page_menu'), 99);

        // Add WC navigation bar to page
        add_action('init', array($this, 'wc_navigation_bar'));
    }
}
