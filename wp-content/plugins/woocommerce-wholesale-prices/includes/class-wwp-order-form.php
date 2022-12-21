<?php
if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class WWP_Order_Form
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of WWP_Order_Form.
     *
     * @since 1.14
     * @access private
     * @var WWP_Order_Form
     */
    private static $_instance;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * WWP_Order_Form constructor.
     *
     * @since 1.14
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Order_Form model.
     */
    public function __construct($dependencies = array())
    {
        // Nothing to see here yet
    }

    /**
     * Ensure that only one instance of WWP_Order_Form is loaded or can be loaded (Singleton Pattern).
     *
     * @since 1.14
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Order_Form model.
     * @return WWP_Order_Form
     */
    public static function instance($dependencies = array())
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self($dependencies);
        }

        return self::$_instance;
    }

    /**
     * View for Wholesale Order Form page.
     *
     * @since 1.14
     * @access public
     */
    public function view_wholesale_order_form_page()
    {
        require_once WWP_VIEWS_PATH . 'view-wwp-order-form.php';
    }

    /**
     * Register new order form menu item if WWOF is not installed/active
     *
     * @since 1.14
     * @access public
     */
    public function register_order_form_page_menu()
    {
        // Test if order form plugin is not installed or if it is, if it's not active
        if (
            !WWP_Helper_Functions::is_wwof_installed() ||
            (WWP_Helper_Functions::is_wwof_installed() && !WWP_Helper_Functions::is_wwof_active())
        ) {
            add_submenu_page(
                'wholesale-suite',
                __('Order Form', 'woocommerce-wholesale-prices'),
                __('Order Form', 'woocommerce-wholesale-prices'),
                'manage_options',
                'order-forms',
                array($this, 'view_wholesale_order_form_page'),
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
                    'id'        => 'wwp-order-form-page',
                    'screen_id' => 'wholesale_page_order-forms',
                    'title'     => __('Wholesale Order Form', 'woocommerce-wholesale-prices'),
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
        // Add a new submenu under the WooCommerce menu for Order Form
        add_action('admin_menu', array($this, 'register_order_form_page_menu'), 99);

        // Add WC navigation bar to page
        add_action('init', array($this, 'wc_navigation_bar'));
    }
}
