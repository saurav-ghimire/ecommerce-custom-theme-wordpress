<?php
if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class WWP_Upgrade_To_Premium_Page
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of WWP_Upgrade_To_Premium_Page.
     *
     * @since 2.1.1
     * @access private
     * @var WWP_Upgrade_To_Premium_Page
     */
    private static $_instance;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * WWP_Upgrade_To_Premium_Page constructor.
     *
     * @since 2.1.1
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Upgrade_To_Premium_Page model.
     */
    public function __construct($dependencies = array())
    {
        // Nothing to see here yet
    }

    /**
     * Ensure that only one instance of WWP_Upgrade_To_Premium_Page is loaded or can be loaded (Singleton Pattern).
     *
     * @since 2.1.1
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Upgrade_To_Premium_Page model.
     * @return WWP_Upgrade_To_Premium_Page
     */
    public static function instance($dependencies = array())
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self($dependencies);
        }

        return self::$_instance;
    }

    /**
     * View for Upgrade to Premium.
     *
     * @since 2.1.1
     * @access public
     */
    public function view_upgrade_to_premium_page()
    {
        require_once WWP_VIEWS_PATH . 'view-wwp-upgrade-upsell.php';
    }

    /**
     * Register new Upgrade to Premium menu
     *
     * @since 2.1.1
     * @access public
     */
    public function register_upgrade_to_premium_page_menu()
    {

        global $submenu;

        if (
            !WWP_Helper_Functions::is_wwp_installed() ||
            (WWP_Helper_Functions::is_wwp_installed() && !WWP_Helper_Functions::is_wwpp_active())
        ) {

            add_submenu_page(
                'wholesale-suite',
                __('Upgrade To Premium', 'woocommerce-wholesale-prices'),
                __('Upgrade To Premium', 'woocommerce-wholesale-prices'),
                'manage_options',
                'upgrade-to-premium-page',
                array($this, 'view_upgrade_to_premium_page'),
                99
            );

            if (isset($submenu['wholesale-suite'])) {
                foreach ($submenu['wholesale-suite'] as $key => $menu) {
                    if (in_array('Upgrade To Premium', $menu)) {
                        $submenu['wholesale-suite'][$key][4] = 'wwp-upgrade-to-premium';
                    }
                }
            }

        }

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
                    'id'        => 'wwp-upgrade-to-premium-page',
                    'screen_id' => 'wholesale_page_upgrade-to-premium-page',
                    'title'     => __('Upgrade To Premium', 'woocommerce-wholesale-prices'),
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

        // Add a new submenu under the Wholesale menu for Upgrade to Premium
        add_action('admin_menu', array($this, 'register_upgrade_to_premium_page_menu'), 99);

        // Add WC navigation bar to page
        add_action('init', array($this, 'wc_navigation_bar'));
    }
}
