<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

if (!class_exists('WWP_WWS_License_Manager')) {

    class WWP_WWS_License_Manager
    {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
         */

        /**
         * Property that holds the single main instance of WWP_WWS_License_Manager.
         *
         * @since 2.1.3
         * @access private
         * @var WWP_WWS_License_Manager
         */
        private static $_instance;

        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
         */

        /**
         * WWP_WWS_License_Manager constructor.
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_WWS_License_Manager model.
         *
         * @access public
         * @since 2.1.3
         */
        public function __construct($dependencies)
        {

        }

        /**
         * Ensure that only one instance of WWP_WWS_License_Manager is loaded or can be loaded (Singleton Pattern).
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_WWS_License_Manager model.
         *
         * @return WWP_WWS_License_Manager
         * @since 2.1.3
         */
        public static function instance($dependencies = null)
        {

            if (!self::$_instance instanceof self) {
                self::$_instance = new self($dependencies);
            }

            return self::$_instance;

        }

        /*
        |---------------------------------------------------------------------------------------------------------------
        | WooCommerce WholeSale Suite License Settings
        |---------------------------------------------------------------------------------------------------------------
         */

        /**
         * Register general wws license settings page in a multi-site environment.
         *
         * @since 2.1.3
         * @access public
         */
        public function register_ms_wws_licenses_settings_menu()
        {

            /*
             * Since we don't have a primary plugin to add this license settings, we have to check first if other plugins
             * belonging to the WWS plugin suite has already added a license settings page.
             */
            if (!defined('WWS_LICENSE_SETTINGS_PAGE')) {

                if (!defined('WWS_LICENSE_SETTINGS_DEFAULT_PLUGIN')) {
                    define('WWS_LICENSE_SETTINGS_DEFAULT_PLUGIN', 'wwpp');
                }

                add_menu_page(
                    __("WWS License", "woocommerce-wholesale-prices"),
                    __("WWS License", "woocommerce-wholesale-prices"),
                    "manage_sites",
                    "wws-ms-license-settings",
                    array(self::instance(), "generate_wws_licenses_settings_page")
                );

                // We define this constant with the text domain of the plugin who added the settings page.
                define('WWS_LICENSE_SETTINGS_PAGE', 'woocommerce-wholesale-prices');

            }

        }

        /**
         * Register general wws license settings page.
         *
         * @since 2.1.3
         */
        public function register_wws_license_settings_menu()
        {

            if (!defined('WWS_LICENSE_SETTINGS_PAGE')) {

                /**
                 * Since we don't have a primary plugin to add this license settings, we have to check first if other plugins
                 * belonging to the WWS plugin suite has already added a license settings page.
                 */

                if (!defined('WWS_LICENSE_SETTINGS_DEFAULT_PLUGIN')) {
                    define('WWS_LICENSE_SETTINGS_DEFAULT_PLUGIN', 'wwpp');
                }

                // We define this constant with the text domain of the plugin who added the settings page.
                define('WWS_LICENSE_SETTINGS_PAGE', 'woocommerce-wholesale-prices');

                // Transfer License to Wholesale Top Level Menu
                if (method_exists('WWP_Helper_Functions', 'is_wwp_v2') && WWP_Helper_Functions::is_wwp_v2()) {

                    // Register WWS Settings Menu
                    add_submenu_page(
                        'wholesale-suite', // Settings
                        __('License', 'woocommerce-wholesale-prices'),
                        __('License', 'woocommerce-wholesale-prices'),
                        'manage_options',
                        'wwc_license_settings',
                        array(self::instance(), "generate_wws_licenses_settings_page"),
                        7
                    );
                    return;
                }

                // Register WWS Settings Menu
                add_submenu_page(
                    'options-general.php', // Settings
                    __('WooCommerce WholeSale Suite License Settings', 'woocommerce-wholesale-prices'),
                    __('WWS License', 'woocommerce-wholesale-prices'),
                    'manage_options',
                    'wwc_license_settings',
                    array(self::instance(), "generate_wws_licenses_settings_page")
                );

            }

        }

        /**
         * Add general WWS license markup.
         *
         * @since 2.1.3
         * @access public
         */
        public function generate_wws_licenses_settings_page()
        {

            require_once WWP_PLUGIN_PATH . 'views/wws-license-settings/view-wws-license-settings-page.php';

        }

        /**
         * Add WWP license header markup.
         *
         * @since 2.1.3
         * @access public
         */
        public function wwpp_license_tab()
        {

            ob_start();

            if (isset($_GET['tab'])) {
                $tab = $_GET['tab'];
            } else {
                $tab = WWS_LICENSE_SETTINGS_DEFAULT_PLUGIN;
            }

            if (is_multisite()) {

                $wwp_license_settings_url = get_site_url() . "/wp-admin/network/admin.php?page=wws-ms-license-settings&tab=wwpp";

            } else {

                $wwp_license_settings_url = get_site_url() . "/wp-admin/options-general.php?page=wwc_license_settings&tab=wwpp";

                if (method_exists('WWP_Helper_Functions', 'is_wwp_v2') && WWP_Helper_Functions::is_wwp_v2()) {
                    $wwp_license_settings_url = get_site_url() . "/wp-admin/admin.php?page=wwc_license_settings&tab=wwpp";
                }

            }?>

			<a href="<?php echo $wwp_license_settings_url; ?>" class="nav-tab <?php echo ($tab == "wwpp") ? "nav-tab-active" : ""; ?>"><?php _e('Wholesale Prices', 'woocommerce-wholesale-prices');?></a>

			<?php echo ob_get_clean();

        }

        /**
         * Add WWOF license header markup.
         *
         * @since 2.1.3
         * @access public
         */
        public function wwof_license_tab()
        {

            ob_start();

            if (isset($_GET['tab']) && $_GET['tab'] == 'wwof') {
                $tab = $_GET['tab'];
            } else {
                $tab = '';
            }

            if (is_multisite()) {

                $wwof_license_settings_url = get_site_url() . "/wp-admin/network/admin.php?page=wws-ms-license-settings&tab=wwof";

            } else {

                $wwof_license_settings_url = get_site_url() . "/wp-admin/options-general.php?page=wwc_license_settings&tab=wwof";

                if (WWP_Helper_Functions::is_wwp_v2()) {
                    $wwof_license_settings_url = get_site_url() . "/wp-admin/admin.php?page=wwc_license_settings&tab=wwof";
                }

            } ?>

            <a href="<?php echo $wwof_license_settings_url; ?>" class="nav-tab <?php echo ($tab == "wwof") ? "nav-tab-active" : ""; ?>"><?php _e('Wholesale Ordering', 'woocommerce-wholesale-prices');?></a>

			<?php echo ob_get_clean();

        }

        /**
         * Add WWLC license header markup.
         *
         * @since 2.1.3
         * @access public
         */
        public function wwlc_license_tab()
        {

            ob_start();

            if (isset($_GET['tab']) && $_GET['tab'] == 'wwlc') {
                $tab = $_GET['tab'];
            } else {
                $tab = '';
            }

            global $wp;

            if (is_multisite()) {

                $wwlc_license_settings_url = get_site_url() . "/wp-admin/network/admin.php?page=wws-ms-license-settings&tab=wwlc";

            } else {

                $wwlc_license_settings_url = get_site_url() . "/wp-admin/options-general.php?page=wwc_license_settings&tab=wwlc";

                if (WWP_Helper_Functions::is_wwp_v2()) {
                    $wwlc_license_settings_url = get_site_url() . "/wp-admin/admin.php?page=wwc_license_settings&tab=wwlc";
                }

            } ?>

            <a href="<?php echo $wwlc_license_settings_url; ?>" class="nav-tab <?php echo ($tab == "wwlc") ? "nav-tab-active" : ""; ?>"><?php _e('Wholesale Lead', 'woocommerce-wholesale-lead-capture');?></a>

			<?php echo ob_get_clean();

        }

        /**
         * Add WWPP license upsell content markup.
         *
         * @since 2.1.3
         * @access public
         */
        public function wwpp_license_content()
        {

            ob_start();

            require_once WWP_PLUGIN_PATH . 'views/wws-license-settings/view-wwpp-license-upsell-content.php';

            echo ob_get_clean();

        }

        /**
         * Add WWLC license upsell content markup.
         *
         * @since 2.1.3
         * @access public
         */
        public function wwlc_license_content()
        {

            ob_start();

            require_once WWP_PLUGIN_PATH . 'views/wws-license-settings/view-wwlc-license-upsell-content.php';

            echo ob_get_clean();

        }

        /**
         * Add WWOF license upsell content markup.
         *
         * @since 2.1.3
         * @access public
         */
        public function wwof_license_content()
        {

            ob_start();

            require_once WWP_PLUGIN_PATH . 'views/wws-license-settings/view-wwof-license-upsell-content.php';

            echo ob_get_clean();

        }

        /**
         * Inserts License and Tab Contents if Premium Plugins are not active
         *
         * @since 2.1.3
         * @since 2.1.4 Bug fix #229
         * 
         * @access public
         */
        public function license_tab_and_contents()
        {
            // WWPP ----------------------------------------------------------------------------------------------------

            if ( !WWP_Helper_Functions::is_wwpp_active() ) {
                
                add_action("wws_action_license_settings_tab", array($this, 'wwpp_license_tab'));
                add_action("wws_action_license_settings_wwpp", array($this, 'wwpp_license_content'));

            } else {

                /**
                 * ! Important:
                 * 
                 * We need to register license menu and content even if WWPP is active but if it is on version 1.27.11.
                 * This version no longer registers license menu and content so its solely the responsibility of WWP to register such.
                 */
                $wwpp_plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php');

                if (version_compare($wwpp_plugin_data['Version'], '1.27.11', '>=')) {

                    add_action("wws_action_license_settings_tab", array($this, 'wwpp_license_tab'));
                    add_action("wws_action_license_settings_wwpp", array($this, 'wwpp_license_content'));

                }

            }

            // WWOF ----------------------------------------------------------------------------------------------------

            if ( !WWP_Helper_Functions::is_wwof_active() ) {

                add_action("wws_action_license_settings_tab", array($this, 'wwof_license_tab'));
                add_action("wws_action_license_settings_wwof", array($this, 'wwof_license_content'));

            } else {

                /**
                 * ! Important:
                 * 
                 * We need to register license menu and content even if WWOF is active but if it is on version 2.0.3.
                 * This version no longer registers license menu and content so its solely the responsibility of WWP to register such.
                 */
                $wwof_plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/woocommerce-wholesale-order-form/woocommerce-wholesale-order-form.bootstrap.php');

                if ( version_compare($wwof_plugin_data['Version'], '2.0.3', '>=') ) {

                    add_action("wws_action_license_settings_tab", array($this, 'wwof_license_tab'));
                    add_action("wws_action_license_settings_wwof", array($this, 'wwof_license_content'));

                }

            }

            // WWLC ----------------------------------------------------------------------------------------------------

            if ( !WWP_Helper_Functions::is_wwlc_active() ) {

                add_action("wws_action_license_settings_tab", array($this, 'wwlc_license_tab'));
                add_action("wws_action_license_settings_wwlc", array($this, 'wwlc_license_content'));

            } else {

                /**
                 * ! Important:
                 * 
                 * We need to register license menu and content even if WWLC is active but if it is on version 1.17.2.
                 * This version no longer registers license menu and content so its solely the responsibility of WWP to register such.
                 */
                $wwlc_plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/woocommerce-wholesale-lead-capture/woocommerce-wholesale-lead-capture.bootstrap.php');

                if ( version_compare($wwlc_plugin_data['Version'], '1.17.2', '>=') ) {

                    add_action("wws_action_license_settings_tab", array($this, 'wwlc_license_tab'));
                    add_action("wws_action_license_settings_wwlc", array($this, 'wwlc_license_content'));

                }

            }

            do_action('wwp_license_tab_and_contents', $this);

        }

        /**
         * Remove duplicate admin menu content when premium plugins are active.
         * Theres an instance where the WWPP is less than 1.27.11, WWOF less than 2.0.3 and WWLC less than 1.17.2 duplicate menu is added.
         * We will register admin menu via the free plugin since 2.1.3.
         *
         * @since 2.1.3
         * @access public
         */
        public function remove_premium_plugins_admin_menu()
        {

            if (WWP_Helper_Functions::is_wwpp_active()) {

                remove_action('admin_menu', array(WWPP_WWS_License_Manager::instance(), 'register_wws_license_settings_menu'), 99);

            }

            if (WWP_Helper_Functions::is_wwof_active()) {

                global $wc_wholesale_order_form;

                remove_action('admin_menu', array($wc_wholesale_order_form->_wwof_license_manager, 'register_wws_license_settings_menu'), 99);

            }

            if (WWP_Helper_Functions::is_wwlc_active()) {

                remove_action('admin_menu', array(WWLC_WWS_License_Manager::instance(), 'register_wws_license_settings_menu'), 99);

            }

        }

        /**
         * Execute model.
         *
         * @since 1.11
         * @access public
         */
        public function run()
        {

            // Remove menu set by premium plugins. We will rely on the menu we register since 2.1.3.
            add_action('init', array($this, 'remove_premium_plugins_admin_menu'));

            if (is_multisite() && get_current_blog_id() === 1) {

                // Add WooCommerce Wholesale Suite License Settings In Multi-Site Environment
                add_action('network_admin_menu', array($this, 'register_ms_wws_licenses_settings_menu'));

                // Add License Tab and Contents
                add_action('init', array($this, 'license_tab_and_contents'));

            } else {

                // Add WooCommerce Wholesale Suite License Menu
                add_action('admin_menu', array($this, 'register_wws_license_settings_menu'), 99);

                // Add License Tab and Contents
                add_action('init', array($this, 'license_tab_and_contents'));

            }

        }

    }

}