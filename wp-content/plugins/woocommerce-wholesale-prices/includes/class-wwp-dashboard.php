<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

if (!class_exists('WWP_Dashboard')) {

    /**
     * Model that houses logic relating to caching.
     *
     * @since 2.0
     */
    class WWP_Dashboard
    {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
         */

        /**
         * Property that holds the single main instance of WWP_Dashboard.
         *
         * @since 2.0
         * @access private
         * @var WWP_Dashboard
         */
        private static $_instance;

        /**
         * Model that houses the logic of retrieving information relating to wholesale role/s of a user.
         *
         * @since 2.0
         * @access private
         * @var WWP_Wholesale_Roles
         */
        private $_wwp_wholesale_roles;

        /**
         * WWPP plugin path.
         *
         * @since 2.0
         * @access private
         */
        const WWPP_PLUGIN_PATH = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'woocommerce-wholesale-prices-premium' . DIRECTORY_SEPARATOR . 'woocommerce-wholesale-prices-premium.bootstrap.php';

        /**
         * WWOF plugin path.
         *
         * @since 2.0
         * @access private
         */
        const WWOF_PLUGIN_PATH = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'woocommerce-wholesale-order-form' . DIRECTORY_SEPARATOR . 'woocommerce-wholesale-order-form.bootstrap.php';

        /**
         * WWLC plugin path.
         *
         * @since 2.0
         * @access private
         */
        const WWLC_PLUGIN_PATH = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'woocommerce-wholesale-lead-capture' . DIRECTORY_SEPARATOR . 'woocommerce-wholesale-lead-capture.bootstrap.php';

        /**
         * Total wholesale orders cache
         * @since 2.0.1
         */
        const WWP_TOTAL_WHOLESALE_ORDERS_CACHE = 'wwp_total_wholesale_orders_cache';

        /**
         * Total wholesale revenue cache
         * @since 2.0.1
         */
        const WWP_TOTAL_WHOLESALE_REVENUE_CACHE = 'wwp_total_wholesale_revenue_cache';

        /**
         * Top wholesale customers cache
         * @since 2.0.1
         */
        const WWP_TOP_WHOLESALE_CUSTOMERS_CACHE = 'wwp_top_wholesale_customers_cache';

        /**
         * Recent wholesale orders cache
         * @since 2.0.1
         */
        const WWP_RECENT_WHOLEALE_ORDERS_CACHE = 'wwp_recent_wholesale_orders_cache';

        /**
         * Wholesale user ids cache
         * @since 2.0.1
         */
        const WWP_WHOLESALE_USERS_IDS_CACHE = 'wwp_wholesale_users_ids_cache';

        /**
         * Wholesale plugins statuses cache
         * @since 2.0.1
         */
        const WWP_PLUGIN_LICENSE_STATUSES_CACHE = 'wwp_plugin_license_statuses_cache';

        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
         */

        /**
         * WWP_Dashboard constructor.
         *
         * @since 2.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Dashboard model.
         */
        public function __construct($dependencies)
        {

            $this->_wwp_wholesale_roles = $dependencies['WWP_Wholesale_Roles'];

        }

        /**
         * Ensure that only one instance of WWP_Dashboard is loaded or can be loaded (Singleton Pattern).
         *
         * @since 2.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Dashboard model.
         * @return WWP_Dashboard
         */
        public static function instance($dependencies)
        {

            if (!self::$_instance instanceof self) {
                self::$_instance = new self($dependencies);
            }

            return self::$_instance;

        }

        /**
         * Enqueue React Scripts
         *
         * @since 2.0
         * @access public
         *
         * @param string $handle
         */
        public function load_back_end_styles_and_scripts($handle)
        {

            // Don't queue scripts if dashboard is disabled via filter
            if ($this->is_wholesale_dashboard_disabled()) {
                return;
            }

            if (strpos($handle, 'wholesale-suite') !== false) {

                // Important: Must enqueue this script in order to use WP REST API via JS
                wp_enqueue_script('wp-api');

                wp_localize_script('wp-api', 'dashboard_options',
                    array(
                        'root'  => esc_url_raw(rest_url()),
                        'nonce' => wp_create_nonce('wp_rest'),
                    )
                );

                // React Order Form Scripts
                $paths = array(
                    'handle'   => 'dashboard_app',
                    'dir_name' => 'dashboard-app',
                    'js_path'  => WWP_JS_PATH,
                    'js_url'   => WWP_JS_URL,
                );

                WWP_Helper_Functions::load_react_scripts($paths);

            }

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
                        'id'        => 'wholesale-dashboard-page',
                        'screen_id' => 'toplevel_page_wholesale-suite',
                        'title'     => __('Dashboard', 'woocommerce-wholesale-prices'),
                    )
                );
            }

        }

        /**
         * Dashboard menu api.
         *
         * @since 2.0
         * @access public
         *
         * @param WP_REST_Request $request
         */
        public function rest_api_dashboard($request)
        {

            register_rest_route('wholesale/v1', '/dashboard', array(
                'methods'             => 'GET',
                'callback'            => array($this, 'get_dashboard_data'),
                'permission_callback' => array($this, 'permissions_check'),
            ));

        }

        /**
         * Check whether the user has permission perform the request.
         *
         * @since 2.0
         * @param  WP_REST_Request
         * @return WP_Error|boolean
         */
        public function permissions_check($request)
        {

            // Bypass wp cookie auth
            if (defined('WWP_DEV') && WWP_DEV) {
                return true;
            }

            // Grant permission if admin or shop manager
            if (current_user_can('administrator') || current_user_can('manage_woocommerce')) {
                return true;

            }

            return new WP_Error('rest_cannot_view', __('Invalid Request.', 'woocommerce-wholesale-prices'), array('status' => rest_authorization_required_code()));

        }

        /**
         * Get dashboard data.
         * This function handles fetches the ff:
         * - Plugin Activation response
         * - Translatable/Translation text
         * - Quick stats filter
         * - Get all data seen in the dashboard
         *
         * @since 2.0
         * @param  WP_REST_Request
         * @return WP_Error|boolean
         */
        public function get_dashboard_data($request)
        {

            $activate_plugin       = $request['activate_plugin'];
            $days_filter           = $request['daysFilter'];
            $internationalization  = $request['internationalization'];
            $recheck_plugin_status = boolval($request['recheck_plugin_status']);

            // Plugin Activation response
            if (isset($activate_plugin)) {

                switch ($activate_plugin) {
                    case 'wwof':
                        $plugin_name = __("WooCommerce Wholesale Order Form plugin.", "woocommerce-wholesale-prices");
                        break;
                    case 'wwlc':
                        $plugin_name = __("WooCommerce Wholesale Lead Capture plugin.", "woocommerce-wholesale-prices");
                        break;
                    default:
                        $plugin_name = __("WooCommerce Wholesale Prices Premium plugin.", "woocommerce-wholesale-prices");
                }

                if (empty($this->activate_plugin($activate_plugin))) {
                    $response = array(
                        'status'  => 'success',
                        'message' => __('Successfully activated', 'woocommerce-wholesale-prices') . ' ' . $plugin_name,
                    );
                } else {
                    $response = array(
                        'status'  => 'error',
                        'message' => __('Unable to activate', 'woocommerce-wholesale-prices') . ' ' . $plugin_name,
                    );
                }

            } else if ($internationalization) {

                // Fetch translatable/translation text
                $response = array(
                    'internationalization' => $this->internationalization(),
                );

            } else if (!empty($days_filter)) {

                // Filter Quick Stats
                $response = array(
                    'quick_stats' => array(
                        'wholesale_orders'  => $this->get_total_wholesale_orders($days_filter),
                        'wholesale_revenue' => $this->get_total_wholesale_revenue($days_filter),
                    ),
                );

            } else if ($recheck_plugin_status) {

                $response = array(
                    'status'           => 'success',
                    'message'          => __('The license status is now up to date.', 'woocommerce-wholesale-prices'),
                    'license_statuses' => $this->get_wws_plugins_license_statuses($recheck_plugin_status),
                );

            } else {

                // Fetch all data in the dashboard
                $response = array(
                    'quick_stats'             => array(
                        'wholesale_orders'  => $this->get_total_wholesale_orders(),
                        'wholesale_revenue' => $this->get_total_wholesale_revenue(),
                    ),
                    'wholesale_orders_link'   => admin_url('edit.php?post_status=all&post_type=shop_order&wwpp_fbwr=all_wholesale_orders'),
                    'top_wholesale_customers' => $this->get_top_wholesale_customers(),
                    'recent_wholesale_orders' => $this->get_recent_wholesale_orders(),
                    'filter_options'          => array(
                        'options' => array(
                            '30'   => __('Last 30 days', 'woocommerce-wholesale-prices'),
                            '14'   => __('Last 14 days', 'woocommerce-wholesale-prices'),
                            '7'    => __('Last 7 days', 'woocommerce-wholesale-prices'),
                            'year' => __('Last 1 year', 'woocommerce-wholesale-prices'),
                        ),
                        'default' => "30",
                    ),
                    'internationalization'    => $this->internationalization(),
                    'license_page_link'       => admin_url('admin.php?page=wwc_license_settings'),
                    'wws_logo'                => WWP_IMAGES_URL . 'logo.png',
                    'logo_link'               => 'https://wholesalesuiteplugin.com/bundle/?utm_source=wwp&utm_medium=upsell&utm_campaign=logo',
                    'help_resources_links'    => array(
                        'getting_started_guide_link' => 'https://wholesalesuiteplugin.com/knowledge-base-category/getting-started/?utm_source=wwp&utm_medium=wwsdashboard&utm_campaign=quicklinksgettingstarted',
                        'read_documentation_link'    => 'https://wholesalesuiteplugin.com/knowledge-base/?utm_source=wwp&utm_medium=wwsdashboard&utm_campaign=quicklinksreaddocs',
                        'settings_link'              => admin_url('admin.php?page=wholesale-settings'),
                        'contact_support'            => 'https://wholesalesuiteplugin.com/support/?utm_source=wwp&utm_medium=wwsdashboard&utm_campaign=quicklinkscontactsupport',
                    ),
                    'license_statuses'        => $this->get_wws_plugins_license_statuses(),
                    'wws_plugins'             => array(
                        'wwpp' => array(
                            'key'             => 'wwpp',
                            'name'            => __('Wholesale Prices Premium', 'woocommerce-wholesale-prices'),
                            'installed'       => WWP_Helper_Functions::is_wwpp_installed(),
                            'active'          => WWP_Helper_Functions::is_wwpp_active(),
                            'link'            => 'https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=wwp&utm_medium=wwsdashboard&utm_campaign=licenseboxwwpp',
                            'learn_more_link' => 'https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=wwp&utm_medium=upsell&utm_campaign=dashboardlearnmorewwpp',
                        ),
                        'wwof' => array(
                            'key'             => 'wwof',
                            'name'            => __('Wholesale Order Form', 'woocommerce-wholesale-prices'),
                            'installed'       => WWP_Helper_Functions::is_wwof_installed(),
                            'active'          => WWP_Helper_Functions::is_wwof_active(),
                            'link'            => 'https://wholesalesuiteplugin.com/woocommerce-wholesale-order-form/?utm_source=wwp&utm_medium=wwsdashboard&utm_campaign=licenseboxwwof',
                            'learn_more_link' => 'https://wholesalesuiteplugin.com/woocommerce-wholesale-order-form/?utm_source=wwp&utm_medium=upsell&utm_campaign=dashboardlearnmorewwof',
                        ),
                        'wwlc' => array(
                            'key'             => 'wwlc',
                            'name'            => __('Wholesale Lead Capture', 'woocommerce-wholesale-prices'),
                            'installed'       => WWP_Helper_Functions::is_wwlc_installed(),
                            'active'          => WWP_Helper_Functions::is_wwlc_active(),
                            'link'            => 'https://wholesalesuiteplugin.com/woocommerce-wholesale-lead-capture/?utm_source=wwp&utm_medium=wwsdashboard&utm_campaign=licenseboxwwlc',
                            'learn_more_link' => 'https://wholesalesuiteplugin.com/woocommerce-wholesale-lead-capture/?utm_source=wwp&utm_medium=upsell&utm_campaign=dashboardlearnmorewwlc',
                        ),
                    ),
                );

                $response = apply_filters('wwp_dashboard_data', $response);

            }

            return rest_ensure_response($response);
        }

        /**
         * This function handles fetching/filtering wholesale total orders in the quick stats.
         *
         * @since 2.0
         * @access public
         * @return int
         */
        public function get_total_wholesale_orders($days_filter = '30')
        {

            $cache = get_transient(self::WWP_TOTAL_WHOLESALE_ORDERS_CACHE);

            if ($this->enable_dashboard_cache() && !empty($cache) && $days_filter == 30) {
                return $cache;
            }

            $date_created = "";

            switch ($days_filter) {
                case '14':
                    $date_created = '>' . (time() - (WEEK_IN_SECONDS * 2));
                    break;
                case '7':
                    $date_created = '>' . (time() - WEEK_IN_SECONDS);
                    break;
                case 'year':
                    $date_created = '>' . (time() - YEAR_IN_SECONDS);
                    break;
                default:
                    $date_created = '>' . (time() - MONTH_IN_SECONDS);

            }

            $total_orders = wc_get_orders(array(
                'status'          => array('wc-processing', 'wc-completed'),
                'date_created'    => $date_created,
                'wholesale_order' => true,
                'return'          => 'ids',
                'limit'           => -1,
            ));

            $total_wholesale_orders = (int) count($total_orders);

            // Store as cache. Cache expires in 1 minute
            if ($this->enable_dashboard_cache()) {
                set_transient(self::WWP_TOTAL_WHOLESALE_ORDERS_CACHE, $total_wholesale_orders, MINUTE_IN_SECONDS);
            }

            return $total_wholesale_orders;

        }

        /**
         * This function handles fetching/filtering total wholesale revenue in the quick stats.
         *
         * @since 2.0
         * @access public
         * @return float
         */
        public function get_total_wholesale_revenue($days_filter = '30')
        {

            $cache = get_transient(self::WWP_TOTAL_WHOLESALE_REVENUE_CACHE);

            if ($this->enable_dashboard_cache() && !empty($cache) && $days_filter == 30) {
                return $cache;
            }

            $wholesale_customer_total_spent = 0;

            $date_created = "";

            switch ($days_filter) {
                case '14':
                    $date_created = '>' . (time() - (WEEK_IN_SECONDS * 2));
                    break;
                case '7':
                    $date_created = '>' . (time() - WEEK_IN_SECONDS);
                    break;
                case 'year':
                    $date_created = '>' . (time() - YEAR_IN_SECONDS);
                    break;
                default:
                    $date_created = '>' . (time() - MONTH_IN_SECONDS);

            }

            $date_created = apply_filters('wwp_dashboard_days_filter', $date_created, $days_filter);

            $orders = wc_get_orders(array(
                'status'          => array('wc-processing', 'wc-completed'),
                'date_created'    => $date_created,
                'wholesale_order' => true,
                'limit'           => -1,
            ));

            if ($orders) {
                foreach ($orders as $order) {
                    $wholesale_customer_total_spent += $order->get_total();
                }
            }

            $total_revenue = wc_price($wholesale_customer_total_spent);

            // Store as cache. Cache expires in 1 week
            if ($this->enable_dashboard_cache()) {
                set_transient(self::WWP_TOTAL_WHOLESALE_REVENUE_CACHE, $total_revenue, WEEK_IN_SECONDS);
            }

            return $total_revenue;

        }

        /**
         * This function checks for wholesale customers who paid for the orders.
         * This uses wc_get_customer_total_spent() to get the total for each user. This calculates orders with status of 'processing', 'completed'.
         *
         * @since 2.0
         * @access public
         * @return array
         */
        public function get_top_wholesale_customers()
        {

            $cache = get_transient(self::WWP_TOP_WHOLESALE_CUSTOMERS_CACHE);

            if ($this->enable_dashboard_cache() && !empty($cache)) {
                return $cache;
            }

            // Get all current wholesale user ids from cache
            $wholesale_user_ids_cache = $this->get_wholesale_user_ids();

            $wholesale_spent = array();
            $limit           = apply_filters('wwp_top_wholesale_customers_limit', 5); // We will only display 5 top wholesale customers

            foreach ($wholesale_user_ids_cache as $user_id) {

                $user  = get_userdata($user_id);
                $spent = wc_get_customer_total_spent($user_id);

                if ($user && $spent > 0) {

                    $wholesale_spent[] = array(
                        'id'        => (int) $user_id,
                        'name'      => $user->data->display_name,
                        'spent_raw' => (float) $spent,
                        'spent'     => wc_price($spent),
                        'link'      => admin_url('user-edit.php?user_id=' . $user_id),
                    );

                }

            }

            $spent_sort = array_column($wholesale_spent, 'spent_raw');

            array_multisort($spent_sort, SORT_DESC, $wholesale_spent);

            // Get only top 5
            $wholesale_spent = array_slice($wholesale_spent, 0, $limit, true);

            // Store as cache. Cache expires in 1 week.
            if ($this->enable_dashboard_cache()) {
                set_transient(self::WWP_TOP_WHOLESALE_CUSTOMERS_CACHE, $wholesale_spent, WEEK_IN_SECONDS);
            }

            return $wholesale_spent;

        }

        /**
         * This function fetches the most recent wholesale orders.
         *
         * @since 2.0
         * @access public
         * @return array
         */
        public function get_recent_wholesale_orders()
        {

            $cache = get_transient(self::WWP_RECENT_WHOLEALE_ORDERS_CACHE);

            if ($this->enable_dashboard_cache() && !empty($cache)) {
                return $cache;
            }

            $orders = wc_get_orders(array(
                'limit'           => apply_filters('wwp_recent_wholesale_orders_limit', 5),
                'orderby'         => 'date',
                'order'           => 'DESC',
                'wholesale_order' => true,
            ));

            $wholesale_orders = array();

            if ($orders) {

                foreach ($orders as $order) {

                    $user            = $order->get_user();
                    $wc_order_status = wc_get_order_statuses();

                    $wholesale_orders[] = array(
                        'id'          => $order->get_id(),
                        'name'        => $user->data->display_name,
                        'order_total' => wc_price($order->get_total()),
                        'status'      => $wc_order_status['wc-' . $order->get_status()] ? $wc_order_status['wc-' . $order->get_status()] : '',
                        'view_order'  => $order->get_edit_order_url(),
                    );

                }
            }

            // Store as cache. Cache expires in 1 week.
            set_transient(self::WWP_RECENT_WHOLEALE_ORDERS_CACHE, $wholesale_orders, WEEK_IN_SECONDS);

            return $wholesale_orders;

        }

        /**
         * Filter wc_get_orders() query to fetch wholesale orders
         *
         * @since 2.0
         * @access public
         * @return array
         */
        public function handle_custom_query_var($query, $query_vars)
        {

            if (!empty($query_vars['wholesale_order']) && $query_vars['wholesale_order'] == true) {

                // Get all current wholesale user ids from cache
                $registered_roles = $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles();
                $wholesale_roles  = array();

                if (!empty($registered_roles)) {
                    foreach ($registered_roles as $role => $data) {
                        $wholesale_roles[] = $role;
                    }
                }

                $query['meta_query'][] = array(
                    'key'     => 'wwp_wholesale_role',
                    'value'   => join(', ', $wholesale_roles),
                    'compare' => 'IN',
                );

            }

            return $query;

        }

        /**
         * Text Internationalization for Dashboard App
         *
         * @since 2.0
         * @access public
         * @return array
         */
        public function internationalization()
        {

            $texts = array(
                'dashboard'                     => __('Dashboard', 'woocommerce-wholesale-prices'),
                'quick_stats'                   => __('Quick Stats:', 'woocommerce-wholesale-prices'),
                'quick_stats_note'              => __('The stats will only count orders that are in processing or completed status.', 'woocommerce-wholesale-prices'),
                'wholesale_orders'              => __('Wholesale Orders', 'woocommerce-wholesale-prices'),
                'wholesale_revenue'             => __('Wholesale Revenue', 'woocommerce-wholesale-prices'),
                'top_wholesale_customers'       => __('Top Wholesale Customers:', 'woocommerce-wholesale-prices'),
                'recent_wholesale_orders'       => __('Recent Wholesale Orders:', 'woocommerce-wholesale-prices'),
                'view_order'                    => __('View Order', 'woocommerce-wholesale-prices'),
                'view_all_wholesale_orders'     => __('View All Wholesale Orders &rarr;', 'woocommerce-wholesale-prices'),
                'helpful_resources'             => __('Helpful Resources:', 'woocommerce-wholesale-prices'),
                'getting_started_guides'        => __('Getting Started Guides', 'woocommerce-wholesale-prices'),
                'read_documentation'            => __('Read Documentation', 'woocommerce-wholesale-prices'),
                'settings'                      => __('Settings', 'woocommerce-wholesale-prices'),
                'contact_support'               => __('Contact Support', 'woocommerce-wholesale-prices'),
                'license_activation_status'     => __('License Activation Status:', 'woocommerce-wholesale-prices'),
                'wholesale_prices_premium'      => __('Wholesale Prices Premium', 'woocommerce-wholesale-prices'),
                'wholesale_order_form'          => __('Wholesale Order Form', 'woocommerce-wholesale-prices'),
                'wholesale_lead_capture'        => __('Wholesale Lead Capture', 'woocommerce-wholesale-prices'),
                'view_licenses'                 => __('View Licenses &rarr;', 'woocommerce-wholesale-prices'),
                'wholesale_suite_plugins'       => __('Wholesale Suite Plugins:', 'woocommerce-wholesale-prices'),
                'deactivated_plugins'           => __('Deactivated Plugins:', 'woocommerce-wholesale-prices'),
                'activate_plugin'               => __('Activate', 'woocommerce-wholesale-prices'),
                'click_to_activate'             => __('Click to activate the plugin.', 'woocommerce-wholesale-prices'),
                'recheck_plugin_status'         => __('Re-check Plugin Status', 'woocommerce-wholesale-prices'),
                'recheck_plugin_status_tooltip' => __('Click to fetch the latest plugin license status.', 'woocommerce-wholesale-prices'),
                'no_data'                       => __('No Data', 'woocommerce-wholesale-prices'),
                'upgrade_now'                   => __('Upgrade Now', 'woocommerce-wholesale-prices'),
                'links'                         => array(
                    'upgrade_now' => 'https://wholesalesuiteplugin.com/bundle/?utm_source=wwp&utm_medium=upsell&utm_campaign=dashboardupgradenowbutton',
                ),
                'learn_more'                    => __('Learn More &rarr;', 'woocommerce-wholesale-prices'),
            );

            $texts = apply_filters('wwp_dashboard_texts', $texts);

            return $texts;

        }

        /**
         * Get WWS Plugins License Statuses.
         * The license is being cached using transient. The expiration is set to 1 day.
         * This set to 1 day so that we can refetch again incase some customers has expired license on the current day so that it will reflect in the dashboard.
         *
         * @since 2.0
         * @access public
         * @return array
         */
        public function get_wws_plugins_license_statuses($recheck_plugin_status = false)
        {

            /**
             * License Manager Example Return Data
             *
             * Active:
             * Array
             * (
             *    [status] => success
             *    [success_msg] => Successfully activated license for this site
             * )
             *
             * Inactive:
             * Array
             * (
             *    [status] => fail
             *    [error_msg] => This license is currently disabled. Please contact plugin support for more info.
             * )
             *
             * Expired:
             * Array
             * (
             *    [status] => fail
             *    [error_msg] => The entered license was purchased over 12 months ago and is now expired. Please renew your license.
             *    [expired_date] => 2021-11-06
             * )
             *
             * Invalid ( License key is added but cant verify):
             * Array
             * (
             *    [status] => fail
             *    [error_msg] => Invalid license key
             * )
             *
             * Invalid (empty license keys):
             * Array
             * (
             *    [status] => fail
             *    [error_msg] => Activation Email is invalid
             * )
             *
             * Invalid ( No email and key supplied)
             *
             * Array
             * (
             *    [status] => fail
             *    [error_msg] => Necessary data not supplied
             *)
             **/

            // Use the cache
            $cache = get_transient(self::WWP_PLUGIN_LICENSE_STATUSES_CACHE);

            if ($this->enable_dashboard_cache() && !empty($cache) && $recheck_plugin_status === false) {
                return $cache;
            }

            $wws_license_statuses = array();

            /* Wholesale Prices Premium */
            $wws_license_statuses = $this->wholesale_prices_premium_license_cache($wws_license_statuses);

            /* Wholesale Order Form */
            $wws_license_statuses = $this->wholesale_order_form_license_cache($wws_license_statuses);

            /* Wholesale Lead Capture */
            $wws_license_statuses = $this->wholesale_lead_capture_license_cache($wws_license_statuses);

            // Store as cache. Cache expires in 1 day.
            if ($this->enable_dashboard_cache()) {
                set_transient(self::WWP_PLUGIN_LICENSE_STATUSES_CACHE, $wws_license_statuses, DAY_IN_SECONDS);
            }

            return $wws_license_statuses;

        }

        /**
         * Get/Update WWPP License status.
         * Only if:
         *          - WWPP is installed and active
         *          - WWPP v1.27.3 and up
         * @since 2.0
         * @access public
         * @return array
         */
        public function wholesale_prices_premium_license_cache($wws_license_statuses)
        {

            if (!WWP_Helper_Functions::is_wwpp_installed()) {
                return $wws_license_statuses;
            }

            $wwpp_text = __('Wholesale Prices Premium', 'woocommerce-wholesale-prices');

            if (WWP_Helper_Functions::is_wwpp_active() && WWP_Helper_Functions::check_wws_plugin_min_version('wwpp', '1.27.3')) {

                $wwpp_status = WWPP_WWS_License_Manager::instance()->ajax_activate_license();

                if (isset($wwpp_status['status']) && $wwpp_status['status'] == 'success') {

                    $wws_license_statuses['wwpp'] = array(
                        'text'   => $wwpp_text . " (<span class='active'>" . __("Active", "woocommerce-wholesale-prices") . "</span>)",
                        'status' => 'active',
                    );

                } else if (isset($wwpp_status['error_msg']) && strpos($wwpp_status['error_msg'], 'This license is currently disabled') !== false) {

                    $wws_license_statuses['wwpp'] = array(
                        'text'   => $wwpp_text . " (<span class='inactive'>" . __("Inactive", "woocommerce-wholesale-prices") . "</span>)",
                        'status' => 'inactive',
                    );

                } else if (isset($wwpp_status['expired_date'])) {

                    $wws_license_statuses['wwpp'] = array(
                        'text'   => $wwpp_text . " (<span class='expired'>" . __("Expired", "woocommerce-wholesale-prices") . "</span>)",
                        'status' => 'expired',
                    );

                } else if (isset($wwpp_status['error_msg'])) {

                    $wws_license_statuses['wwpp'] = array(
                        'text'   => $wwpp_text . " (<span class='invalid'>" . __("Invalid", "woocommerce-wholesale-prices") . "</span>)",
                        'status' => 'invalid',
                    );

                    $license_email = is_multisite() ? get_site_option('wwpp_option_license_email') : get_option('wwpp_option_license_email');
                    $license_key   = is_multisite() ? get_site_option('wwpp_option_license_key') : get_option('wwpp_option_license_key');

                    if (empty($license_email) && empty($license_key)) {

                        // No email and key
                        $wws_license_statuses['wwpp']['tooltip'] = __('No license email and key.', 'woocommerce-wholesale-prices');

                    } else if (empty($license_email)) {

                        // No email
                        $wws_license_statuses['wwpp']['tooltip'] = __('License email is empty.', 'woocommerce-wholesale-prices');

                    } else if (empty($license_key)) {

                        // No key
                        $wws_license_statuses['wwpp']['tooltip'] = __('License key is empty.', 'woocommerce-wholesale-prices');

                    } else if (strpos($wwpp_status['error_msg'], 'Invalid activation email') !== false) {

                        // Invalid email
                        $wws_license_statuses['wwpp']['tooltip'] = __('The license email is invalid.', 'woocommerce-wholesale-prices');

                    } elseif (strpos($wwpp_status['error_msg'], 'Invalid license key') !== false) {

                        // Invalid key
                        $wws_license_statuses['wwpp']['tooltip'] = __('The license key is invalid.', 'woocommerce-wholesale-prices');

                    }

                }

            } else {

                $plugin_file                  = 'woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php';
                $wws_license_statuses['wwpp'] = array(
                    'text'   => $wwpp_text . " (<span class='deactivated'>" . __("Activate Plugin", "woocommerce-wholesale-prices") . "</span>)",
                    'status' => 'deactivated',
                );

            }

            return $wws_license_statuses;

        }

        /**
         * Update WWOF License status
         *
         * @since 2.0
         * @access public
         * @return array
         */
        public function wholesale_order_form_license_cache($wws_license_statuses)
        {

            if (!WWP_Helper_Functions::is_wwof_installed()) {
                return $wws_license_statuses;
            }

            $wwof_text = __('Wholesale Order Form', 'woocommerce-wholesale-prices');

            if (WWP_Helper_Functions::is_wwof_active() && WWP_Helper_Functions::check_wws_plugin_min_version('wwof', '1.21.2')) {

                $wwof_status = WWOF_WWS_License_Manager::instance()->ajax_activate_license();

                if (isset($wwof_status['status']) && $wwof_status['status'] == 'success') {

                    $wws_license_statuses['wwof'] = array(
                        'text'   => $wwof_text . " (<span class='active'>" . __("Active", "woocommerce-wholesale-prices") . "</span>)",
                        'status' => 'active',
                    );

                } else if (isset($wwof_status['error_msg']) && strpos($wwof_status['error_msg'], 'This license is currently disabled') !== false) {

                    $wws_license_statuses['wwof'] = array(
                        'text'   => $wwof_text . " (<span class='inactive'>" . __("Inactive", "woocommerce-wholesale-prices") . "</span>)",
                        'status' => 'inactive',
                    );

                } else if (isset($wwof_status['expired_date'])) {

                    $wws_license_statuses['wwof'] = array(
                        'text'   => $wwof_text . " (<span class='expired'>" . __("Expired", "woocommerce-wholesale-prices") . "</span>)",
                        'status' => 'expired',
                    );

                } else if (isset($wwof_status['error_msg'])) {

                    $wws_license_statuses['wwof'] = array(
                        'text'   => $wwof_text . " (<span class='invalid'>" . __("Invalid", "woocommerce-wholesale-prices") . "</span>)",
                        'status' => 'invalid',
                    );

                    $license_email = is_multisite() ? get_site_option('wwof_option_license_email') : get_option('wwof_option_license_email');
                    $license_key   = is_multisite() ? get_site_option('wwof_option_license_key') : get_option('wwof_option_license_key');

                    if (empty($license_email) && empty($license_key)) {

                        // No email and key
                        $wws_license_statuses['wwof']['tooltip'] = __('No license email and key.', 'woocommerce-wholesale-prices');

                    } else if (empty($license_email)) {

                        // No email
                        $wws_license_statuses['wwof']['tooltip'] = __('License email is empty.', 'woocommerce-wholesale-prices');

                    } else if (empty($license_key)) {

                        // No key
                        $wws_license_statuses['wwof']['tooltip'] = __('License key is empty.', 'woocommerce-wholesale-prices');

                    } else if (strpos($wwof_status['error_msg'], 'Invalid activation email') !== false) {

                        // Invalid email
                        $wws_license_statuses['wwof']['tooltip'] = __('The license email is invalid.', 'woocommerce-wholesale-prices');

                    } elseif (strpos($wwof_status['error_msg'], 'Invalid license key') !== false) {

                        // Invalid key
                        $wws_license_statuses['wwof']['tooltip'] = __('The license key is invalid.', 'woocommerce-wholesale-prices');

                    }

                }

            } else {

                $plugin_file                  = 'woocommerce-wholesale-order-form/woocommerce-wholesale-order-form.bootstrap.php';
                $wws_license_statuses['wwof'] = array(
                    'text'   => $wwof_text . " (<span class='deactivated'>" . __("Activate Plugin", "woocommerce-wholesale-prices") . "</span>)",
                    'status' => 'deactivated',
                );

            }

            return $wws_license_statuses;

        }

        /**
         * Update WWOF License status
         *
         * @since 2.0
         * @access public
         * @return array
         */
        public function wholesale_lead_capture_license_cache($wws_license_statuses)
        {

            if (!WWP_Helper_Functions::is_wwlc_installed()) {
                return $wws_license_statuses;
            }

            $wwlc_text = __('Wholesale Lead Capture', 'woocommerce-wholesale-prices');

            if (WWP_Helper_Functions::is_wwlc_active() && WWP_Helper_Functions::check_wws_plugin_min_version('wwlc', '1.17.1')) {

                $wwlc_status = WWLC_WWS_License_Manager::instance()->ajax_activate_license();

                if (isset($wwlc_status['status']) && $wwlc_status['status'] == 'success') {

                    $wws_license_statuses['wwlc'] = array(
                        'text'   => $wwlc_text . " (<span class='active'>" . __("Active", "woocommerce-wholesale-prices") . "</span>)",
                        'status' => 'active',
                    );

                } else if (isset($wwlc_status['error_msg']) && strpos($wwlc_status['error_msg'], 'This license is currently disabled') !== false) {

                    $wws_license_statuses['wwlc'] = array(
                        'text'   => $wwlc_text . " (<span class='inactive'>" . __("Inactive", "woocommerce-wholesale-prices") . "</span>)",
                        'status' => 'inactive',
                    );

                } else if (isset($wwlc_status['expired_date'])) {

                    $wws_license_statuses['wwlc'] = array(
                        'text'   => $wwlc_text . " (<span class='expired'>" . __("Expired", "woocommerce-wholesale-prices") . "</span>)",
                        'status' => 'expired',
                    );

                } else if (isset($wwlc_status['error_msg'])) {

                    $wws_license_statuses['wwlc'] = array(
                        'text'   => $wwlc_text . " (<span class='invalid'>" . __("Invalid", "woocommerce-wholesale-prices") . "</span>)",
                        'status' => 'invalid',
                    );

                    $license_email = is_multisite() ? get_site_option('wwlc_option_license_email') : get_option('wwlc_option_license_email');
                    $license_key   = is_multisite() ? get_site_option('wwlc_option_license_key') : get_option('wwlc_option_license_key');

                    if (empty($license_email) && empty($license_key)) {

                        // No email and key
                        $wws_license_statuses['wwlc']['tooltip'] = __('No license email and key.', 'woocommerce-wholesale-prices');

                    } else if (empty($license_email)) {

                        // No email
                        $wws_license_statuses['wwlc']['tooltip'] = __('License email is empty.', 'woocommerce-wholesale-prices');

                    } else if (empty($license_key)) {

                        // No key
                        $wws_license_statuses['wwlc']['tooltip'] = __('License key is empty.', 'woocommerce-wholesale-prices');

                    } else if (strpos($wwlc_status['error_msg'], 'Invalid activation email') !== false) {

                        // Invalid email
                        $wws_license_statuses['wwlc']['tooltip'] = __('The license email is invalid.', 'woocommerce-wholesale-prices');

                    } elseif (strpos($wwlc_status['error_msg'], 'Invalid license key') !== false) {

                        // Invalid key
                        $wws_license_statuses['wwlc']['tooltip'] = __('The license key is invalid.', 'woocommerce-wholesale-prices');

                    }

                }

            } else {

                $plugin_file                  = 'woocommerce-wholesale-lead-capture/woocommerce-wholesale-lead-capture.bootstrap.php';
                $wws_license_statuses['wwlc'] = array(
                    'text'   => $wwlc_text . " (<span class='deactivated'>" . __("Activate Plugin", "woocommerce-wholesale-prices") . "</span>)",
                    'status' => 'deactivated',
                );

            }

            return $wws_license_statuses;

        }

        /**
         * Clear Cache.
         * The hook "save_post_shop_order" is called on the following events:
         * - order creation (both customer checkout process & admin)
         * - single order update
         * - bulk status update
         * - trashing the order
         * - untrashing the order
         *
         * @since 2.0
         * @access public
         * @return array
         */
        public function clear_cache_on_new_orders($post_id, $post, $update)
        {

            $this->clear_cache();

        }

        /**
         * Clear Cache.
         *
         * @since 2.0
         * @access public
         * @return array
         */
        public function clear_cache()
        {

            // Total wholesale orders cache
            delete_transient(self::WWP_TOTAL_WHOLESALE_ORDERS_CACHE);

            // Total wholesale revenue cache
            delete_transient(self::WWP_TOTAL_WHOLESALE_REVENUE_CACHE);

            // Top wholesale customers cache
            delete_transient(self::WWP_TOP_WHOLESALE_CUSTOMERS_CACHE);

            // Recent wholesale orders cache
            delete_transient(self::WWP_RECENT_WHOLEALE_ORDERS_CACHE);

            // Wholesale user ids cache
            delete_transient(self::WWP_WHOLESALE_USERS_IDS_CACHE);

        }

        /**
         * Clear Cache.
         *
         * @since 2.0
         * @access public
         * @return array
         */
        public function enable_dashboard_cache()
        {

            return apply_filters('wwp_enable_dashboard_cache', true);

        }

        /**
         * Activate Plugin.
         *
         * @since 2.0
         * @access public
         * @return array
         */
        public function activate_plugin($plugin_name)
        {

            // Define the new plugin you want to activate
            switch ($plugin_name) {
                case 'wwof':
                    $plugin_path = self::WWOF_PLUGIN_PATH;
                    break;
                case 'wwlc':
                    $plugin_path = self::WWLC_PLUGIN_PATH;
                    break;
                default:
                    $plugin_path = self::WWPP_PLUGIN_PATH;
            }

            // Get already-active plugins
            $active_plugins = get_option('active_plugins');

            // Make sure your plugin isn't active
            if (isset($active_plugins[$plugin_path])) {
                return;
            }

            // Include the plugin.php file so you have access to the activate_plugin() function
            require_once ABSPATH . '/wp-admin/includes/plugin.php';

            // Activate your plugin
            return activate_plugin($plugin_path);

        }

        /**
         * Clear Cache.
         * The hook is called on the following events:
         * - Triggered on the update page
         * - WWPP has new version
         * - WWOF has new version
         * - WWLC has new version
         *
         * @since 2.0
         * @access public
         * @return array
         */
        public function clear_license_cache_on_version_check($result, $activation_email, $license_key)
        {

            delete_transient(self::WWP_PLUGIN_LICENSE_STATUSES_CACHE);

        }

        /**
         * Clear license statues cache
         *
         * @since 2.0
         * @since 2.1 Clear all cache when activating/deactivating WWPP
         *
         * @access public
         * @return array
         */
        public function clear_license_cache_on_activation_deactivation()
        {

            // Clear all cache when WWPP is activated/deactivated
            if (
                in_array(current_filter(),
                    array(
                        'activate_woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php',
                        'deactivate_woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php',
                    )
                )
            ) {

                $this->clear_cache();

            }

            delete_transient(self::WWP_PLUGIN_LICENSE_STATUSES_CACHE);

        }

        /**
         * Option to toggle dashboard on/off
         *
         * @since 2.0.1
         * @access public
         * @return array
         */
        public function is_wholesale_dashboard_disabled()
        {

            return apply_filters('wwp_disable_wholesale_dashboard', false);

        }

        /**
         * Get all wholesale user ids.
         *
         * @since 2.0.1
         * @access public
         * @return array
         */
        public function get_wholesale_user_ids()
        {

            $cache = get_transient(self::WWP_WHOLESALE_USERS_IDS_CACHE);

            if ($this->enable_dashboard_cache() && !empty($cache)) {
                return $cache;
            }

            $wholesale_user_ids_cache = $this->_wwp_wholesale_roles->get_all_wholesale_user_ids();

            // Store as cache. Cache expires in 1 day.
            if ($this->enable_dashboard_cache()) {
                set_transient(self::WWP_WHOLESALE_USERS_IDS_CACHE, $wholesale_user_ids_cache, HOUR_IN_SECONDS);
            }

            return $wholesale_user_ids_cache;

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

            // Load react scripts
            add_action('admin_enqueue_scripts', array($this, 'load_back_end_styles_and_scripts'), 10, 1);

            // Add wc navigation bar
            add_action('init', array($this, 'wc_navigation_bar'));

            // REST API for dashboard page
            add_action('rest_api_init', array($this, 'rest_api_dashboard'));

            // Wholesale Orders query
            add_filter('woocommerce_order_data_store_cpt_get_orders_query', array($this, 'handle_custom_query_var'), 10, 2);

            // Clear cache on new/update order
            add_action('save_post_shop_order', array($this, 'clear_cache_on_new_orders'), 10, 3);

            // Clear cache on new version check
            add_action('wwpp_software_product_update_data', array($this, 'clear_license_cache_on_version_check'), 10, 3);
            add_action('wwof_software_product_update_data', array($this, 'clear_license_cache_on_version_check'), 10, 3);
            add_action('wwlc_software_product_update_data', array($this, 'clear_license_cache_on_version_check'), 10, 3);

            // Clear cache on add/update license key
            add_action('wwpp_ajax_activate_license', array($this, 'clear_license_cache_on_version_check'), 10, 3);
            add_action('wwof_ajax_activate_license', array($this, 'clear_license_cache_on_version_check'), 10, 3);
            add_action('wwlc_ajax_activate_license', array($this, 'clear_license_cache_on_version_check'), 10, 3);

            // Update license cache on wws plugin activation
            register_activation_hook(self::WWPP_PLUGIN_PATH, array($this, 'clear_license_cache_on_activation_deactivation'));
            register_activation_hook(self::WWOF_PLUGIN_PATH, array($this, 'clear_license_cache_on_activation_deactivation'));
            register_activation_hook(self::WWLC_PLUGIN_PATH, array($this, 'clear_license_cache_on_activation_deactivation'));

            // Update license cache on wws plugin deactivation
            register_deactivation_hook(self::WWPP_PLUGIN_PATH, array($this, 'clear_license_cache_on_activation_deactivation'));
            register_deactivation_hook(self::WWOF_PLUGIN_PATH, array($this, 'clear_license_cache_on_activation_deactivation'));
            register_deactivation_hook(self::WWLC_PLUGIN_PATH, array($this, 'clear_license_cache_on_activation_deactivation'));

        }

    }

}
