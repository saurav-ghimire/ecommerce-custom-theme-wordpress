<?php
if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class WWP_Help_Page
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of WWP_Help_Page.
     *
     * @since 2.1.1
     * @access private
     * @var WWP_Help_Page
     */
    private static $_instance;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * WWP_Help_Page constructor.
     *
     * @since 2.1.1
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Help_Page model.
     */
    public function __construct($dependencies = array())
    {
        // Nothing to see here yet
    }

    /**
     * Ensure that only one instance of WWP_Help_Page is loaded or can be loaded (Singleton Pattern).
     *
     * @since 2.1.1
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Help_Page model.
     * @return WWP_Help_Page
     */
    public static function instance($dependencies = array())
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self($dependencies);
        }

        return self::$_instance;
    }

    /**
     * View for Wholesale Help page.
     *
     * @since 2.1.1
     * @access public
     */
    public function view_help_page()
    {
        require_once WWP_VIEWS_PATH . 'view-wwp-help-page.php';
    }

    /**
     * Register new Help page menu item
     *
     * @since 2.1.1
     * @access public
     */
    public function register_help_page_menu()
    {

        add_submenu_page(
            'wholesale-suite',
            __('Help', 'woocommerce-wholesale-prices'),
            __('Help', 'woocommerce-wholesale-prices'),
            'manage_options',
            'help-page',
            array($this, 'view_help_page'),
            10
        );

    }

    /**
     * Integration of WC Navigation Bar.
     *
     * @since 2.1.1
     * @access public
     */
    public function wc_navigation_bar()
    {
        if (function_exists('wc_admin_connect_page')) {
            wc_admin_connect_page(
                array(
                    'id'        => 'wwp-help-page',
                    'screen_id' => 'wholesale_page_help-page',
                    'title'     => __('Help Page', 'woocommerce-wholesale-prices'),
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
     * @since 2.1.1
     * @access public
     */
    public function run()
    {

        // Add a new submenu under the Wholesale menu for Help page
        add_action('admin_menu', array($this, 'register_help_page_menu'), 99);

        // Add WC navigation bar to page
        add_action('init', array($this, 'wc_navigation_bar'));
    }
}
