<?php

/**
 * Wholesale Suite usage tracking functions for reporting usage to the Rymera Web Co servers for users who have opted in
 *
 * @author      Rymera Web Co
 * @version     1.14
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Usage tracking
 *
 * @access public
 * @since  7.0.0
 * @return void
 */

class WWP_Usage
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of WWP_Usage.
     *
     * @since 1.14
     * @access private
     * @var WWP_Usage
     */
    private static $_instance;

    /**
     * Current WWP version.
     *
     * @since 1.14
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
     * WWP_Usage constructor.
     *
     * @since 1.14
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Usage model.
     */
    public function __construct($dependencies = array())
    {
        // Nothing to see here yet
    }

    /**
     * Ensure that only one instance of WWP_Usage is loaded or can be loaded (Singleton Pattern).
     *
     * @since 1.14
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Usage model.
     * @return WWP_Usage
     */
    public static function instance($dependencies = array())
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self($dependencies);
        }

        return self::$_instance;
    }

    /**
     * Gather the tracking data together
     *
     * @since 1.14
     * @access public
     */
    private function get_data()
    {
        $data = array();

        // Get current theme info
        $theme_data = wp_get_theme();

        // Get multisite data
        $count_blogs = 1;
        if (is_multisite()) {
            if (function_exists('get_blog_count')) {
                $count_blogs = get_blog_count();
            } else {
                $count_blogs = 'Not Set';
            }
        }

        // Plugin specific data
        $data['wwp_version']  = WWP_Helper_Functions::get_wwp_version();
        $data['wwpp_version'] = WWP_Helper_Functions::get_wwpp_version();
        $data['wwof_version'] = WWP_Helper_Functions::get_wwof_version();
        $data['wwlc_version'] = WWP_Helper_Functions::get_wwlc_version();
        $data['wwpp']         = (int) WWP_Helper_Functions::is_wwpp_active();
        $data['wwof']         = (int) WWP_Helper_Functions::is_wwof_active();
        $data['wwlc']         = (int) WWP_Helper_Functions::is_wwlc_active();

        // License data
        $licenses = WWP_Helper_Functions::get_license_data();

        $data['wwpp_license_email'] = isset($licenses) && isset($licenses['wwpp_license_email']) ? $licenses['wwpp_license_email'] : '';
        $data['wwpp_license_key']   = isset($licenses) && isset($licenses['wwpp_license_key']) ? $licenses['wwpp_license_key'] : '';
        $data['wwof_license_email'] = isset($licenses) && isset($licenses['wwof_license_email']) ? $licenses['wwof_license_email'] : '';
        $data['wwof_license_key']   = isset($licenses) && isset($licenses['wwof_license_key']) ? $licenses['wwof_license_key'] : '';
        $data['wwlc_license_email'] = isset($licenses) && isset($licenses['wwlc_license_email']) ? $licenses['wwlc_license_email'] : '';
        $data['wwlc_license_key']   = isset($licenses) && isset($licenses['wwlc_license_key']) ? $licenses['wwlc_license_key'] : '';

        // Wholesale Roles
        $wwp_wholesale_roles     = WWP_Wholesale_Roles::getInstance();
        $data['wholesale_roles'] = $wwp_wholesale_roles->getAllRegisteredWholesaleRoles();

        // Settings
        $data['settings'] = $this->fetch_all_wws_settings();

        // Common server stuff
        $data['url']            = home_url();
        $data['php_version']    = phpversion();
        $data['wp_version']     = get_bloginfo('version');
        $data['wc_version']     = WWP_Helper_Functions::get_current_woocommerce_version();
        $data['server']         = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
        $data['multisite']      = is_multisite();
        $data['sites']          = $count_blogs;
        $data['usercount']      = function_exists('count_users') ? count_users() : 'Not Set';
        $data['themename']      = $theme_data->Name;
        $data['themeversion']   = $theme_data->Version;
        $data['admin_email']    = get_bloginfo('admin_email');
        $data['usagetracking']  = get_option('wwp_usage_tracking_config', false);
        $data['timezoneoffset'] = wp_timezone_string();
        $data['locale']         = get_locale();

        // Retrieve current plugin information

        // This site's active plugins list
        $active_plugins = get_option('active_plugins', array());

        // Multi-site network activated plugins (if we're on multi-site)
        $network_active_plugins = array_keys(get_site_option('active_sitewide_plugins', array()));

        // Merge to get the final active plugins list
        $all_active_plugins     = array_unique(array_merge($active_plugins, $network_active_plugins));
        $data['active_plugins'] = $all_active_plugins;

        return $data;
    }

    /**
     * Fetch all WWS settings (across Premium plugins too if installed) and compile them together into an array
     *
     * @since 1.14
     * @access public
     * @return array All settings from WWP, WWPP, WWOF, WWLC plugins
     */
    public function fetch_all_wws_settings()
    {
        global $wpdb;
        $settings = array();

        // WWP
        $sql_fetch_all_wws_settings = "SELECT option_name, option_value
    FROM {$wpdb->prefix}options
    WHERE option_name like 'wwp_%'
    OR option_name like 'wwpp_%'
    OR option_name like 'wwof_%'
    OR option_name like 'wwlc_%'";

        $result = $wpdb->get_results($sql_fetch_all_wws_settings, ARRAY_A);

        foreach ($result as $option) {
            if (isset($option) && isset($option['option_name']) && isset($option['option_value'])) {
                $settings[$option['option_name']] = $option['option_value'];
            }

        }

        // Unset unwanted settings
        unset($settings['wwp_settings_hash']);
        unset($settings['wwpp_settings_hash']);
        unset($settings['wwp_product_hash']);
        unset($settings['wwpp_product_hash']);
        unset($settings['wwp_product_cat_hash']);
        unset($settings['wwpp_product_cat_hash']);
        unset($settings['wwpp_update_data']);
        unset($settings['wwof_update_data']);
        unset($settings['wwlc_update_data']);
        unset($settings['wwof_order_form_v2_consumer_key']);
        unset($settings['wwof_order_form_v2_consumer_secret']);
        unset($settings['wwlc_security_recaptcha_secret_key']);
        unset($settings['wwlc_security_recaptcha_site_key']);

        // Return the settings as an array
        return $settings;
    }

    /**
     * Send the checkin
     *
     * @since 1.14
     * @access public
     * @param $override Flag to override if tracking is allowed or not
     * @param $ignore_last_checkin Flag to ignore that last checkin time check
     * @return BOOL Whether the checkin was sent successfully
     */
    public function send_checkin($override = false, $ignore_last_checkin = false)
    {

        // Don't track anything from our domains
        $home_url = trailingslashit(home_url());
        if (strpos($home_url, 'wholesalesuiteplugin.com') !== false) {
            return false;
        }

        // Check if tracking is allowed on this site
        if (!$this->tracking_allowed() && !$override) {
            return false;
        }

        // Send a maximum of once per week
        $last_send = get_option('wwp_usage_tracking_last_checkin');
        if (is_numeric($last_send) && $last_send > strtotime('-1 week') && !($ignore_last_checkin || defined('WWS_TESTING_SITE'))) {
            return false;
        }

        wp_remote_post('https://usg.rymeraplugins.com/v1/wwp-checkin/', array(
            'method'      => 'POST',
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking'    => false,
            'body'        => $this->get_data(),
            'user-agent'  => 'WWP/' . WWP_Helper_Functions::get_wwp_version() . '; ' . get_bloginfo('url'),
        ));

        // If we have completed successfully, recheck in 1 week
        update_option('wwp_usage_tracking_last_checkin', time());
        return true;
    }

    /**
     * Check if tracking is allowed on this site
     *
     * @since 1.14
     * @access public
     * @return BOOL whether this site can be tracked or not
     */
    private function tracking_allowed()
    {
        $allow_usage = get_option('wwp_anonymous_data', false);

        return ($allow_usage != false && $allow_usage != 'no') || WWP_Helper_Functions::has_paid_plugin_active();
    }

    /**
     * Schedule when we should send tracking data
     *
     * @since 1.14
     * @access public
     */
    public function schedule_send()
    {
        if (!wp_next_scheduled('wwp_usage_tracking_cron')) {
            $tracking           = array();
            $tracking['day']    = rand(0, 6);
            $tracking['hour']   = rand(0, 23);
            $tracking['minute'] = rand(0, 59);
            $tracking['second'] = rand(0, 59);
            $tracking['offset'] = ($tracking['day'] * DAY_IN_SECONDS) +
                ($tracking['hour'] * HOUR_IN_SECONDS) +
                ($tracking['minute'] * MINUTE_IN_SECONDS) +
                $tracking['second'];
            $tracking['initsend'] = strtotime("next sunday") + $tracking['offset'];

            wp_schedule_event($tracking['initsend'], 'weekly', 'wwp_usage_tracking_cron');
            update_option('wwp_usage_tracking_config', $tracking);
        }
    }

    /**
     * Check if the admin notice was opted in to tracking and if so, send the data and schedule the cron for future sends
     *
     * @since 1.14
     * @access public
     */
    public function check_for_optin()
    {
        if (!(!empty($_REQUEST['wwp_action']) && 'opt_into_tracking' === $_REQUEST['wwp_action'])) {
            return;
        }

        if (get_option('wwp_anonymous_data') == 'yes') {
            return;
        }

        if (WWP_Helper_Functions::has_paid_plugin_active()) {
            update_option('wwp_anonymous_data', 'yes');
            return;
        }

        update_option('wwp_anonymous_data', 'yes');
        $this->send_checkin(true, true);
        update_option('wwp_tracking_notice', 1);
    }

    /**
     * Check for optout via the admin notice and handle appropriately
     *
     * @since 1.14
     * @access public
     */
    public function check_for_optout()
    {
        if (!(!empty($_REQUEST['wwp_action']) && 'opt_out_of_tracking' === $_REQUEST['wwp_action'])) {
            return;
        }

        if (get_option('wwp_anonymous_data') == 'yes') {
            return;
        }

        if (WWP_Helper_Functions::has_paid_plugin_active()) {
            return;
        }

        update_option('wwp_anonymous_data', 'no');
        update_option('wwp_tracking_notice', 1);
    }

    /**
     * Add the cron schedule
     *
     * @since 1.14
     * @access public
     * @param $schedules The schedules array from the filter
     */
    public function add_schedules($schedules = array())
    {
        // Adds once weekly to the existing schedules.
        $schedules['weekly'] = array(
            'interval' => 604800,
            'display'  => __('Once Weekly', 'woocommerce-wholesale-prices'),
        );
        return $schedules;
    }

    /**
     * Set up the usage tracking notice for admin users. Only shows once so as not to annoy them.
     *
     * @since 1.14
     * @access public
     */
    public function wwp_admin_setup_usage_tracking_notice()
    {
        if (current_user_can('administrator')) {
            if (!is_network_admin()) {

                if (!get_option('wwp_tracking_notice') || defined('WWS_TESTING_SITE')) {

                    if (!get_option('wwp_anonymous_data', false)) {

                        if (!WWP_Helper_Functions::is_dev_url(network_site_url('/')) || defined('WWS_TESTING_SITE')) {

                            if (WWP_Helper_Functions::has_paid_plugin_active()) {
                                update_option('wwp_anonymous_data', 1);
                                return;
                            }

                            $optin_url  = add_query_arg('wwp_action', 'opt_into_tracking');
                            $optout_url = add_query_arg('wwp_action', 'opt_out_of_tracking');

                            echo '<div class="updated">';
                            echo '<p style="font-weight:700;">' . __('Wholesale Suite Usage Tracking Permission', 'woocommerce-wholesale-prices') . '</p>';
                            echo '<p>';
                            echo __('Allow Wholesale Suite to track plugin usage? Opt-in let us track usage data so we know with which WordPress configurations, themes and plugins we should test with.', 'woocommerce-wholesale-prices');
                            echo '</p>';
                            echo '<a href="' . esc_url($optin_url) . '" class="button-primary">' . __('Allow', 'woocommerce-wholesale-prices') . '</a>';
                            echo '&nbsp;<a href="' . esc_url($optout_url) . '" class="button-secondary">' . __('Do not allow', 'woocommerce-wholesale-prices') . '</a>';
                            echo '</p></div>';
                        } else {
                            // is testing site
                            update_option('wwp_tracking_notice', '1');
                        }
                    }
                }
            }
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
        // Schedule sending
        add_action('init', array($this, 'schedule_send'));
        add_filter('cron_schedules', array($this, 'add_schedules'));
        add_action('wwp_usage_tracking_cron', array($this, 'send_checkin'));

        // Handle admin notice for optin
        add_action('admin_notices', array($this, 'wwp_admin_setup_usage_tracking_notice'));
        add_action('network_admin_notices', array($this, 'wwp_admin_setup_usage_tracking_notice'));
        add_action('admin_head', array($this, 'check_for_optin'));
        add_action('admin_head', array($this, 'check_for_optout'));
    }
}
