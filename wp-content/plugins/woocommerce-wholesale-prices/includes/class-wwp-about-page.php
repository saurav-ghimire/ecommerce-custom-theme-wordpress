<?php
if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class WWP_About_Page
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of WWP_About_Page.
     *
     * @since 2.1.1
     * @access private
     * @var WWP_About_Page
     */
    private static $_instance;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * WWP_About_Page constructor.
     *
     * @since 2.1.1
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_About_Page model.
     */
    public function __construct($dependencies = array())
    {
        // Nothing to see here yet
    }

    /**
     * Ensure that only one instance of WWP_About_Page is loaded or can be loaded (Singleton Pattern).
     *
     * @since 2.1.1
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_About_Page model.
     * @return WWP_About_Page
     */
    public static function instance($dependencies = array())
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self($dependencies);
        }

        return self::$_instance;
    }

    /**
     * View for About page.
     *
     * @since 2.1.1
     * @access public
     */
    public function view_about_page()
    {

        $bundle_installed = WWP_Helper_Functions::is_wwpp_installed() && WWP_Helper_Functions::is_wwof_installed() && WWP_Helper_Functions::is_wwlc_installed() ? true : false;

        require_once WWP_VIEWS_PATH . 'view-wwp-about-page.php';

    }

    /**
     * Register new about page menu
     *
     * @since 2.1.1
     * @access public
     */
    public function register_about_page_menu()
    {

        add_submenu_page(
            'wholesale-suite',
            __('About', 'woocommerce-wholesale-prices'),
            __('About', 'woocommerce-wholesale-prices'),
            'manage_options',
            'about-page',
            array($this, 'view_about_page'),
            9
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
                    'id'        => 'wwp-about-page',
                    'screen_id' => 'wholesale_page_about-page',
                    'title'     => __('About Page', 'woocommerce-wholesale-prices'),
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

        // Add a new submenu under the Wholesale menu for About Page
        add_action('admin_menu', array($this, 'register_about_page_menu'), 99);

        // Add WC navigation bar to page
        add_action('init', array($this, 'wc_navigation_bar'));
    }
}
