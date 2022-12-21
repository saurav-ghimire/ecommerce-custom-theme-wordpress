<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

if (!class_exists('WWP_Admin_Menu')) {

    /**
     * Model that houses logic relating to caching.
     *
     * @since 2.0
     */
    class WWP_Admin_Menu
    {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
         */

        /**
         * Property that holds the single main instance of WWP_Admin_Menu.
         *
         * @since 2.0
         * @access private
         * @var WWP_Admin_Menu
         */
        private static $_instance;

        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
         */

        /**
         * WWP_Admin_Menu constructor.
         *
         * @since 2.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Admin_Menu model.
         */
        public function __construct($dependencies)
        {}

        /**
         * Ensure that only one instance of WWP_Admin_Menu is loaded or can be loaded (Singleton Pattern).
         *
         * @since 2.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Admin_Menu model.
         * @return WWP_Admin_Menu
         */
        public static function instance($dependencies)
        {

            if (!self::$_instance instanceof self) {
                self::$_instance = new self($dependencies);
            }

            return self::$_instance;

        }

        /**
         * Register Wholesale Top Tevel Menu
         *
         * @since 2.0
         * @access public
         */
        public function register_page()
        {

            // Only admin can see the top level menu
            if (!current_user_can('administrator')) {
                return;
            }

            global $submenu, $wc_wholesale_prices;

            $wws_icon = WWP_IMAGES_URL . 'wholesale-suite-icon.svg';

            // Wholesale Top Level Menu
            add_menu_page(__('Wholesale', 'woocommerce-wholesale-prices'), __('Wholesale', 'woocommerce-wholesale-prices'), 'manage_options', 'wholesale-suite', array($this, 'wholesale_dashboard'), $wws_icon, '55.5');

            // Reports Submenu
            if (WWP_Helper_Functions::is_plugin_active('woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php')) {
                add_submenu_page('wholesale-suite', __('Reports', 'woocommerce-wholesale-prices'), __('Reports', 'woocommerce-wholesale-prices'), 'manage_options', 'wholesale-reports', array($this, 'wholesale_reports'), 5);
            }

            // Orders Submenu
            add_submenu_page('wholesale-suite', __('Orders', 'woocommerce-wholesale-prices'), __('Orders', 'woocommerce-wholesale-prices'), 'manage_options', 'wholesale-orders', array($this, 'wholesale_orders'), 1);

            // Settings Submenu
            add_submenu_page('wholesale-suite', __('Settings', 'woocommerce-wholesale-prices'), __('Settings', 'woocommerce-wholesale-prices'), 'manage_options', 'wholesale-settings', array($this, 'wholesale_settings'), 7);

            if ($wc_wholesale_prices->wwp_dashboard->is_wholesale_dashboard_disabled()) {
                $submenu['wholesale-suite'][0][0] = __('Dashboard (Disabled)', 'woocommerce-wholesale-prices');
            } else {
                $submenu['wholesale-suite'][0][0] = __('Dashboard', 'woocommerce-wholesale-prices');
            }

        }

        /**
         * Wholesale Dashboard react element wrapper
         *
         * @since 2.0
         * @access public
         */
        public function wholesale_dashboard()
        {

            global $wc_wholesale_prices;

            if ($wc_wholesale_prices->wwp_dashboard->is_wholesale_dashboard_disabled()) {
                echo "<style>#wpcontent{background:#fff;}</style>";
                echo '<div class="wrap">';
                echo '<h3 style="margin-left: 20px;">Dashboard Disabled</h3>';
                echo '</div>';
            } else {
                echo '<div class="wrap">';
                echo '<div id="wholesale-dashboard"></div>';
                echo '</div>';
            }

        }

        /**
         * Redirect Wholesale > Reports submenu to WC Reports page
         *
         * @since 2.0
         * @access public
         */
        public function wholesale_reports()
        {
            wp_redirect(admin_url('admin.php?page=wc-reports&tab=wwpp_reports'));
            exit;
        }

        /**
         * Redirect Wholesale > Orders submenu to WC Orders page
         *
         * @since 2.0
         * @access public
         */
        public function wholesale_orders()
        {
            wp_redirect(admin_url('edit.php?post_status=all&post_type=shop_order&wwpp_fbwr=all_wholesale_orders'));
            exit;
        }

        /**
         * Display links to old settings.
         *
         * @since 2.0
         * @access public
         */
        public function wholesale_settings()
        {

            if (!WWP_Helper_Functions::is_wwof_active() && !WWP_Helper_Functions::is_wwlc_active()) {
                wp_redirect(admin_url('admin.php?page=wc-settings&tab=wwp_settings'));
                exit;
            }

            echo '<div class="wrap">';

            echo '<h1>' . __('Settings', 'woocommerce-wholesale-prices') . '</h1>';
            echo '<br/>';
            echo '<p style="font-size:20px;"><a href="' . admin_url('admin.php?page=wc-settings&tab=wwp_settings') . '">' . __('Wholesale Prices Settings Page', 'woocommerce-wholesale-prices') . '</a></p>';

            if (WWP_Helper_Functions::is_wwof_active()) {
                echo '<p style="font-size:20px;"><a href="' . admin_url('admin.php?page=wc-settings&tab=wwof_settings') . '">' . __('Wholesale Order Form Settings Page', 'woocommerce-wholesale-prices') . '</a></p>';
            }

            if (WWP_Helper_Functions::is_wwlc_active()) {
                echo '<p style="font-size:20px;"><a href="' . admin_url('admin.php?page=wc-settings&tab=wwlc_settings') . '">' . __('Wholesale Lead Capture Settings Page', 'woocommerce-wholesale-prices') . '</a></p>';
            }

            echo '</div>';

        }

        /**
         * Integration of WC Navigation Bar.
         *
         * @since 2.0
         * @access public
         */
        public function wc_navigation_bar()
        {

            if (function_exists('wc_admin_connect_page')) {

                wc_admin_connect_page(
                    array(
                        'id'        => 'wholesale-suite-settings',
                        'screen_id' => 'wholesale_page_wholesale-settings',
                        'title'     => __('Settings', 'woocommerce-wholesale-prices'),
                    )
                );

            }

        }

        /**
         * Removes admin notices displaying in the dashboard.
         *
         * @since 2.0
         * @access public
         */
        public function remove_admin_notices_in_dashboard()
        {

            $screen = get_current_screen();

            if ($screen && $screen->id == 'toplevel_page_wholesale-suite') {
                remove_all_actions('admin_notices');
            }

        }

        /*
        |-------------------------------------------------------------------------------------------------------------------
        | Execute Model
        |-------------------------------------------------------------------------------------------------------------------
         */

        /**
         * Execute model.
         *
         * @since 2.0
         * @access public
         */
        public function run()
        {

            add_action('admin_menu', array($this, 'register_page'), 98);
            add_action('init', array($this, 'wc_navigation_bar'));

            // Removes admin notices in dashboard
            add_action('admin_head', array($this, 'remove_admin_notices_in_dashboard'), 1);

        }

    }

}
