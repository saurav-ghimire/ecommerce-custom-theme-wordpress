<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WWP_Wholesale_Roles
{

    private static $_instance;

    public static function getInstance()
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self;
        }

        return self::$_instance;

    }

    /**
     * Add custom user role.
     *
     * @param $roleKey
     * @param $roleName
     *
     * @since 1.0.0
     */
    public function addCustomRole($roleKey, $roleName)
    {

        global $wp_roles;
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        $customerRole = $wp_roles->get_role('customer'); // Copy customer role capabilities

        if ($customerRole) {

            do_action('wwp_action_before_add_custom_role', $roleKey, $roleName, $customerRole->capabilities);

            add_role($roleKey, $roleName, $customerRole->capabilities);

            do_action('wwp_action_after_add_custom_role', $roleKey, $roleName, $customerRole->capabilities);

        }

    }

    /**
     * Add custom capability to a given user role.
     *
     * @param $roleKey
     * @param $cap
     *
     * @since 1.0.0
     */
    public function addCustomCapability($roleKey, $cap)
    {

        do_action('wwp_action_before_add_custom_cap', $roleKey, $cap);

        $role = get_role($roleKey);
        $role->add_cap($cap);

        do_action('wwp_action_after_add_custom_cap', $roleKey, $cap);

    }

    /**
     * Remove custom user role.
     *
     * @param $roleKey
     *
     * @since 1.0.0
     */
    public function removeCustomRole($roleKey)
    {

        do_action('wwp_action_before_remove_custom_role', $roleKey);

        remove_role($roleKey);

        do_action('wwp_action_after_remove_custom_role', $roleKey);

    }

    /**
     * Remove custom user capability to a given role.
     *
     * @param $roleKey
     * @param $cap
     *
     * @since 1.0.0
     */
    public function removeCustomCapability($roleKey, $cap)
    {

        do_action('wwp_action_before_remove_custom_cap', $roleKey, $cap);

        $role = get_role($roleKey);

        if ($role instanceof WP_Role) {
            $role->remove_cap($cap);
        }

        do_action('wwp_action_after_remove_custom_cap', $roleKey, $cap);

    }

    /**
     * Register a custom role to the plugin custom role options, the plugin custom role options is used to track
     * all custom roles added via this plugin.
     *
     * @param $roleKey
     * @param $roleName
     * @param $attr
     *
     * @since 1.0.0
     */
    public function registerCustomRole($roleKey, $roleName, $attr)
    {

        do_action('wwp_action_before_register_custom_role', $roleKey, $roleName);

        $registeredCustomRoles = maybe_unserialize(get_option(WWP_OPTIONS_REGISTERED_CUSTOM_ROLES));

        $newRole = array('roleName' => $roleName);
        foreach ($attr as $attKey => $attVal) {
            $newRole[$attKey] = $attVal;
        }

        $newRole = apply_filters('wwp_filter_new_role', $newRole, $roleKey);

        $registeredCustomRoles[$roleKey] = $newRole;

        update_option(WWP_OPTIONS_REGISTERED_CUSTOM_ROLES, serialize($registeredCustomRoles));

        do_action('wwp_action_after_register_custom_role', $roleKey, $roleName);

    }

    /**
     * Unregister a custom role from the plugin custom role options, the plugin custom role options is used to track
     * all custom roles added via this plugin.
     *
     * @param $roleKey
     *
     * @since 1.0.0
     */
    public function unregisterCustomRole($roleKey)
    {

        do_action('wwp_action_before_unregister_custom_role', $roleKey);

        $registeredCustomRoles = maybe_unserialize(get_option(WWP_OPTIONS_REGISTERED_CUSTOM_ROLES));

        if (is_array($registeredCustomRoles) && array_key_exists($roleKey, $registeredCustomRoles));
        unset($registeredCustomRoles[$roleKey]);

        update_option(WWP_OPTIONS_REGISTERED_CUSTOM_ROLES, serialize($registeredCustomRoles));

        do_action('wwp_action_after_unregister_custom_role', $roleKey);

    }

    /**
     * Return all registered custom role from the plugin custom role options, the plugin custom role options is used to
     * track all custom roles added via this plugin.
     *
     * @since 1.0.0
     * @since 1.3.0 Refactor codebase. Make sure we return an array.
     * @access public
     *
     * @return array
     */
    public function getAllRegisteredWholesaleRoles()
    {

        $all_registered_wholesale_roles = maybe_unserialize(get_option(WWP_OPTIONS_REGISTERED_CUSTOM_ROLES));
        if (!is_array($all_registered_wholesale_roles)) {
            $all_registered_wholesale_roles = array();
        }

        return apply_filters('wwp_registered_wholesale_roles', $all_registered_wholesale_roles);

    }

    /**
     * Return all wordpress registered user roles.
     *
     * @return mixed
     *
     * @since 1.0.0
     */
    public function getAllRoles()
    {

        global $wp_roles;

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        return $wp_roles->get_names();

    }

    /**
     * Return all roles for the current user
     *
     * @return mixed
     *
     * @since 1.0.0
     * @since 1.2.10 Make sure it returns an array.
     * @since 1.6.0 Add support for passing $user object.
     *
     * @param WP_User|null $user WP_User object, null by default.
     */
    public function getUserRoles($user = null)
    {

        if (!function_exists('wp_get_current_user')) {
            include ABSPATH . "wp-includes/pluggable.php";
        }

        $current_user = is_null($user) ? wp_get_current_user() : $user;

        return $current_user->ID && is_array($current_user->roles) ? $current_user->roles : array();

    }

    /**
     * Return current user's wholesale role, array is empty if none.
     * We return an array coz we do not close the possibility of allowing a user having multiple wholesale roles in the future.
     * But for the mean time, tho it returns an array, it will only return an array with single element, and that element is the user's current wholesale role.
     *
     * @since 1.0.0
     * @since 1.2.10 Re-index the returned array of wholesale roles.
     * @since 1.6.0 Add support for passing $user object.
     * @since 1.6.4 Add 'wwp_user_wholesale_role' filter to allow 3rd party codes to alter the returned current user's wholesale role.
     * Note that since version 1.0.0 of this plugin, this function returns an array with only 1 element, and that one element is considered
     * as the user's current wholesale role. That is why in various places in this and wwpp's code base we are using [0] to get the first element of the array
     * the array value that was returned by this function, as use it as the current user's wholesale role.
     * As of this version, we do not support multiple wholesale role for one user. The doors are not closed however that why we set this to return an array.
     *
     * @param WP_User|null $user WP_User object, null by default.
     * @return array Array of user wholesale roles.
     */
    public function getUserWholesaleRole($user = null)
    {

        $wholesaleRoleKeys = array();

        foreach ($this->getAllRegisteredWholesaleRoles() as $roleKey => $roleName) {
            $wholesaleRoleKeys[] = $roleKey;
        }

        if (!function_exists('wp_get_current_user')) {
            include ABSPATH . "wp-includes/pluggable.php";
        }

        $user = is_null($user) ? wp_get_current_user() : $user;

        return apply_filters('wwp_user_wholesale_role', array_values(array_intersect($this->getUserRoles($user), $wholesaleRoleKeys)), $user);

    }

    /**
     * Add current user wholesale role, if any, on body tag classes.
     *
     * @since 1.1.2
     * @since 1.4.0 Refactor codebase.
     * @access public
     *
     * @param array $classes Array of classes for the body tag.
     * @return array Filtered array of classes for the body tag.
     */
    public function add_wholesale_role_to_body_class($classes)
    {

        $wholesale_role = $this->getUserWholesaleRole();

        if (is_array($wholesale_role) && !empty($wholesale_role)) {
            foreach ($wholesale_role as $role) {
                $classes[] = $role;
            }
        }

        return $classes;

    }

    /**
     * Only return wholesale customer role only if WWP is active and WWPP is deactivated.
     *
     * @since 1.11
     * @access public
     *
     * @param array $wholesale_roles
     * @return array
     */
    public function filter_registered_wholesale_roles($wholesale_roles)
    {

        if (!WWP_Helper_Functions::is_plugin_active('woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php')) {

            if (isset($wholesale_roles['wholesale_customer'])) {
                return array('wholesale_customer' => $wholesale_roles['wholesale_customer']);
            }

        }

        return $wholesale_roles;

    }

    /**
     * Integration of WC Navigation Bar.
     *
     * @since 1.11.3
     * @access public
     */
    public function wc_navigation_bar()
    {

        if (function_exists('wc_admin_connect_page')) {
            wc_admin_connect_page(
                array(
                    'id'        => 'wholesale-roles-page',
                    'screen_id' => 'wholesale_page_wwpp-wholesale-roles-page',
                    'title'     => __('Wholesale Roles', 'woocommerce-wholesale-prices'),
                )
            );
        }

    }

    /**
     * Get wholesale user ids.
     *
     * @since 2.0
     * @access public
     */
    public function get_all_wholesale_user_ids()
    {

        global $wpdb;

        // Get wholesale users ids
        $wholesale_users_query = "SELECT $wpdb->users.ID
                                    FROM $wpdb->users
                                    INNER JOIN $wpdb->usermeta
                                    ON $wpdb->users.ID = $wpdb->usermeta.user_id
                                    WHERE $wpdb->usermeta.meta_key = '" . $wpdb->prefix . "capabilities'
                                    AND (";

        $counter     = 1;
        $total_roles = count($this->getAllRegisteredWholesaleRoles());

        foreach ($this->getAllRegisteredWholesaleRoles() as $role_key => $role_data) {

            $wholesale_users_query .= " $wpdb->usermeta.meta_value LIKE '%$role_key%' ";

            if ($counter < $total_roles) {
                $wholesale_users_query .= " OR ";
            }

            $counter++;

        }

        $wholesale_users_query .= ")";

        $query_result       = $wpdb->get_results($wholesale_users_query, ARRAY_A);
        $wholesale_user_ids = array();

        foreach ($query_result as $result) {
            $wholesale_user_ids[] = $result['ID'];
        }

        return $wholesale_user_ids;

    }

    /*
    |--------------------------------------------------------------------------
    | Execute Model
    |--------------------------------------------------------------------------
     */

    /**
     * Execute model.
     *
     * @since 1.4.0
     * @access public
     */
    public function run()
    {

        add_filter('body_class', array($this, 'add_wholesale_role_to_body_class'), 10, 1);
        add_filter('wwp_registered_wholesale_roles', array($this, 'filter_registered_wholesale_roles'), 10, 1);
        add_action('init', array($this, 'wc_navigation_bar'));

    }

}
