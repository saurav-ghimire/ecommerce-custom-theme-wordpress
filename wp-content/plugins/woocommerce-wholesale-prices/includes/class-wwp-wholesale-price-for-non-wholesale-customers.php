<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WWP_Wholesale_Prices_For_Non_Wholesale_Customers
{

    /** ===============================================================================================================
     *  Class Properties
     *===============================================================================================================*/

    /**
     * Property that holds single main instance of WWP_Wholesale_Prices_For_Non_Wholesale_Customers
     *
     * @since 1.15.0
     * @access private
     * @var WWP_Wholesale_Prices_For_Non_Wholesale_Customers
     */
    private static $_instance;

    /**
     * Model that houses the logic of retrieving information relating to wholesale role/s of a user.
     *
     * @since 1.15.0
     * @access private
     * @var WWP_Wholesale_Roles
     */
    private $_wwp_wholesale_roles;

    /**
     * Model that houses the logic of retrieving information relating to woocommerce rest api.
     *
     * @since 1.16.1
     * @access private
     * @var WWP_Rest_API_Client
     */
    private $_wwp_rest_api_client;

    /** ===============================================================================================================
     *  Class Methods
     *===============================================================================================================*/

    /**
     * WWP_Wholesale_Prices_For_Non_Wholesale_Customers constructor.
     *
     * @since 1.3.0
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Wholesale_Prices_For_Non_Wholesale_Customers model.
     */
    public function __construct($dependencies = array())
    {
        if (isset($dependencies['WWP_Wholesale_Roles'])) {
            $this->_wwp_wholesale_roles = $dependencies['WWP_Wholesale_Roles'];
        }

        if (isset($dependencies['WWP_Rest_API_Client'])) {
            $this->_wwp_rest_api_client = $dependencies['WWP_Rest_API_Client'];
        }
    }

    /**
     * Ensure that only one instance of WWP_Wholesale_Prices_For_Non_Wholesale_Customers is loaded (singleton pattern)
     *
     * @since 1.15.0
     * @access public
     * @param array $dependencies
     * @return WWP_Wholesale_Prices_For_Non_Wholesale_Customers
     */
    public static function instance($dependencies)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($dependencies);
        }

        return self::$_instance;
    }

    /**
     * This function will process product variables to be used in show wholesale prices to non wholesale customers
     *
     * @since 1.16.1
     * @access private
     *
     * @param string|array $data Data from api results
     * @param string $role_name Wholesale role name
     * @return string $html
     */
    private function _process_variable_product_wholesale_price($data, $role_name)
    {
        $price_html = $data->wholesale_data->price_html;

        $result = __(str_replace('Wholesale Price:', ucwords($role_name), $price_html), 'woocommerce-wholesale-prices');

        return $result;
    }

    /**
     * This function will process simple product to be used in show wholesale prices to non wholesale customers
     *
     * @since 1.16.1
     * @access private
     *
     * @param string|array $data Data from api results
     * @param string $role_name Wholesale role name
     * @return string $html
     */
    private function _process_simple_product_wholesale_price($data, $role_name)
    {

        $price_html = $data->wholesale_data->price_html;

        $result = str_replace(__('Wholesale Price:', 'woocommerce-wholesale-prices'), ucwords($role_name), $price_html);

        return $result;
    }

    /**
     * This function is responsible for the prices of wholesale roles if each products, this is triggered by "Click to See Wholesale Prices"
     *
     * @since 1.15.0
     * @since 1.15.1 removing function of getting ajax request, we dont need it anymore, since data is now encoded using base64 utf8 and added to html data attribute for fetching later on in js script for faster and better user experience.
     * - rename function from get_product_wholesale_prices_ajax to get_product_wholesale_prices
     *
     * @access public
     * @param {*} $product_id
     * @return string html
     */
    public function get_product_wholesale_prices_ajax()
    {
        global $wc_wholesale_prices;

        /**
         * Verify nonce if its the same as we created, if not then we return
         */
        if (isset($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'wwp_nonce')) {
            return;
        }

        $product_id             = $_POST['data']['product_id'];
        $wholesale_role_options = array();
        $wholesale_roles        = $wc_wholesale_prices->wwp_wholesale_roles->getAllRegisteredWholesaleRoles();

        $consumer_key    = trim(get_option('wwp_woocommerce_api_consumer_key'));
        $consumer_secret = trim(get_option('wwp_woocommerce_api_consumer_secret'));
        $args            = array();
        $api_result      = '';

        if (WWP_Helper_Functions::is_wwpp_active()) {
            $wholesale_role_options = get_option('wwp_non_wholesale_wholesale_role_select2');
        } else {
            $wholesale_role_options = array_keys($wholesale_roles);
        }

        $product_endpoint = "products/$product_id";

        foreach ($wholesale_roles as $wholesale_role => $data) {
            if (in_array($wholesale_role, $wholesale_role_options)) {
                $args = array(
                    'wholesale_data' => array(
                        'wholesale_role' => $wholesale_role,
                        'fields'         => 'wholesale_data,price,variations,type,status',
                    ),
                );

                $result = $this->_wwp_rest_api_client->get($args, $consumer_key, $consumer_secret, $product_endpoint);

                if (isset($result->status)) {

                    if ($result->status === 'publish' && in_array($result->type, array('simple', 'variation'))) {

                        $api_result .= $this->_process_simple_product_wholesale_price($result, $data['roleName']);

                    } elseif ($result->status === 'publish' && $result->type === 'variable') {

                        $api_result .= $this->_process_variable_product_wholesale_price($result, $data['roleName']);

                    }

                }
            }
        }

        if (!empty($wholesale_roles)) {

            $wwlc_registration_link = $this->registration_link_filter();

            if (WWP_Helper_Functions::is_wwlc_active() && !empty($wwlc_registration_link)) {
                $api_result .= "<div class='register-link'><a href='" . $wwlc_registration_link . "'><p><strong>" . $this->registration_text_filter() . "</strong></p></a></div>";
            }

            echo $api_result;
            die();

        }
    }

    /**
     * Register custom fields
     *
     * @since 1.15.0
     * @access private
     */
    public function register_settings_field_options()
    {
        // Show wholesale price to non wholesale users settings options
        if (get_option('wwp_see_wholesale_prices_replacement_text') == false) {
            update_option('wwp_see_wholesale_prices_replacement_text', 'See wholesale prices');
        }

        // NOTE: Default role value is added in add_default_wholesale_role_value

        if (get_option('wwp_price_settings_register_text') == false) {
            update_option('wwp_price_settings_register_text', 'Click here to register as a wholesale customer');
        }

        if (get_option('wwp_non_wholesale_show_in_products') == false) {
            update_option('wwp_non_wholesale_show_in_products', 'yes');
        }

        if (get_option('wwp_non_wholesale_show_in_shop') == false) {
            update_option('wwp_non_wholesale_show_in_shop', 'yes');
        }

        if (get_option('wwp_non_wholesale_show_in_wwof') == false) {
            update_option('wwp_non_wholesale_show_in_wwof', 'yes');
        }

        // WooCommerce API Keys
        if (get_option('wwp_woocommerce_api_consumer_key') == false) {
            update_option('wwp_woocommerce_api_consumer_key', null);
        }

        if (get_option('wwp_woocommerce_api_consumer_secret') == false) {
            update_option('wwp_woocommerce_api_consumer_secret', null);
        }
    }

    /**
     * This will get the registration wholesale page if WWLC is active/installed from the selected options in WWLC Registration Settings.
     *
     * @since 1.15.0
     * @since 1.15.1
     * @access public
     * @return permalink for registration page
     */
    public function registration_link_filter()
    {
        $wwlc_registration_page = get_option('wwlc_general_registration_page', '');

        return apply_filters('wwp_non_wholesale_registration_link_filter', WWP_Helper_Functions::is_wwlc_active() && !empty($wwlc_registration_page) ? get_permalink($wwlc_registration_page) : '');

    }

    /**
     * This will display registration text message for non wholesale users to register as a wholesale customer
     *
     * @since 1.15.0
     * @since 1.15.1
     * @access public
     * @return string registration text message which is filterable
     */
    public function registration_text_filter()
    {
        $registration_text = get_option('wwp_price_settings_register_text', '');

        return apply_filters('wwp_non_wholesale_registration_text_filter', empty($registration_text) ? __('Click here to register as a wholesale customer', 'woocommerce-wholesale-prices') : $registration_text);

    }

    /**
     * This function display's "Click to See Wholesale Prices" on Shops, Single Products, Upsells, Cross sells
     * Wholesale Order Form, this function will also trigger popover Wholesale Price Box if click.
     *
     * @since 1.15.0
     * @since 1.15.1 added function get_product_wholesale_prices
     * @access public
     * @return string $message containing html string
     */
    public function display_replacement_message_to_non_wholesale($product = null)
    {

        if (is_null($product)) {
            return;
        }

        $show_wholesale_prices_text      = false;
        $product_id                      = $product->get_id();
        $is_wwpp_active                  = WWP_Helper_Functions::is_wwpp_active();
        $replacement_text                = get_option('wwp_see_wholesale_prices_replacement_text');
        $wholesale_role_general_discount = get_option('wwpp_option_wholesale_role_general_discount_mapping', array());
        $wholesale_price_options         = $is_wwpp_active ? get_option('wwp_non_wholesale_wholesale_role_select2', array()) : array_keys($this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles());
        $show_in_product                 = get_option('wwp_non_wholesale_show_in_products');
        $show_in_shop                    = get_option('wwp_non_wholesale_show_in_shop');
        $show_in_wwof                    = get_option('wwp_non_wholesale_show_in_wwof');
        $variable_parent_id              = $product->get_type('variation') ? $product->get_parent_id() : 0;
        $show_wholesale_prices           = get_option('wwp_prices_settings_show_wholesale_prices_to_non_wholesale');

        $message = apply_filters('wwp_display_non_wholesale_replacement_message', empty($replacement_text) ? __('See wholesale prices', 'woocommerce-wholesale-prices') : $replacement_text);

        if (!empty($wholesale_price_options)) {

            // For PHP 8.0.8 Compatibility
            if (is_array($wholesale_price_options) || is_object($wholesale_price_options)) {

                foreach ($wholesale_price_options as $wholesale_role) {

                    $wholesale_price                 = get_post_meta($product_id, $wholesale_role . '_wholesale_price', true);
                    $have_wholesale_price            = get_post_meta($product_id, $wholesale_role . '_have_wholesale_price', true);
                    $variations_with_wholesale_price = get_post_meta($product_id, $wholesale_role . '_variations_with_wholesale_price');

                    // Discount is set in product level
                    if ($wholesale_price > 0 || !empty($variations_with_wholesale_price)) {
                        $show_wholesale_prices_text = true;
                        break;
                    }

                    if ($is_wwpp_active) {

                        $ignore_cat_level                        = get_post_meta($product_id, 'wwpp_ignore_cat_level_wholesale_discount', true);
                        $ignore_role_level                       = get_post_meta($product_id, 'wwpp_ignore_role_level_wholesale_discount', true);
                        $have_wholesale_price_cat_level          = get_post_meta($product_id, $wholesale_role . '_have_wholesale_price_set_by_product_cat', true);
                        $variable_have_wholesale_price_cat_level = get_post_meta($variable_parent_id, $wholesale_role . '_have_wholesale_price_set_by_product_cat', true);

                        // Category wholesale price and ignore category level should not be set
                        if (($have_wholesale_price === 'yes' || $have_wholesale_price_cat_level === 'yes' || $variable_have_wholesale_price_cat_level) && $ignore_cat_level != 'yes') {

                            $show_wholesale_prices_text = true;
                            break;

                        }

                        // General Discount is set
                        if (!empty($wholesale_role_general_discount) && $ignore_role_level != 'yes') {

                            $show_wholesale_prices_text = true;
                            break;

                        }

                    }

                }

            }

        }

        $show_wholesale_prices_text = apply_filters('wwp_show_wholesale_prices_to_non_wholesale_customers', $show_wholesale_prices_text);

        if (
            $show_wholesale_prices_text && 'yes' === $show_wholesale_prices && (
                (is_product() && $show_in_product == 'yes') ||
                (is_shop() && $show_in_shop == 'yes') ||
                ($show_in_wwof == 'yes')
            )
        ) {

            $message = '<a href="#" role="button" type="button" data-trigger="focus" class="wwp_show_wholesale_prices_link popover_wholesale_replacement_message nostyle" rel="popover"  data-html="true" data-product_id="' . $product_id . '" data-wholesale_data=""><span>' . $message . '</span></a>';

            $message = '<div class="wwp_show_wholesale_prices_text">' . $message . '</div>';

            return $message;

        }

    }

    /**
     * Show wholesale price to non wholesale customer under product price
     * WooCommerce Admin, Shop Managers, Guest, and Regular Customers should be able to access the Wholesale Prices Box
     *
     * @since 1.16.1
     * @access public
     * @return html content
     */
    public function add_click_wholesale_price_for_non_wholesale_customers($price, $product)
    {

        global $wc_wholesale_prices;

        $product_id            = $product->get_id();
        $user_wholesale_role   = $wc_wholesale_prices->wwp_wholesale_roles->getUserWholesaleRole();
        $show_wholesale_prices = get_option('wwp_prices_settings_show_wholesale_prices_to_non_wholesale');

        /**
         * Check if the screen is in product edit page if user is admin or shop manager, make sure that the "See wholesale price" text will not show in products edit page.
         */
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if (isset($screen) && $screen->parent_base == 'edit') {
                return $price;
            }
        }

        if ($product_id && $show_wholesale_prices === 'yes' && (!is_user_logged_in() || current_user_can('manage_woocommerce') || empty($user_wholesale_role)) && (is_shop() || is_product())) {

            if (in_array(WWP_Helper_Functions::wwp_get_product_type($product), array('simple', 'variable'))) {

                $price .= $this->display_replacement_message_to_non_wholesale($product);

            }

        }

        return $price;
    }

    /**
     * Show Wholesale prices to non wholesale customer in WWOF
     * This function is being called by filter woocommerce_get_price_html
     *
     * @since 1.16.1
     * @access public
     */
    public function show_wholesale_price_in_wwof($price, $product)
    {
        global $wc_wholesale_prices;

        $price_html          = '';
        $user_wholesale_role = $wc_wholesale_prices->wwp_wholesale_roles->getUserWholesaleRole();

        // Check if WWOF beta is performing API request to WWP/WWPP
        // If value wholesale role is present then the current user is wholesale customer
        if (isset($_REQUEST['wholesale_role']) && !empty($_REQUEST['wholesale_role'])) {
            return $price;
        }

        if ((!is_product() && !is_shop() && !is_cart()) && (!is_user_logged_in() || current_user_can('manage_woocommerce') || empty($user_wholesale_role))) {

            $price_html = $this->display_replacement_message_to_non_wholesale($product);
        }

        return $price .= $price_html;

    }

    /**
     * Add default role value.
     * We need to have default value in order to use the feature "Show Wholesale Price to non-wholesale users"
     * Re-add the value when user activates WWPP then removes the role.
     *
     * @since 1.16.1
     * @access public
     */
    public function add_default_wholesale_role_value()
    {

        $role_value = get_option('wwp_non_wholesale_wholesale_role_select2', array());

        // If only WWP is active and WWPP is deactivated and if role is empty then add a default value.
        // Note WWP will always have a default role value else the feature will be useless.
        if ((empty($role_value) || $role_value == false) && !WWP_Helper_Functions::is_wwpp_active()) {

            update_option('wwp_non_wholesale_wholesale_role_select2', array('wholesale_customer'));

        }

    }

    /**
     * This function is responsible in executing all actions needed to run our application
     *
     * @since 1.15.0
     * @access public
     */
    public function run()
    {

        // We will only use the feature if wc api key is provided and is valid
        if (WWP_REST_API_Keys::is_api_key_valid()) {

            // Add default role value
            add_action('init', array($this, 'add_default_wholesale_role_value'));

            // Get available wholesale prices
            add_action('wp_ajax_get_product_wholesale_prices_ajax', array($this, 'get_product_wholesale_prices_ajax'));
            add_action('wp_ajax_nopriv_get_product_wholesale_prices_ajax', array($this, 'get_product_wholesale_prices_ajax'));

            // Display "See wholesale prices" text
            add_filter('woocommerce_get_price_html', array($this, 'add_click_wholesale_price_for_non_wholesale_customers'), 10, 2);

            // Display "See wholesale prices" text in v2 and old form
            add_filter('woocommerce_get_price_html', array($this, 'show_wholesale_price_in_wwof'), 9999, 2);

        }

    }

}