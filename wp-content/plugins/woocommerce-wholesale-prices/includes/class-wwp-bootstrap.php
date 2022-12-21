<?php if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly

if ( ! class_exists( 'WWP_Bootstrap' ) ) {

    /**
     * Model that houses the logic of bootstrapping the plugin.
     *
     * @since 1.3.0
     */
    class WWP_Bootstrap {


        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
         */

        /**
         * Property that holds the single main instance of WWP_Bootstrap.
         *
         * @since 1.3.0
         * @access private
         * @var WWP_Bootstrap
         */
        private static $_instance;

        /**
         * Model that houses the logic of retrieving information relating to wholesale role/s of a user.
         *
         * @since 1.3.0
         * @access private
         * @var WWP_Wholesale_Roles
         */
        private $_wwp_wholesale_roles;

        /**
         * Model that houses the logic of retrieving information relating to wholesale price to show to non wholesale customers.
         *
         * @since 1.16.1
         * @access private
         * @var WWP_Wholesale_Prices_For_Non_Wholesale_Customers
         */
        private $_wwp_wholesale_price_for_non_wholesale_customers;

        /**
         * Current WWP version.
         *
         * @since 1.3.1
         * @access private
         * @var int
         */
        private $_wwp_current_version;

        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
         */

        /**
         * WWP_Bootstrap constructor.
         *
         * @since 1.3.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Bootstrap model.
         */
        public function __construct( $dependencies ) {
            $this->_wwp_wholesale_roles                             = $dependencies['WWP_Wholesale_Roles'];
            $this->_wwp_current_version                             = $dependencies['WWP_CURRENT_VERSION'];
            $this->_wwp_wholesale_price_for_non_wholesale_customers = $dependencies['WWP_Wholesale_Prices_For_Non_Wholesale_Customers'];

        }

        /**
         * Ensure that only one instance of WWP_Bootstrap is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.3.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Bootstrap model.
         * @return WWP_Bootstrap
         */
        public static function instance( $dependencies ) {
            if ( ! self::$_instance instanceof self ) {
                self::$_instance = new self( $dependencies );
            }

            return self::$_instance;

        }

        /*
        |------------------------------------------------------------------------------------------------------------------
        | Internationalization and Localization
        |------------------------------------------------------------------------------------------------------------------
         */

        /**
         * Load plugin text domain.
         *
         * @since 1.2.0
         * @since 1.3.0 Refactor codebase and move to its dedicated model.
         * @access public
         */
        public function load_plugin_text_domain() {
            load_plugin_textdomain( 'woocommerce-wholesale-prices', false, WWP_PLUGIN_BASE_PATH . 'languages/' );

        }

        /*
        |------------------------------------------------------------------------------------------------------------------
        | Bootstrap/Shutdown Functions
        |------------------------------------------------------------------------------------------------------------------
         */

        /**
         * Plugin activation hook callback.
         *
         * @since 1.0.0
         * @since 1.2.9 Renamed from 'init' to 'activate'. Also add option to indicate that activation code has been successfully triggered. Flush rewrite rules.
         * @since 1.3.0 Add multi-site support
         * @access public
         *
         * @param boolean $network_wide Flag that determines if the plugin is activated in a multi-site environment.
         */
        public function activate( $network_wide ) {
            global $wpdb;

            if ( is_multisite() ) {

                if ( $network_wide ) {

                    // get ids of all sites
                    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                    foreach ( $blog_ids as $blog_id ) {

                        switch_to_blog( $blog_id );
                        $this->_activate( $blog_id );

                    }

                    restore_current_blog();

                } else {
                    $this->_activate( $wpdb->blogid );
                }
                // activated on a single site, in a multi-site

            } else {
                $this->_activate( $wpdb->blogid );
            }
            // activated on a single site
        }

        /**
         * Plugin activation codebase.
         *
         * @since 1.3.0
         * @since 1.3.1 Save plugin version.
         * @since 1.5.0 Trigger cron to show a pop up message requesting a review.
         * @since 1.11  If default wholesale role is edited before, we retain the edited name or description on plugin re-activation.
         * @access private

         * @param int $blog_id Site id.
         */
        private function _activate( $blog_id ) {
            global $wc_wholesale_prices;

            $wholesale_roles     = $wc_wholesale_prices->wwp_wholesale_roles->getAllRegisteredWholesaleRoles();
            $wholesale_role_name = __( 'Wholesale Customer', 'woocommerce-wholesale-prices' );

            if ( isset( $wholesale_roles['wholesale_customer'] ) ) {
                $wholesale_role_name = $wholesale_roles['wholesale_customer']['roleName'];
            }

            // Add plugin custom roles and capabilities
            $this->_wwp_wholesale_roles->addCustomRole( 'wholesale_customer', $wholesale_role_name );
            $this->_wwp_wholesale_roles->addCustomCapability( 'wholesale_customer', 'have_wholesale_price' );

            // If wholesale customer doesn't exist
            if ( ! isset( $wholesale_roles['wholesale_customer'] ) ) {
                $this->_wwp_wholesale_roles->registerCustomRole(
                    'wholesale_customer',
                    $wholesale_role_name,
                    array(
                        'desc' => __( 'This is the main wholesale user role.', 'woocommerce-wholesale-prices' ),
                        'main' => true,
                    )
                );
            }

            // Review Request
            if ( ! wp_next_scheduled( WWP_CRON_REQUEST_REVIEW ) ) {
                wp_schedule_single_event( strtotime( '+14 days' ), WWP_CRON_REQUEST_REVIEW );
            }

            // Instal ACFWF notice
            if ( ! wp_next_scheduled( WWP_CRON_INSTALL_ACFWF_NOTICE ) ) {
                wp_schedule_single_event( strtotime( '+30 days' ), WWP_CRON_INSTALL_ACFWF_NOTICE );
            }

            flush_rewrite_rules();

            update_option( 'wwp_option_activation_code_triggered', 'yes' );

            update_option( 'wwp_option_installed_version', $this->_wwp_current_version );

            // Getting Started Notice
            if ( ! get_option( 'wwp_admin_notice_getting_started_show', false ) ) {
                update_option( 'wwp_admin_notice_getting_started_show', 'yes' );
            }

            // Default Wholesale Price Text
            if ( ! get_option( 'wwpp_settings_wholesale_price_title_text', false ) ) {
                update_option( 'wwpp_settings_wholesale_price_title_text', 'Wholesale Price:' );
            }

            // Init Admin Note Crons on Plugin Activation
            // Check if WC Admin is active in WC core (4.0 and up) or
            // If WC Admin is active as external plugin.
            if ( WWP_Helper_Functions::is_wc_admin_active() ) {
                WWP_Store_Owner_Tips::init_cron_hook();
                WWP_WWS_Bundle::init_cron_hook();
                WWP_WWS_Youtube::init_cron_hook();
            }

            wc_delete_product_transients();

            // Add settings options for show wholesale prices to non wholesale customers
            $this->_wwp_wholesale_price_for_non_wholesale_customers->register_settings_field_options();
        }

        /**
         * Plugin deactivation hook callback.
         *
         * @since 1.0.0
         * @since 1.2.9 Renamed from 'terminate' to 'deactivate'. Flush rewrite rules.
         * @since 1.3.0 Add multi-site support.
         * @access public
         *
         * @param boolean $network_wide Flag that determines if the plugin is activated in a multi-site environment.
         */
        public function deactivate( $network_wide ) {
            global $wpdb;

            // check if it is a multisite network
            if ( is_multisite() ) {

                // check if the plugin has been activated on the network or on a single site
                if ( $network_wide ) {

                    // get ids of all sites
                    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                    foreach ( $blog_ids as $blog_id ) {

                        switch_to_blog( $blog_id );
                        $this->_deactivate( $wpdb->blogid );

                    }

                    restore_current_blog();

                } else {
                    $this->_deactivate( $wpdb->blogid );
                }
                // activated on a single site, in a multi-site

            } else {
                $this->_deactivate( $wpdb->blogid );
            }
            // activated on a single site
        }

        /**
         * Plugin deactivation codebase.
         *
         * @since 1.3.0
         * @since 1.11 Removed unregisterCustomRole(). We need to keep track if default custom wholesale customer role is edited.
         * @access public
         *
         * @param int $blog_id Site id.
         */
        private function _deactivate( $blog_id ) {
            // Remove plugin custom roles and capabilities
            $this->_wwp_wholesale_roles->removeCustomCapability( 'wholesale_customer', 'have_wholesale_price' );
            $this->_wwp_wholesale_roles->removeCustomRole( 'wholesale_customer' );

            flush_rewrite_rules();

            wc_delete_product_transients();

        }

        /**
         * Method to initialize a newly created site in a multi site set up.
         *
         * @since 1.3.0
         * @access public
         *
         * @param int    $blog_id Blog ID.
         * @param int    $user_id User ID.
         * @param string $domain  Site domain.
         * @param string $path    Site path.
         * @param int    $site_id Site ID. Only relevant on multi-network installs.
         * @param array  $meta    Meta data. Used to set initial site options.
         */
        public function new_mu_site_init( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
            if ( is_plugin_active_for_network( 'woocommerce-wholesale-prices/woocommerce-wholesale-prices.plugin.php' ) ) {

                switch_to_blog( $blog_id );
                $this->_activate( $blog_id );
                restore_current_blog();

            }

        }

        /**
         * Plugin initializaton.
         *
         * @since 1.2.9
         * @since 1.3.0 Add multi-site support.
         * @since 1.3.1 Check if plugin installed version is same as plugin current version.
         */
        public function initialize() {
            // Check if activation has been triggered, if not trigger it
            // Activation codes are not triggered if plugin dependencies are not present and this plugin is activated.
            if ( version_compare( get_option( 'wwp_option_installed_version', false ), $this->_wwp_current_version, '!=' ) || get_option( 'wwp_option_activation_code_triggered', false ) !== 'yes' ) {

                if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
                    require_once ABSPATH . '/wp-admin/includes/plugin.php';
                }

                $network_wide = is_plugin_active_for_network( 'woocommerce-wholesale-prices/woocommerce-wholesale-prices.plugin.php' );
                $this->activate( $network_wide );

            }

        }

        /*
        |------------------------------------------------------------------------------------------------------------------
        | Plugin Custom Action Links
        |------------------------------------------------------------------------------------------------------------------
         */

        /**
         * Add plugin listing custom action link ( settings ).
         *
         * @since 1.0.1
         * @since 1.4.0 Refactor codebase and move to its proper model.
         * @access public
         *
         * @param array  $links Array of links.
         * @param string $file  Plugin basename.
         * @return array Filtered array of links.
         */
        public function add_plugin_listing_custom_action_links( $links, $file ) {
            if ( $file == plugin_basename( WWP_PLUGIN_PATH . 'woocommerce-wholesale-prices.bootstrap.php' ) ) {

                $settings_link = '<a href="admin.php?page=wc-settings&tab=wwp_settings">' . __( 'Settings', 'woocommerce-wholesale-prices' ) . '</a>';
                array_unshift( $links, $settings_link );

                if ( ! WWP_Helper_Functions::is_plugin_active( 'woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php' ) ) {

                    $upgrade_link = '<a href="https://wholesalesuiteplugin.com/bundle/?utm_source=wwp&utm_medium=upsell&utm_campaign=pluginpagebundlelink" target="_blank"><b>' . __( 'Upgrade To Premium', 'woocommerce-wholesale-prices' ) . '</b></a>';
                    array_unshift( $links, $upgrade_link );

                }

                $getting_started          = '<a href="https://wholesalesuiteplugin.com/kb/woocommerce-wholesale-prices-free-plugin-getting-started-guide/?utm_source=wwp&utm_medium=kb&utm_campaign=wwpgettingstarted" target="_blank">' . __( 'Getting Started', 'woocommerce-wholesale-prices' ) . '</a>';
                $links['getting_started'] = $getting_started;

            }

            return $links;

        }

        /**
         * Getting Started notice on plugin activation.
         *
         * @since 1.11
         * @since 1.11.1 * Only show Getting Started only if user is admin.
         *               * Once dismissed it should not show on re-activation.
         *               * Only show if WWPP is active and is lower than 1.24 ( we will show a different notice for WWPP replacing WWP )
         *               * Only show on plugins and woocommerge pages.
         * @since 1.11.5 Remove 1.24 flag checks.
         * @since 2.0 Display notices in the new top level menu.
         * @access public
         */
        public function getting_started_notice() {
            // Check if current user is admin or shop manager
            // Check if getting started option is 'yes'
            if ( ( current_user_can( 'administrator' ) || current_user_can( 'shop_manager' ) ) && get_option( 'wwp_admin_notice_getting_started_show' ) === 'yes' ) {

                $screen = get_current_screen();

                // Check if WWS license page
                // Check if products pages
                // Check if woocommerce pages ( wc, products, analytics )
                // Check if plugins page
                if (
                    in_array( $screen->id, array( 'wholesale_page_order-forms', 'wholesale_page_wholesale-settings', 'settings_page_wwc_license_settings', 'wholesale_page_wwpp-wholesale-roles-page', 'wholesale_page_wwc_license_settings' ) ) ||
                    $screen->post_type === 'product' || in_array( $screen->parent_base, array( 'woocommerce', 'plugins' ) )
                ) {?>

                    <div class="updated notice wwp-getting-started">
                        <p><img src="<?php echo WWP_IMAGES_URL; ?>wholesale-suite-activation-notice-logo.png" alt="" /></p>
                        <p><?php _e( 'Thank you for choosing Wholesale Suite &#8211; the most complete wholesale solution for building wholesale sales into your existing WooCommerce driven store.', 'woocommerce-wholesale-prices' ); ?>
                        <p><?php _e( 'The free WooCommerce Wholesale Prices plugin lets you set wholesale pricing for wholesale level customers. Would you like to find out how to drive it?', 'woocommerce-wholesale-prices' ); ?>
                        <p><a href="https://wholesalesuiteplugin.com/kb/woocommerce-wholesale-prices-free-plugin-getting-started-guide/?utm_source=wwp&utm_medium=kb&utm_campaign=wwpgettingstarted"
                                target="_blank">
                                <?php _e( 'Read the Getting Started guide', 'woocommerce-wholesale-prices' ); ?>
                                <span class="dashicons dashicons-arrow-right-alt" style="margin-top: 5px"></span>
                            </a></p>
                        <button type="button" class="notice-dismiss"><span
                                class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'woocommerce-wholesale-prices' ); ?></span></button>
                    </div>
                    <?php

                }
}

        }

        /**
         * Hide getting started notice on close.
         * Attached to wp_ajax_wwp_getting_started_notice_hide
         *
         * @since 1.11
         * @access public
         */
        public function wwp_getting_started_notice_hide() {
            if ( ! defined( 'DOING_AJAX' ) && ! wp_verify_nonce( $_POST['nonce'], 'wwp_getting_started_nonce' ) ) {
                // Security check failure.
                return;
            }

            update_option( 'wwp_admin_notice_getting_started_show', 'no' );
            wp_send_json( array( 'status' => 'success' ) );

        }

        /*
        |------------------------------------------------------------------------------------------------------------------
        | Execute Model
        |------------------------------------------------------------------------------------------------------------------
         */

        /**
         * Execute model.
         *
         * @since 1.3.0
         * @access public
         */
        public function run() {
            // Load Plugin Text Domain
            add_action( 'plugins_loaded', array( $this, 'load_plugin_text_domain' ) );

            register_activation_hook( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'woocommerce-wholesale-prices' . DIRECTORY_SEPARATOR . 'woocommerce-wholesale-prices.bootstrap.php', array( $this, 'activate' ) );
            register_deactivation_hook( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'woocommerce-wholesale-prices' . DIRECTORY_SEPARATOR . 'woocommerce-wholesale-prices.bootstrap.php', array( $this, 'deactivate' ) );

            // Execute plugin initialization ( plugin activation ) on every newly created site in a multi site set up
            add_action( 'wpmu_new_blog', array( $this, 'new_mu_site_init' ), 10, 6 );

            // Initialize Plugin
            add_action( 'init', array( $this, 'initialize' ) );

            // Settings
            add_filter( 'plugin_action_links', array( $this, 'add_plugin_listing_custom_action_links' ), 10, 2 );

            // Getting Started notice
            add_action( 'admin_notices', array( $this, 'getting_started_notice' ), 10 );
            add_action( 'wp_ajax_wwp_getting_started_notice_hide', array( $this, 'wwp_getting_started_notice_hide' ) );

        }

    }

}
