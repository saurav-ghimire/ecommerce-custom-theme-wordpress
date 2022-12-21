<?php
if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class WWP_Wholesale_Prices
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of WWP_Wholesale_Prices.
     *
     * @since 1.3.0
     * @access private
     * @var WWP_Wholesale_Prices
     */
    private static $_instance;

    /**
     * Model that houses the logic of retrieving information relating to wholesale role/s of a user.
     *
     * @since 1.5.0
     * @access private
     * @var WWP_Wholesale_Roles
     */
    private $_wwp_wholesale_roles;

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * WWP_Wholesale_Prices constructor.
     *
     * @since 1.3.0
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Wholesale_Prices model.
     */
    public function __construct($dependencies = array())
    {

        if (isset($dependencies['WWP_Wholesale_Roles'])) {
            $this->_wwp_wholesale_roles = $dependencies['WWP_Wholesale_Roles'];
        }

    }

    /**
     * Ensure that only one instance of WWP_Wholesale_Prices is loaded or can be loaded (Singleton Pattern).
     *
     * @since 1.3.0
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Wholesale_Prices model.
     * @return WWP_Wholesale_Prices
     */
    public static function instance($dependencies)
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self($dependencies);
        }

        return self::$_instance;

    }

    /**
     * Ensure that only one instance of WWP_Wholesale_Prices is loaded or can be loaded (Singleton Pattern).
     *
     * @since 1.3.0
     * @access public
     * @deprecated: Will be remove on future versions
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_Wholesale_Prices model.
     * @return WWP_Wholesale_Prices
     */
    public static function getInstance()
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self;
        }

        return self::$_instance;

    }

    /**
     * Return product wholesale price for a given wholesale user role.
     * Still being used on WWOF 1.7.8
     *
     * @deprecated: Will be remove on future versions
     * @since 1.0.0
     * @param $product_id
     * @param $user_wholesale_role
     * @return string
     */
    public static function getUserProductWholesalePrice($product_id, $user_wholesale_role)
    {

        return self::getProductWholesalePrice($product_id, $user_wholesale_role);

    }

    /**
     * Return product wholesale price for a given wholesale user role.
     *
     * @param $product_id
     * @param $user_wholesale_role
     * @param $quantity
     * @deprecated To be removed for future versions.
     *
     * @return string
     * @since 1.0.0
     */
    public static function getProductWholesalePrice($product_id, $user_wholesale_role, $quantity = 1)
    {

        if (empty($user_wholesale_role)) {

            return '';

        } else {

            if (WWP_ACS_Integration_Helper::aelia_currency_switcher_active()) {

                $baseCurrencyWholesalePrice = $wholesale_price = get_post_meta($product_id, $user_wholesale_role[0] . '_wholesale_price', true);

                if ($baseCurrencyWholesalePrice) {

                    $activeCurrency = get_woocommerce_currency();
                    $baseCurrency   = WWP_ACS_Integration_Helper::get_product_base_currency($product_id);

                    if ($activeCurrency == $baseCurrency) {
                        $wholesale_price = $baseCurrencyWholesalePrice;
                    }
                    // Base Currency
                    else {

                        $wholesale_price = get_post_meta($product_id, $user_wholesale_role[0] . '_' . $activeCurrency . '_wholesale_price', true);

                        if (!$wholesale_price) {

                            /*
                             * This specific currency has no explicit wholesale price (Auto). Therefore will need to convert the wholesale price
                             * set on the base currency to this specific currency.
                             *
                             * This is why it is very important users set the wholesale price for the base currency if they want wholesale pricing
                             * to work properly with aelia currency switcher plugin integration.
                             */
                            $wholesale_price = WWP_ACS_Integration_Helper::convert($baseCurrencyWholesalePrice, $activeCurrency, $baseCurrency);

                        }

                    }

                    $wholesale_price = apply_filters('wwp_filter_' . $activeCurrency . '_wholesale_price', $wholesale_price, $product_id, $user_wholesale_role, $quantity);

                } else {
                    $wholesale_price = '';
                }
                // Base currency not set. Ignore the rest of the wholesale price set on other currencies.

            } else {
                $wholesale_price = get_post_meta($product_id, $user_wholesale_role[0] . '_wholesale_price', true);
            }

            return apply_filters('wwp_filter_wholesale_price', $wholesale_price, $product_id, $user_wholesale_role, $quantity);

        }

    }

    /**
     * Get product raw wholesale price. Without being passed through any filter.
     *
     * @since 1.5.0
     * @since 1.6.3 Removed $quantity variable from the list of variables being passed to 'wwp_filter_' . $activeCurrency . '_wholesale_price' filter.
     * @access public
     *
     * @param int     $product_id          Product id.
     * @param array   $user_wholesale_role Array of user wholesale roles.
     * @return string Filtered wholesale price.
     */
    public static function get_product_raw_wholesale_price($product_id, $user_wholesale_role)
    {

        if (empty($user_wholesale_role)) {
            $wholesale_price = '';
        } else {

            if (WWP_ACS_Integration_Helper::aelia_currency_switcher_active()) {

                $baseCurrencyWholesalePrice = $wholesale_price = get_post_meta($product_id, $user_wholesale_role[0] . '_wholesale_price', true);

                if ($baseCurrencyWholesalePrice) {

                    $activeCurrency = get_woocommerce_currency();
                    $baseCurrency   = WWP_ACS_Integration_Helper::get_product_base_currency($product_id);

                    if ($activeCurrency == $baseCurrency) {
                        $wholesale_price = $baseCurrencyWholesalePrice;
                    }
                    // Base Currency
                    else {

                        $wholesale_price = get_post_meta($product_id, $user_wholesale_role[0] . '_' . $activeCurrency . '_wholesale_price', true);

                        if (!$wholesale_price) {

                            /*
                             * This specific currency has no explicit wholesale price (Auto). Therefore will need to convert the wholesale price
                             * set on the base currency to this specific currency.
                             *
                             * This is why it is very important users set the wholesale price for the base currency if they want wholesale pricing
                             * to work properly with aelia currency switcher plugin integration.
                             */
                            $wholesale_price = WWP_ACS_Integration_Helper::convert($baseCurrencyWholesalePrice, $activeCurrency, $baseCurrency);

                        }

                    }

                    $wholesale_price = apply_filters('wwp_filter_' . $activeCurrency . '_wholesale_price', $wholesale_price, $product_id, $user_wholesale_role);

                } else {
                    $wholesale_price = '';
                }
                // Base currency not set. Ignore the rest of the wholesale price set on other currencies.

            } else {
                $wholesale_price = get_post_meta($product_id, $user_wholesale_role[0] . '_wholesale_price', true);
            }

        }

        return $wholesale_price;

    }

    /**
     * Return product wholesale price for a given wholesale user role.
     * With 'wwp_filter_wholesale_price_shop' filter already applied.
     * Replaces getProductWholesalePrice.
     *
     * @since 1.5.0
     * @since 1.6.0 Deprecated.
     * @access public
     *
     * @param int     $product_id          Product id.
     * @param array   $user_wholesale_role Array of user wholesale roles.
     * @return string Filtered wholesale price.
     */
    public static function get_product_wholesale_price_on_shop($product_id, $user_wholesale_role)
    {

        $price_arr = self::get_product_wholesale_price_on_shop_v3($product_id, $user_wholesale_role);
        return $price_arr['wholesale_price'];

    }

    /**
     * Replacement for 'get_product_wholesale_price_on_shop'.
     * Returns an array containing wholesale price both passed through and not passed through taxing.
     *
     * @since 1.6.0
     * @since 1.6.3 Add 'wwp_filter_wholesale_price_shop_v2' filter.
     * @since 1.10  Deprecated.
     * @access public
     *
     * @param int     $product_id          Product id.
     * @param array   $user_wholesale_role Array of user wholesale roles.
     * @return array Array of wholesale price data.
     */
    public static function get_product_wholesale_price_on_shop_v2($product_id, $user_wholesale_role)
    {

        WWP_Helper_Functions::deprecated_function(debug_backtrace(), 'WWP_Wholesale_Prices::get_product_wholesale_price_on_shop_v2()', '1.10', 'WWP_Wholesale_Prices::get_product_wholesale_price_on_shop_v3()');

        $price_arr = array();

        $per_product_level_wholesale_price = self::get_product_raw_wholesale_price($product_id, $user_wholesale_role);

        if (empty($per_product_level_wholesale_price)) {

            $result = apply_filters('wwp_filter_wholesale_price_shop', array('source' => 'per_product_level', 'wholesale_price' => $per_product_level_wholesale_price), $product_id, $user_wholesale_role, null, null);

            $price_arr['wholesale_price_with_no_tax'] = trim($result['wholesale_price']);
            $price_arr['source']                      = $result['source'];

        } else {

            $price_arr['wholesale_price_with_no_tax'] = $per_product_level_wholesale_price;
            $price_arr['source']                      = 'per_product_level';

        }

        $price_arr['wholesale_price'] = trim(apply_filters('wwp_pass_wholesale_price_through_taxing', $price_arr['wholesale_price_with_no_tax'], $product_id, $user_wholesale_role));

        return apply_filters('wwp_filter_wholesale_price_shop_v2', $price_arr, $product_id, $user_wholesale_role);

    }

    /**
     * Replacement for get_product_wholesale_price_on_shop_v2.
     * Returns an array containing of the raw wholesale price, wholesale price for display, and wholesale price without tax.
     * - wholesale_price             = the price used in display after all calculation. dependent on all settings.
     * - raw_wholesale_price         = the raw amount value inputted on the wholesale price field.
     * - wholesale_price_with_no_tax = the wholesale price deducted of the calculated tax.
     *
     * @since 1.9
     * @since 1.12 "WooCommerce Currency Switcher" plugin support. Wrap wholesale_price_raw with "woocommerce_product_get_price" filter
     *              so that the wholesale prices is properly converted to selected currency.
     * @since 2.0.2 Add filter to be used for caching wholesale price data. Feature is available in premium.
     * @access public
     *
     * @param int     $product_id          Product id.
     * @param array   $user_wholesale_role Array of user wholesale roles.
     * @return array Array of wholesale price data.
     */
    public static function get_product_wholesale_price_on_shop_v3($product_id, $user_wholesale_role)
    {

        $price_arr  = array();
        $user_id    = apply_filters('wwp_wholesale_price_current_user_id', get_current_user_id());
        $product    = wc_get_product($product_id);
        $cache_data = apply_filters('wwp_get_product_wholesale_price_on_shop_v3_cache', false, $user_id, $product, $product_id, $user_wholesale_role);

        if (!empty($cache_data)) {

            $price_arr = $cache_data;

        } else {

            $per_product_level_wholesale_price = self::get_product_raw_wholesale_price($product_id, $user_wholesale_role);

            if (empty($per_product_level_wholesale_price)) {

                $result = apply_filters('wwp_filter_wholesale_price_shop', array('source' => 'per_product_level', 'wholesale_price' => $per_product_level_wholesale_price), $product_id, $user_wholesale_role, null, null);

                $price_arr['wholesale_price_raw'] = trim($result['wholesale_price']);
                $price_arr['source']              = $result['source'];

            } else {

                $price_arr['wholesale_price_raw'] = $per_product_level_wholesale_price;
                $price_arr['source']              = 'per_product_level';

            }

            // Single Product Page Wholesale Price "WooCommerce Currency Switcher" plugin support
            // "WooCommerce Currency Switcher" must be enabled and "Aelia Currency Switcher for WooCommerce" must be disabled.
            if (
                WWP_Helper_Functions::is_plugin_active('woocommerce-currency-switcher/index.php') &&
                !WWP_Helper_Functions::is_plugin_active('woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php')
            ) {
                if (!empty($price_arr['wholesale_price_raw']) && $price_arr['source'] == 'per_product_level') {
                    $price_arr['wholesale_price_raw'] = apply_filters('woocommerce_product_get_price', $price_arr['wholesale_price_raw'], $product);
                }
            }

            $price_arr['wholesale_price'] = trim(apply_filters('wwp_pass_wholesale_price_through_taxing', $price_arr['wholesale_price_raw'], $product_id, $user_wholesale_role));

            // when product price is inclusive of tax, we use the calculated wholesale_price here cause it has been deducted by tax.
            if (wc_prices_include_tax() && $price_arr['wholesale_price_raw']) {
                $price_arr['wholesale_price_with_no_tax'] = WWP_Helper_Functions::wwp_get_price_excluding_tax($product, array('qty' => 1, 'price' => $price_arr['wholesale_price_raw']));
            } else {
                $price_arr['wholesale_price_with_no_tax'] = $price_arr['wholesale_price_raw'];
            }

            $price_arr['wholesale_price_with_tax'] = WWP_Helper_Functions::wwp_get_price_including_tax($product, array('qty' => 1, 'price' => $price_arr['wholesale_price_raw']));

            if (isset($price_arr['wholesale_price']) && $price_arr['wholesale_price'] > 0) {

                do_action('wwp_after_get_product_wholesale_price_on_shop_v3', $user_id, $product, $product_id, $user_wholesale_role, $price_arr);

            }

        }

        return apply_filters('wwp_filter_wholesale_price_shop_v2', $price_arr, $product_id, $user_wholesale_role);
    }

    /**
     * Return product wholesale price for a given wholesale user role.
     * With 'wwp_filter_wholesale_price_cart' filter already applied.
     * The wholesale price returned is not passed through taxing filters.
     * No need to do it tho, coz we hooking on 'before_calculate_totals' hook so after our wholesale price computation,
     * WC will take care of passing it through taxing options.
     *
     * @since 1.5.0
     * @since 1.6.0 Refactor codebase.
     * @since 1.12 Compatibility with "WooCommerce Currency Switcher by PluginUs.NET. Woo Multi Currency and Woo Multi Pay" plugin
     *
     * @access public
     *
     * @param int     $product_id          Product id.
     * @param array   $user_wholesale_role Array of user wholesale roles.
     * @param array   $cart_item           Cart item data.
     * @return string Filtered wholesale price.
     */
    public static function get_product_wholesale_price_on_cart($product_id, $user_wholesale_role, $cart_item, $cart_object)
    {

        $wholesale_price = self::get_product_raw_wholesale_price($product_id, $user_wholesale_role);

        global $WOOCS;

        if ($WOOCS && empty($wholesale_price)) {
            $_REQUEST['woocs_block_price_hook'] = true;
        }

        $result = apply_filters('wwp_filter_wholesale_price_cart', array('source' => 'per_product_level', 'wholesale_price' => $wholesale_price), $product_id, $user_wholesale_role, $cart_item, $cart_object);

        if ($WOOCS && empty($wholesale_price)) {
            unset($_REQUEST['woocs_block_price_hook']);
        }

        return isset($result['wholesale_price']) ? trim($result['wholesale_price']) : '';

    }

    /**
     * Get wholesale price suffix.
     *
     * @since 1.6.0
     * @since 1.7.0  When '{price_including_tax}', '{price_excluding_tax}' tags are used in the 'Price display suffix' dont return any computation since it will just use the regular price instead of wholesale price.
     * @since 1.11.5 We now support '{price_including_tax}', '{price_excluding_tax}' tags in our wholesale prices.
     * @since 1.16.1 Add filter to return value of $price_base
     * @since 2.1.5  Fix wholesale price suffix always display default wholesale role (wholesale_customer) price
     * @access public
     *
     * @param WC_Product $product                     WC_Product object.
     * @param array      $user_wholesale_role         User wholesale role.
     * @param string     $wholesale_price             Wholesale price.
     * @param boolean    $return_wholesale_price_only Whether to return wholesale price markup only, used on product cpt listing.
     * @return string Wholesale price suffix.
     */
    public static function get_wholesale_price_suffix($product, $user_wholesale_role, $wholesale_price, $return_wholesale_price_only = false, $extra_args = array())
    {

        $wc_price_suffix = apply_filters('wwp_wholesale_price_suffix', get_option('woocommerce_price_display_suffix'));

        if (!empty($user_wholesale_role)) {

            $price_arr  = WWP_Wholesale_Prices::get_product_wholesale_price_on_shop_v3(WWP_Helper_Functions::wwp_get_product_id($product), $user_wholesale_role);
            $base_price = apply_filters('wwp_wholesale_price_suffix_base_price', !empty($price_arr['wholesale_price_raw']) ? $price_arr['wholesale_price_raw'] : $product->get_regular_price(), $product);

            // To be used in function get_wholesale_price_display_suffix_filter of WWPP
            // For wholesale price display suffix
            $extra_args['base_price'] = $base_price;

            if (strpos($wc_price_suffix, "{price_including_tax}") !== false) {

                $wholesale_price_incl_tax = WWP_Helper_Functions::wwp_formatted_price(WWP_Helper_Functions::wwp_get_price_including_tax($product, array('qty' => 1, 'price' => $base_price)));
                $wc_price_suffix          = str_replace("{price_including_tax}", $wholesale_price_incl_tax, $wc_price_suffix);

            }

            if (strpos($wc_price_suffix, "{price_excluding_tax}") !== false) {

                $wholesale_price_excl_tax = WWP_Helper_Functions::wwp_formatted_price(WWP_Helper_Functions::wwp_get_price_excluding_tax($product, array('qty' => 1, 'price' => $base_price)));
                $wc_price_suffix          = str_replace("{price_excluding_tax}", $wholesale_price_excl_tax, $wc_price_suffix);

            }

            $wc_price_suffix = ' <small class="woocommerce-price-suffix wholesale-price-suffix">' . $wc_price_suffix . '</small>';

        } else {
            $wc_price_suffix = $product->get_price_suffix();
        }

        return apply_filters('wwp_filter_wholesale_price_display_suffix', $wc_price_suffix, $product, $user_wholesale_role, $wholesale_price, $return_wholesale_price_only, $extra_args);

    }

    /**
     * Filter callback that alters the product price, it embeds the wholesale price of a product for a wholesale user.
     *
     * @since 1.0.0
     * @since 1.2.8 Now if empty $price then don't bother creating wholesale html price.
     * @since 1.5.0 Refactor codebase.
     * @since 1.6.0 Refactor codebase.
     * @access public
     *
     * @param string     $price                       Product price in html.
     * @param WC_Product $product                     WC_Product instance.
     * @param array      $user_wholesale_role         User's wholesale role.
     * @param boolean    $return_wholesale_price_only Whether to only return the wholesale price markup. Used for products cpt listing.
     * @return string Product price with wholesale applied if necessary.
     */
    public function wholesale_price_html_filter($price, $product, $user_wholesale_role = null, $return_wholesale_price_only = false)
    {

        if (is_null($user_wholesale_role)) {
            $user_wholesale_role = $this->_wwp_wholesale_roles->getUserWholesaleRole();
        }

        if (!empty($user_wholesale_role) && !empty($price)) {

            $wholesale_price_title_text = trim(apply_filters('wwp_filter_wholesale_price_title_text', __('Wholesale Price:', 'woocommerce-wholesale-prices')));
            $raw_wholesale_price        = '';
            $wholesale_price            = '';
            $source                     = '';
            $extra_args                 = array();

            if (in_array(WWP_Helper_Functions::wwp_get_product_type($product), array('simple', 'variation'))) {

                $price_arr           = self::get_product_wholesale_price_on_shop_v3(WWP_Helper_Functions::wwp_get_product_id($product), $user_wholesale_role);
                $raw_wholesale_price = $price_arr['wholesale_price'];
                $source              = $price_arr['source'];

                if (strcasecmp($raw_wholesale_price, '') != 0) {

                    $wholesale_price = WWP_Helper_Functions::wwp_formatted_price($raw_wholesale_price);

                    if (!$return_wholesale_price_only) {
                        $wholesale_price .= self::get_wholesale_price_suffix($product, $user_wholesale_role, $price_arr['wholesale_price_with_no_tax'], $return_wholesale_price_only);
                    }

                }

            } elseif (WWP_Helper_Functions::wwp_get_product_type($product) === 'variable') {

                $user_id    = apply_filters('wwp_wholesale_price_current_user_id', get_current_user_id());
                $cache_data = apply_filters('wwp_get_variable_product_price_range_cache', false, $user_id, $product, $user_wholesale_role);

                // Do not use caching if $return_wholesale_price_only is true, coz this is used on cpt listing
                // and cpt listing callback is triggered unpredictably, and multiple times.
                // It is even triggered even before WC have initialized
                if (is_array($cache_data) && $cache_data['min_price'] && $cache_data['max_price'] && !$return_wholesale_price_only) {

                    $min_price                            = $cache_data['min_price'];
                    $min_wholesale_price_without_taxing   = $cache_data['min_wholesale_price_without_taxing'];
                    $max_price                            = $cache_data['max_price'];
                    $max_wholesale_price_without_taxing   = $cache_data['max_wholesale_price_without_taxing'];
                    $some_variations_have_wholesale_price = $cache_data['some_variations_have_wholesale_price'];

                } else {

                    $variations                           = WWP_Helper_Functions::wwp_get_variable_product_variations($product);
                    $min_price                            = '';
                    $min_wholesale_price_without_taxing   = '';
                    $max_price                            = '';
                    $max_wholesale_price_without_taxing   = '';
                    $some_variations_have_wholesale_price = false;

                    foreach ($variations as $variation) {

                        if (!$variation['is_purchasable']) {
                            continue;
                        }

                        $curr_var_price = $variation['display_price'];
                        $price_arr      = self::get_product_wholesale_price_on_shop_v3($variation['variation_id'], $user_wholesale_role);

                        if (strcasecmp($price_arr['wholesale_price'], '') != 0) {

                            $curr_var_price = $price_arr['wholesale_price'];

                            if (!$some_variations_have_wholesale_price) {
                                $some_variations_have_wholesale_price = true;
                            }

                        }

                        if (strcasecmp($min_price, '') == 0 || $curr_var_price < $min_price) {

                            $min_price                          = $curr_var_price;
                            $min_wholesale_price_without_taxing = strcasecmp($price_arr['wholesale_price_with_no_tax'], '') != 0 ? $price_arr['wholesale_price_with_no_tax'] : '';

                        }

                        if (strcasecmp($max_price, '') == 0 || $curr_var_price > $max_price) {

                            $max_price                          = $curr_var_price;
                            $max_wholesale_price_without_taxing = strcasecmp($price_arr['wholesale_price_with_no_tax'], '') != 0 ? $price_arr['wholesale_price_with_no_tax'] : '';

                        }

                    }

                    if (!$return_wholesale_price_only) {

                        do_action('wwp_after_variable_product_compute_price_range', $user_id, $product, $user_wholesale_role, array(
                            'min_price'                            => $min_price,
                            'min_wholesale_price_without_taxing'   => $min_wholesale_price_without_taxing,
                            'max_price'                            => $max_price,
                            'max_wholesale_price_without_taxing'   => $max_wholesale_price_without_taxing,
                            'some_variations_have_wholesale_price' => $some_variations_have_wholesale_price,
                        ));

                    }

                }

                // To be used in function get_wholesale_price_display_suffix_filter of WWPP
                // For wholesale price display suffix
                $extra_args = array(
                    'min_price' => $min_price,
                    'max_price' => $max_price,
                );

                // Only alter price html if, some/all variations of this variable product have sale price and
                // min and max price have valid values
                if ($some_variations_have_wholesale_price && strcasecmp($min_price, '') != 0 && strcasecmp($max_price, '') != 0) {

                    if ($min_price != $max_price && $min_price < $max_price) {

                        $wholesale_price = WWP_Helper_Functions::wwp_formatted_price($min_price) . ' - ' . WWP_Helper_Functions::wwp_formatted_price($max_price);
                        $wc_price_suffix = get_option('woocommerce_price_display_suffix');

                        if (strpos($wc_price_suffix, '{price_including_tax}') === false && strpos($wc_price_suffix, '{price_excluding_tax}') === false && !$return_wholesale_price_only) {

                            $wsprice = !empty($max_wholesale_price_without_taxing) ? $max_wholesale_price_without_taxing : null;
                            $wholesale_price .= self::get_wholesale_price_suffix($product, $user_wholesale_role, $wsprice, $return_wholesale_price_only, $extra_args);

                        }

                    } else {

                        $wholesale_price = WWP_Helper_Functions::wwp_formatted_price($max_price);

                        if (!$return_wholesale_price_only) {

                            $wsprice = !empty($max_wholesale_price_without_taxing) ? $max_wholesale_price_without_taxing : null;
                            $wholesale_price .= self::get_wholesale_price_suffix($product, $user_wholesale_role, $wsprice, $return_wholesale_price_only, $extra_args);

                        }

                    }

                }

                $return_value = apply_filters('wwp_filter_variable_product_wholesale_price_range', array(
                    'wholesale_price'                    => $wholesale_price,
                    'price'                              => $price,
                    'product'                            => $product,
                    'user_wholesale_role'                => $user_wholesale_role,
                    'min_price'                          => $min_price,
                    'min_wholesale_price_without_taxing' => $min_wholesale_price_without_taxing,
                    'max_price'                          => $max_price,
                    'max_wholesale_price_without_taxing' => $max_wholesale_price_without_taxing,
                    'wholesale_price_title_text'         => $wholesale_price_title_text,
                    'return_wholesale_price_only'        => $return_wholesale_price_only,
                ));

                $wholesale_price = $return_value['wholesale_price'];

                if (isset($return_value['wholesale_price_title_text'])) {
                    $wholesale_price_title_text = $return_value['wholesale_price_title_text'];
                }

            }

            if (strcasecmp($wholesale_price, '') != 0) {

                $wholesale_price_html = '<span style="display: block;" class="wholesale_price_container">
                                            <span class="wholesale_price_title">' . $wholesale_price_title_text . '</span>
                                            <ins>' . $wholesale_price . '</ins>
                                        </span>';

                if ($return_wholesale_price_only) {
                    return $wholesale_price_html;
                }

                $wholesale_price_html = apply_filters('wwp_product_original_price', '<del class="original-computed-price">' . $price . '</del>', $wholesale_price, $price, $product, $user_wholesale_role) . $wholesale_price_html;

                return apply_filters('wwp_filter_wholesale_price_html', $wholesale_price_html, $price, $product, $user_wholesale_role, $wholesale_price_title_text, $raw_wholesale_price, $source);

            }

        }

        return apply_filters('wwp_filter_variable_product_price_range_for_none_wholesale_users', $price, $product);

    }

    /**
     * Apply product wholesale price upon adding to cart.
     *
     * @since 1.0.0
     * @since 1.2.3 Add filter hook 'wwp_filter_get_custom_product_type_wholesale_price' for which extensions can attach and add support for custom product types.
     * @since 1.4.0 Add filter hook 'wwp_wholesale_requirements_not_passed' for which extensions can attach and do something whenever wholesale requirement is not meet.
     * @since 1.5.0 Rewrote the code for speed and efficiency.
     * @access public
     *
     * @param $cart_object
     * @param $user_wholesale_role
     */
    public function apply_product_wholesale_price_to_cart($cart_object)
    {

        $user_wholesale_role = $this->_wwp_wholesale_roles->getUserWholesaleRole();

        if (empty($user_wholesale_role)) {
            return false;
        }

        $per_product_requirement_notices = array();
        $has_cart_items                  = false;
        $cart_total                      = 0;
        $cart_items                      = 0;
        $cart_items_price_cache          = array(); // Holds the original prices of products in cart

        do_action('wwp_before_apply_product_wholesale_price_cart_loop', $cart_object, $user_wholesale_role);

        foreach ($cart_object->cart_contents as $cart_item_key => $cart_item) {

            if (!$has_cart_items) {
                $has_cart_items = true;
            }

            $wwp_data        = null;
            $wholesale_price = '';

            if (in_array(WWP_Helper_Functions::wwp_get_product_type($cart_item['data']), array('simple', 'variation'))) {
                $wholesale_price = self::get_product_wholesale_price_on_cart(WWP_Helper_Functions::wwp_get_product_id($cart_item['data']), $user_wholesale_role, $cart_item, $cart_object);
            } else {
                $wholesale_price = apply_filters('wwp_filter_get_custom_product_type_wholesale_price', $wholesale_price, $cart_item, $user_wholesale_role, $cart_object);
            }

            if ($wholesale_price !== '') {

                if (get_option('woocommerce_prices_include_tax') === 'yes') {
                    $wp = wc_get_price_excluding_tax($cart_item['data'], array('qty' => 1, 'price' => $wholesale_price));
                } else {
                    $wp = $wholesale_price;
                }

                $apply_product_level_wholesale_price = apply_filters('wwp_apply_wholesale_price_per_product_level', true, $cart_item, $cart_object, $user_wholesale_role, $wp);

                if ($apply_product_level_wholesale_price === true) {

                    $cart_items_price_cache[$cart_item_key] = $cart_item['data']->get_price();
                    $cart_item['data']->set_price(WWP_Helper_Functions::wwp_wpml_price($wholesale_price));
                    $wwp_data = array('wholesale_priced' => 'yes', 'wholesale_role' => $user_wholesale_role[0]);

                } else {

                    if (is_array($apply_product_level_wholesale_price)) {
                        $per_product_requirement_notices[] = $apply_product_level_wholesale_price;
                    }

                    $wwp_data = array('wholesale_priced' => 'no', 'wholesale_role' => $user_wholesale_role[0]);

                }

            } else {
                $wwp_data = array('wholesale_priced' => 'no', 'wholesale_role' => $user_wholesale_role[0]);
            }

            // Add additional wwp data to cart item. This is used for WWS Reporting
            $cart_item['data']->wwp_data = apply_filters('wwp_add_cart_item_meta', $wwp_data, $cart_item, $cart_object, $user_wholesale_role);

            if (apply_filters('wwp_include_cart_item_on_cart_totals_computation', true, $cart_item, $user_wholesale_role)) {

                if ($wholesale_price) {

                    if (get_option('woocommerce_prices_include_tax') === 'yes') {
                        $wp = wc_get_price_excluding_tax($cart_item['data'], array('qty' => 1, 'price' => $wholesale_price));
                    } else {
                        $wp = $wholesale_price;
                    }

                } else {
                    $wp = $cart_item['data']->get_price();
                }

                $cart_total += $wp * $cart_item['quantity'];
                $cart_items += $cart_item['quantity'];

            }

        } // Cart loop

        do_action('wwp_after_apply_product_wholesale_price_cart_loop', $cart_object, $user_wholesale_role);

        $apply_wholesale_price_cart_level = apply_filters('wwp_apply_wholesale_price_cart_level', true, $cart_total, $cart_items, $cart_object, $user_wholesale_role);

        if (($has_cart_items && $apply_wholesale_price_cart_level !== true) || !empty($per_product_requirement_notices)) {
            do_action('wwp_wholesale_requirements_not_passed', $cart_object, $user_wholesale_role);
        }

        if ($has_cart_items && $apply_wholesale_price_cart_level !== true) {

            // Revert back to original pricing
            foreach ($cart_object->cart_contents as $cart_item_key => $cart_item) {

                if (array_key_exists($cart_item_key, $cart_items_price_cache)) {

                    $cart_item['data']->set_price($cart_items_price_cache[$cart_item_key]);
                    $cart_item['data']->wwp_data = array('wholesale_priced' => 'no', 'wholesale_role' => $user_wholesale_role[0]);

                }

            }

            if ((is_cart() || is_checkout()) && (!defined('DOING_AJAX') || !DOING_AJAX)) {
                $this->printWCNotice($apply_wholesale_price_cart_level);
            }

        }

        if (!empty($per_product_requirement_notices)) {
            foreach ($per_product_requirement_notices as $notice) {
                if ((is_cart() || is_checkout()) && (!defined('DOING_AJAX') || !DOING_AJAX)) {
                    $this->printWCNotice($per_product_requirement_notices);
                }
            }
        }

    }

    /**
     * Recalculate cart totals.
     * We need to do this on loading widget cart to properly sync the cart item prices.
     * If we don't do this, the cart item line price will not be sync with what's on the cart.
     *
     * @since 1.5.0
     * @access public
     */
    public function recalculate_cart_totals()
    {

        WC()->cart->calculate_totals();

    }

    /**
     * Apply taxing accordingly to wholesale prices on shop page.
     * We will handle tax application to wholesale prices only on WWP if WWPP is not present.
     * If WWPP is present lets allow WWPP to handle this instead.
     * This is only applied on shop page, we dont need to do this on cart/checkout prices.
     * WC will take care of that coz we are hooking to 'before_calculate_totals' so after we apply wholesale pricing on cart/checkout page,
     * WC will then apply taxing above it.
     *
     * @since 1.5.0
     * @access public
     *
     * @param float $wholesale_price     Wholesale price.
     * @param int   $product_id          Product Id.
     * @param array $user_wholesale_role User wholesale roles.
     * @return float Modified wholesale price.
     */
    public function apply_taxing_to_wholesale_prices_on_shop_page($wholesale_price, $product_id, $user_wholesale_role)
    {

        if (!WWP_Helper_Functions::is_plugin_active('woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php') && !empty($wholesale_price) && !empty($user_wholesale_role) && get_option('woocommerce_calc_taxes', false) === 'yes') {

            $product                      = wc_get_product($product_id);
            $woocommerce_tax_display_shop = get_option('woocommerce_tax_display_shop', false);

            if ($woocommerce_tax_display_shop === 'incl') {
                $wholesale_price = WWP_Helper_Functions::wwp_get_price_including_tax($product, array('qty' => 1, 'price' => $wholesale_price));
            } else {
                $wholesale_price = WWP_Helper_Functions::wwp_get_price_excluding_tax($product, array('qty' => 1, 'price' => $wholesale_price));
            }

        }

        return $wholesale_price;

    }

    /**
     * Print WP Notices.
     *
     * @since 1.0.7
     * @access public
     *
     * @param string|array $notices WWP/P related notices.
     */
    public function printWCNotice($notices)
    {

        if (is_array($notices) && array_key_exists('message', $notices) && array_key_exists('type', $notices)) {
            // Pre Version 1.2.0 of wwpp where it sends back single dimension array of notice

            wc_print_notice($notices['message'], $notices['type']);

        } elseif (is_array($notices)) {
            // Version 1.2.0 of wwpp where it sends back multiple notice via multi dimensional arrays

            foreach ($notices as $notice) {

                if (array_key_exists('message', $notice) && array_key_exists('type', $notice)) {
                    wc_print_notice($notice['message'], $notice['type']);
                }

            }

        }

    }

    /**
     * Fix issue regarding meta role key being lowercased after product import.
     * Issue 1: Addressed the issue with aelia currency wholesale price not detecting after import. WWP-160
     * Issue 2: Addressed the issue with uppercase wholesale role key not detected the wholesale price after import. WWPP-657
     * Reason for that is WC tends to lowercase the meta keys while the currency is in uppercase or role has uppercase letter so wp won't detect the meta properly.
     * ex 1: instead of 'wholesale_customer_USD_wholesale_price' wc imports the key as wholesale_customer_usd_wholesale_price.
     *
     * @since 1.8
     * @access public
     *
     * @param array     $data       WC Product Data
     * @param object    $importer   WC_Product_CSV_Importer Object
     * @return array
     */
    public function update_meta_data_with_proper_meta_keys($data, $importer)
    {

        $aelia_currency_switcher_active = WWP_ACS_Integration_Helper::aelia_currency_switcher_active();

        if (isset($data['meta_data'])) {

            $wholesale_roles         = $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles();
            $wacs_enabled_currencies = WWP_ACS_Integration_Helper::enabled_currencies();

            if (!empty($wholesale_roles)) {

                foreach ($wholesale_roles as $role_key => $role_data) {

                    $pattern = '/' . strtolower($role_key) . '_([a-z]+)_wholesale_price/';

                    foreach ($data['meta_data'] as $key => $meta) {

                        // Aelia Currency Fix
                        if ($aelia_currency_switcher_active) {

                            preg_match($pattern, $meta['key'], $matches);

                            if (isset($matches[1]) && in_array(strtoupper($matches[1]), $wacs_enabled_currencies)) {

                                $updated_key                    = $role_key . '_' . strtoupper($matches[1]) . '_wholesale_price';
                                $data['meta_data'][$key]['key'] = $updated_key;
                                $meta['key']                    = $updated_key;

                            }

                        }

                        // Wholesale role key with uppercase letter fix
                        $pos = strpos($meta['key'], strtolower($role_key));

                        if ($pos !== false) {
                            $data['meta_data'][$key]['key'] = str_replace(strtolower($role_key), $role_key, $meta['key']);
                        }

                    }

                }

            }

        }

        return $data;

    }

    /**
     * Add the wholesale price to the variation data on the single product page form.
     *
     * @since 1.9
     * @access public
     *
     * @param array                $data      Variation data.
     * @param WC_Product_Variable  $parent    Parent variable product object.
     * @param WC_Product_Variation $variation Variation product object.
     */
    public function add_wholesale_price_to_variation_data($data, $parent, $variation)
    {

        $user_wholesale_role = $this->_wwp_wholesale_roles->getUserWholesaleRole();

        if (!empty($user_wholesale_role)) {

            $price_arr = self::get_product_wholesale_price_on_shop_v3($variation->get_id(), $user_wholesale_role);

            if (isset($price_arr['wholesale_price']) && $price_arr['wholesale_price']) {
                $data['wholesale_price'] = (float) $price_arr['wholesale_price'];
            }

            if (isset($price_arr['wholesale_price_raw']) && $price_arr['wholesale_price_raw']) {
                $data['wholesale_price_raw'] = (float) $price_arr['wholesale_price_raw'];
            }

            if (isset($price_arr['wholesale_price_with_no_tax']) && $price_arr['wholesale_price_with_no_tax']) {
                $data['wholesale_price_with_no_tax'] = (float) $price_arr['wholesale_price_with_no_tax'];
            }

        }

        return $data;
    }

    /**
     * Set coupons availability to wholesale users.
     * Used to show/hide original product price.
     *
     * @since 1.11
     * @access public
     *
     * @param boolean $enabled Coupons available flag.
     * @return bool Filtered coupons available flag.
     */
    public function toggle_availability_of_coupons_to_wholesale_users($enabled)
    {

        $user_wholesale_role = $this->_wwp_wholesale_roles->getUserWholesaleRole();
        $user_wholesale_role = (is_array($user_wholesale_role) && !empty($user_wholesale_role)) ? $user_wholesale_role[0] : '';

        if (get_option('wwpp_settings_disable_coupons_for_wholesale_users') == 'yes' && !empty($user_wholesale_role)) {
            $enabled = false;
        }

        return $enabled;
    }

    /**
     * There's a bug on wwpp where wholesale users can still avail coupons even if 'Disable Coupons For Wholesale Users' option is enabled.
     * They can do this by applying coupon to cart first before logging in as wholesale user.
     * Therefore when wholesale user visits cart/checkout pages, we check if 'Disable Coupons For Wholesale Users' is enabled.
     * If so then we remove coupons to the cart.
     *
     * @since 1.11
     * @access public
     */
    public function remove_coupons_for_wholesale_users_when_necessary()
    {

        $user_wholesale_role = $this->_wwp_wholesale_roles->getUserWholesaleRole();
        $user_wholesale_role = (is_array($user_wholesale_role) && !empty($user_wholesale_role)) ? $user_wholesale_role[0] : '';

        if (get_option('wwpp_settings_disable_coupons_for_wholesale_users') == 'yes' && !empty($user_wholesale_role)) {
            WC()->cart->remove_coupons();
        }

    }

    /*
     * @param string     $original_price      Crossed out original price html.
     * @param float      $wholesale_price     wholesale price.
     * @param float      $price               Original price.
     * @param WC_Product $product             Product object.
     * @param array      $user_wholesale_role User wholesale role.
     * @return string Filtered crossed out original price html.
     */
    public function filter_product_original_price_visibility($original_price, $wholesale_price, $price, $product, $user_wholesale_role)
    {

        if (get_option('wwpp_settings_hide_original_price') === "yes") {
            $original_price = '';
        }

        return $original_price;

    }

    /*
     * Filter the text for the wholesale price title.
     *
     * @since 1.11
     * @return mixed
     *
     * @param $title_text
     */
    public function filter_wholesale_price_title_text($title_text)
    {

        $setting_title_text = esc_attr(trim(get_option('wwpp_settings_wholesale_price_title_text')));
        return $setting_title_text;

    }

    /**
     * Handles hiding Price and Add to Cart button when "Hide Price and Add to Cart button" option is enabled.
     *
     * @since 1.13
     * @access public
     */
    public function hide_price_and_add_to_cart_button()
    {

        $hide_price_and_add_to_cart_button = apply_filters('wwp_hide_price_and_add_to_cart_button', !is_user_logged_in() && get_option('wwp_hide_price_add_to_cart') === 'yes' ? true : false);

        if ($hide_price_and_add_to_cart_button) {
            remove_action('woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30);
            remove_action('woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30);
            remove_action('woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30);
            remove_action('woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30);
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
            remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);

            /**
             * This small line of code will render the product unpurchasable and do it in a pretty simple way,
             * and no hooks or templates will be removed, thus no incompatibility issues would creep up.
             *
             * This will remove the add to cart button
             */
            add_filter('woocommerce_is_purchasable', '__return_false', 999);

            /**
             * Hide also Click to See Wholesale Prices for non wholesale customers
             */
            add_filter('wwp_show_wholesale_prices_to_non_wholesale_customers', function ($show_wholesale_prices) {return 'no';}, 11, 1);

            /**
             * Empty prices for other theme compatibility, some themes have custom hooks
             */
            add_filter('woocommerce_get_price_html', array($this, 'remove_product_prices'), 10, 2);
        }

    }

    /**
     * Remove Prices if Hide price and add to cart button is enabled
     *
     * @since 1.16.1
     * @access public
     *
     * @param string $prices
     * @param object $product
     * @return boolean
     */
    public function remove_product_prices($prices, $product)
    {
        return $prices = '';
    }

    /**
     * Handles displaying replacement message for Price and Add to Cart button when "Hide Price and Add to Cart button" option is enabled.
     *
     * @since 1.13
     * @since 2.1.5 Separate logic on how to get the price and add to cart replacement message so the function is reusable
     * @access public
     */
    public function display_replacement_message()
    {
        
        $hide_price_and_add_to_cart_button = apply_filters('wwp_hide_price_and_add_to_cart_button', !is_user_logged_in() && get_option('wwp_hide_price_add_to_cart') === 'yes' ? true : false);

        if ($hide_price_and_add_to_cart_button) {
            echo $this->get_price_and_add_to_cart_replacement_message();
        }
        
    }

    /**
     * Get the replacement message for price and add to cart when "Hide Price and Add to Cart button" option is enabled.
     * 
     * @since 2.1.5
     * @access public
     */
    public function get_price_and_add_to_cart_replacement_message()
    {
        
        $message = get_option('wwp_price_and_add_to_cart_replacement_message');

        if (empty($message)) {
            $message = '<a class="wwp-login-to-see-wholesale-prices" href="' . get_permalink(wc_get_page_id('myaccount')) . '">' . __('Login to see prices', 'woocommerce-wholesale-prices') . '</a>';
        } else {
            $message = html_entity_decode($message);
        }

        return apply_filters('wwp_display_replacement_message', $message);
        
    }

    /**
     * Clear Product Transient on Tax Settings Save
     * 
     * This will clear product transient when there is a change in WC Tax settings to properly apply the price tax, and will only run if wwpp is not activated.
     * 
     * - The problem is that when the Tax Settings in WC > Tax options, specially on "Prices entered with tax" options has been change. The products price tax are not properly applied.
     * 
     * @since 2.1.2
     */
    public function clear_product_transient_on_tax_settings_save(){

        // We will only execute "wc_delete_product_transients" if WWPP is not activated
        if( ! WWP_Helper_Functions::is_wwpp_active() && function_exists('wc_delete_product_transients') ){

            wc_delete_product_transients();

        }

    }

    /**
     * Handles hide Add to Cart button in WooCommerce Product Blocks when "Hide Price and Add to Cart button" option is enabled.
     *
     * @param string     $html    Product grid item HTML.
     * @param array      $data    Product data passed to the template.
     * @param WC_Product $product Product object. 
     * 
     * @since 2.1.5
     * @access public
     */
    public function hide_add_to_cart_button_wc_blocks($html, $data, $product){
        
        $hide_price_and_add_to_cart_button = apply_filters('wwp_hide_price_and_add_to_cart_button', !is_user_logged_in() && get_option('wwp_hide_price_add_to_cart') === 'yes' ? true : false);

        if($hide_price_and_add_to_cart_button) {

            $replacement_message = $this->get_price_and_add_to_cart_replacement_message();

            return "<li class=\"wc-block-grid__product\">
                <a href=\"{$data->permalink}\" class=\"wc-block-grid__product-link\">
                    {$data->image}
                    {$data->title}
                </a>
                {$data->badge}
                {$data->price}
                {$data->rating}
                $replacement_message
            </li>";
        }

        return $html;
    }

    /*
    |--------------------------------------------------------------------------
    | Execute Model
    |--------------------------------------------------------------------------
     */

    /**
     * Execute model.
     *
     * @since 1.5.0
     * @access public
     */
    public function run()
    {

        // Apply wholesale price to archive and single product pages
        // On WC 3.x series, includes variation products
        add_filter('woocommerce_get_price_html', array($this, 'wholesale_price_html_filter'), 10, 2);

        // Apply wholesale price upon adding product to cart
        add_action('woocommerce_before_calculate_totals', array($this, 'apply_product_wholesale_price_to_cart'), 10, 1);

        // We need to recalculate cart on loading widget cart to properly sync the cart item prices
        add_action('woocommerce_before_mini_cart', array($this, 'recalculate_cart_totals'));

        // Apply taxing to wholesale price on shop pages
        add_filter('wwp_pass_wholesale_price_through_taxing', array($this, 'apply_taxing_to_wholesale_prices_on_shop_page'), 10, 3);

        // Product Import. Wholesale Prices + Aelia Currency plugin compatibility. Also fix issue with wholesale role with uppercase letter.
        add_filter('woocommerce_product_importer_parsed_data', array($this, 'update_meta_data_with_proper_meta_keys'), 10, 2);

        // Add the wholesale price to the variation data on the single product page form.
        add_filter('woocommerce_available_variation', array($this, 'add_wholesale_price_to_variation_data'), 10, 3);

        // Disable Coupons For Wholesale Users Option
        add_filter('woocommerce_coupons_enabled', array($this, 'toggle_availability_of_coupons_to_wholesale_users'), 10, 1);
        add_action('woocommerce_before_cart', array($this, 'remove_coupons_for_wholesale_users_when_necessary'));
        add_action('woocommerce_before_checkout_form', array($this, 'remove_coupons_for_wholesale_users_when_necessary'));

        // Filter the product price to hide the original price for wholesale users
        add_filter('wwp_product_original_price', array($this, 'filter_product_original_price_visibility'), 10, 5);

        // Custom Wholesale Price Text
        add_filter('wwp_filter_wholesale_price_title_text', array($this, 'filter_wholesale_price_title_text'), 10, 1);

        // Hide Price and Add to Cart button feature
        add_filter('init', array($this, 'hide_price_and_add_to_cart_button'));
        add_action('woocommerce_single_product_summary', array($this, 'display_replacement_message'), 10);
        add_action('woocommerce_after_shop_loop_item', array($this, 'display_replacement_message'), 10);
        add_filter('woocommerce_blocks_product_grid_item_html', array($this, 'hide_add_to_cart_button_wc_blocks'), 10, 3);

        // Clear Product Transients on tax settings save.
        add_action( 'woocommerce_settings_save_tax', array($this, 'clear_product_transient_on_tax_settings_save'), 10 );

    }

}
