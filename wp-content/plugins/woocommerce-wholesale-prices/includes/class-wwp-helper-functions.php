<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

if (!class_exists('WWP_Helper_Functions')) {

    /**
     * Model that houses plugin helper functions.
     *
     * @since 1.3.0
     */
    final class WWP_Helper_Functions
    {

        /**
         * Utility function that determines if a plugin is active or not.
         *
         * @since 1.3.0
         * @access public
         *
         * @param string $plugin_basename Plugin base name. Ex. woocommerce/woocommerce.php
         * @return boolean True if active, false otherwise.
         */
        public static function is_plugin_active($plugin_basename)
        {

            // Makes sure the plugin is defined before trying to use it
            if (!function_exists('is_plugin_active')) {
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            return is_plugin_active($plugin_basename);
        }

        /**
         * Get Admin Note instance. WC Admin v1.7 they changed the class from WC_Admin_Note to Note.
         *
         * @since 1.13
         * @param string $note_id Note ID
         * @access public
         * @return string
         */
        public static function wc_admin_note_instance($note_id = null)
        {

            if (class_exists('\Automattic\WooCommerce\Admin\Notes\Note')) {
                return new \Automattic\WooCommerce\Admin\Notes\Note($note_id);
            } else {
                return new \Automattic\WooCommerce\Admin\Notes\WC_Admin_Note($note_id);
            }
        }

        /**
         * WWPP is active.
         *
         * @since 1.12
         * @access public
         * @return boolean True if active, false otherwise.
         */
        public static function is_wwpp_active()
        {

            global $wc_wholesale_prices_premium;

            return self::is_plugin_active('woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php') && $wc_wholesale_prices_premium;

        }

        /**
         * WWOF is active.
         *
         * @since 1.14
         * @access public
         * @return boolean True if active, false otherwise.
         */
        public static function is_wwof_active()
        {

            global $wc_wholesale_order_form;

            return self::is_plugin_active('woocommerce-wholesale-order-form/woocommerce-wholesale-order-form.bootstrap.php') && $wc_wholesale_order_form;

        }

        /**
         * WWLC is active.
         *
         * @since 1.14
         * @access public
         * @return boolean True if active, false otherwise.
         */
        public static function is_wwlc_active()
        {

            global $wc_wholesale_lead_capture;

            return self::is_plugin_active('woocommerce-wholesale-lead-capture/woocommerce-wholesale-lead-capture.bootstrap.php') && $wc_wholesale_lead_capture;

        }

        /**
         * ACFWF is active.
         *
         * @since 2.1.1
         * @access public
         * @return boolean True if active, false otherwise.
         */
        public static function is_acfwf_active()
        {

            global $ACFWF;

            return self::is_plugin_active('advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php') && $ACFWF;

        }

        /**
         * Check if WC version is 4.3 and up or rc versions.
         *
         * @since 1.11.10
         * @access public
         * @return boolean True if active, false otherwise.
         */
        public static function is_wc_four_point_three_and_up()
        {

            $wc_data = self::get_woocommerce_data();

            if (
                version_compare($wc_data['Version'], '4.3.0', '>=') ||
                $wc_data['Version'] === '4.3.0-rc.1' ||
                $wc_data['Version'] === '4.3.0-rc.2'
            ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Check if WC Admin is active
         *
         * @since 1.11.7
         * @access public
         * @return boolean True if active, false otherwise.
         */
        public static function is_wc_admin_active()
        {

            if (class_exists('\Automattic\WooCommerce\Admin\Composer\Package') && defined('WC_ADMIN_APP') && WC_ADMIN_APP) {
                return \Automattic\WooCommerce\Admin\Composer\Package::is_package_active();
            } else if (self::is_plugin_active('woocommerce-admin/woocommerce-admin.php')) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Check if ACFWF is installed
         *
         * @since 1.11.5
         *
         * @return bool
         */
        public static function is_acfwf_installed()
        {

            $plugin_file = 'advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php';
            $acfwf_file  = trailingslashit(WP_PLUGIN_DIR) . plugin_basename($plugin_file);

            return file_exists($acfwf_file);
        }

        /**
         * Check if WWP is installed
         *
         * @since 1.11.5
         * @access public
         *
         * @return bool
         */
        public static function is_wwp_installed()
        {

            $plugin_file = 'woocommerce-wholesale-prices/woocommerce-wholesale-prices.bootstrap.php';
            $wwp_file    = trailingslashit(WP_PLUGIN_DIR) . plugin_basename($plugin_file);

            return file_exists($wwp_file);
        }

        /**
         * Check if WWPP is installed
         *
         * @since 1.11.5
         * @access public
         *
         * @return bool
         */
        public static function is_wwpp_installed()
        {

            $plugin_file = 'woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php';
            $wwpp_file   = trailingslashit(WP_PLUGIN_DIR) . plugin_basename($plugin_file);

            return file_exists($wwpp_file);
        }

        /**
         * Check if WWOF is installed
         *
         * @since 1.11.5
         * @access public
         *
         * @return bool
         */
        public static function is_wwof_installed()
        {

            $plugin_file = 'woocommerce-wholesale-order-form/woocommerce-wholesale-order-form.bootstrap.php';
            $wwof_file   = trailingslashit(WP_PLUGIN_DIR) . plugin_basename($plugin_file);

            return file_exists($wwof_file);
        }

        /**
         * Check if WWLC is installed
         *
         * @since 1.11.5
         * @access public
         *
         * @return bool
         */
        public static function is_wwlc_installed()
        {

            $plugin_file = 'woocommerce-wholesale-lead-capture/woocommerce-wholesale-lead-capture.bootstrap.php';
            $wwlc_file   = trailingslashit(WP_PLUGIN_DIR) . plugin_basename($plugin_file);

            return file_exists($wwlc_file);
        }

        /**
         * Get data about the current woocommerce installation.
         *
         * @since 1.3.1
         * @access public
         *
         * @return array Array of data about the current woocommerce installation.
         */
        public static function get_woocommerce_data()
        {

            return self::get_plugin_data('woocommerce/woocommerce.php');
        }

        /**
         * Get plugin data.
         *
         * @since 1.4.0
         * @access public
         *
         * @oaran string $plugin_basename Plugin basename.
         * @return array Array of data about the current woocommerce installation.
         */
        public static function get_plugin_data($plugin_basename)
        {

            if (!function_exists('get_plugin_data')) {
                require_once ABSPATH . '/wp-admin/includes/plugin.php';
            }

            if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_basename)) {
                return get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_basename);
            } else {
                return false;
            }
        }

        /**
         * Output a select input box.
         *
         * This is similar to woocommerce_wp_select function of WooCommerce.
         * Modified the position of tooltip, this is essential for the variation products where the tooltip should be next to the label, not the text field.
         *
         * @since 2.1.0
         * @access public
         *
         * @param array $field Data about the field to render.
         */
        public static function woocommerce_wp_select($field)
        {
            global $thepostid, $post;

            $thepostid = empty($thepostid) ? $post->ID : $thepostid;
            $field     = wp_parse_args(
                $field, array(
                    'class'             => 'select short',
                    'style'             => '',
                    'wrapper_class'     => '',
                    'value'             => get_post_meta($thepostid, $field['id'], true),
                    'name'              => $field['id'],
                    'desc_tip'          => false,
                    'custom_attributes' => array(),
                )
            );

            $wrapper_attributes = array(
                'class' => $field['wrapper_class'] . " form-field {$field['id']}_field",
            );

            $label_attributes = array(
                'for' => $field['id'],
            );

            $field_attributes          = (array) $field['custom_attributes'];
            $field_attributes['style'] = $field['style'];
            $field_attributes['id']    = $field['id'];
            $field_attributes['name']  = $field['name'];
            $field_attributes['class'] = $field['class'];

            // Custom part
            $tooltip = $description = '';
            if (!empty($field['description'])) {

                if (isset($field['desc_tip']) && false !== $field['desc_tip']) {
                    $tooltip = '<span class="wwp_wc_help_tip_container" style="top: -3px; position: relative; display: inline-block;">' . wc_help_tip($field['description']) . '</span>';
                } else {
                    $description = '<br><span class="description">' . wp_kses_post($field['description']) . '</span>';
                }
            }

            ?>
            <p <?php echo wc_implode_html_attributes($wrapper_attributes); // WPCS: XSS ok.       ?>>
                <label <?php echo wc_implode_html_attributes($label_attributes); // WPCS: XSS ok.       ?>><?php echo wp_kses_post($field['label']) . $tooltip; ?></label>
                <select <?php echo wc_implode_html_attributes($field_attributes); // WPCS: XSS ok.       ?>>
                    <?php
foreach ($field['options'] as $key => $value) {
                echo '<option value="' . esc_attr($key) . '"' . wc_selected($key, $field['value']) . '>' . esc_html($value) . '</option>';
            }
            ?>
                </select>
                <?php echo $description; ?>
            </p>
            <?php
}

        /**
         * Output a text input box.
         * This gonna be 99.99% similar to woocommerce_wp_text_input function of WooCommerce.
         * Only difference is the position of the tooltip.
         * This is essential for wholesale prices field on variation products where the tooltip should be next to the label, not the text field.
         *
         * @since 1.3.0
         * @access public
         *
         * @param array $field Field data.
         */
        public static function wwp_woocommerce_wp_text_input($field)
        {
            global $thepostid, $post;

            $thepostid              = empty($thepostid) ? $post->ID : $thepostid;
            $field['placeholder']   = isset($field['placeholder']) ? $field['placeholder'] : '';
            $field['class']         = isset($field['class']) ? $field['class'] : 'short';
            $field['style']         = isset($field['style']) ? $field['style'] : '';
            $field['wrapper_class'] = isset($field['wrapper_class']) ? $field['wrapper_class'] : '';
            $field['value']         = isset($field['value']) ? $field['value'] : get_post_meta($thepostid, $field['id'], true);
            $field['name']          = isset($field['name']) ? $field['name'] : $field['id'];
            $field['type']          = isset($field['type']) ? $field['type'] : 'text';
            $data_type              = empty($field['data_type']) ? '' : $field['data_type'];

            switch ($data_type) {
                case 'price':
                    $field['class'] .= ' wc_input_price';
                    $field['value'] = wc_format_localized_price($field['value']);
                    break;
                case 'decimal':
                    $field['class'] .= ' wc_input_decimal';
                    $field['value'] = wc_format_localized_decimal($field['value']);
                    break;
                case 'stock':
                    $field['class'] .= ' wc_input_stock';
                    $field['value'] = wc_stock_amount($field['value']);
                    break;
                case 'url':
                    $field['class'] .= ' wc_input_url';
                    $field['value'] = esc_url($field['value']);
                    break;

                default:
                    break;
            }

            // Custom attribute handling
            $custom_attributes = array();

            if (!empty($field['custom_attributes']) && is_array($field['custom_attributes'])) {

                foreach ($field['custom_attributes'] as $attribute => $value) {
                    $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($value) . '"';
                }
            }

            // Custom part
            $desc_tootip = '';
            if (!empty($field['description'])) {

                if (isset($field['desc_tip']) && false !== $field['desc_tip']) {
                    $desc_tootip = '<span class="wwp_wc_help_tip_container" style="top: -3px; position: relative; display: inline-block;">' . wc_help_tip($field['description']) . '</span>';
                } else {
                    $desc_tootip = '<br><span class="description">' . wp_kses_post($field['description']) . '</span>';
                }
            }

            echo '<p class="form-field ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '">
                    <label for="' . esc_attr($field['id']) . '">' . wp_kses_post($field['label']) . $desc_tootip . '</label>
                    <input type="' . esc_attr($field['type']) . '" class="' . esc_attr($field['class']) . '" style="' . esc_attr($field['style']) . '" name="' . esc_attr($field['name']) . '" id="' . esc_attr($field['id']) . '" value="' . esc_attr($field['value']) . '" placeholder="' . esc_attr($field['placeholder']) . '" ' . implode(' ', $custom_attributes) . ' /> ';
            echo '</p>';
        }

        /**
         * Return formatted price.
         * WPML compatible.
         *
         * @since 1.4.0
         * @since 1.6.0 Bug fix. Default currency wholesale price have no currency symbol. WWP-143
         * @since 1.11.3 Bug fix. Removed is_admin() condition. For some reason WPML is hacking it to return true always. This is causing pricing conversion issue with custom page like WWOF.
         * @access public
         *
         * @param float $price Raw price.
         * @return string Formatted price.
         */
        public static function wwp_formatted_price($price, $args = array())
        {

            if (self::is_plugin_active('woocommerce-multilingual/wpml-woocommerce.php')) {

                global $woocommerce_wpml;

                if (!defined('WCML_MULTI_CURRENCIES_INDEPENDENT')) {
                    include_once WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'wpml-woocommerce' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'constants.php';
                }

                if ($woocommerce_wpml->settings['enable_multi_currency'] === WCML_MULTI_CURRENCIES_INDEPENDENT) {

                    /**
                     * Wrap raw price with 'wcml_formatted_price' filter so that conversion will be correct.
                     * Only do this only if the currency is not the same as the default currency.
                     */
                    if ($woocommerce_wpml->multi_currency->get_client_currency() != get_option('woocommerce_currency')) {
                        return apply_filters('wcml_formatted_price', $price);
                    }
                }
            }

            return wc_price($price, $args);
        }

        /**
         * Return price.
         * WPML compatible. Converts price accordingly.
         *
         * @since 1.4.0
         * @access public
         *
         * @param float $price Raw price.
         * @return float Processed price.
         */
        public static function wwp_wpml_price($price)
        {

            if (self::is_plugin_active('woocommerce-multilingual/wpml-woocommerce.php')) {

                global $woocommerce_wpml;

                if (!defined('WCML_MULTI_CURRENCIES_INDEPENDENT')) {
                    include_once WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'wpml-woocommerce' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'constants.php';
                }

                if ($woocommerce_wpml->settings['enable_multi_currency'] === WCML_MULTI_CURRENCIES_INDEPENDENT) {
                    return apply_filters('woocommerce_adjust_price', $price);
                } else {
                    return $price;
                }
            } else {
                return $price;
            }
        }

        /**
         * Get product price including tax. WC 2.7.
         *
         * @since 1.3.1
         * @since 2.1.5 Remove legacy code related to pre-WC 3.0.
         * @access public
         *
         * @param WC_Product $product Product object.
         * @param array      $ars     Array of arguments data.
         * @return float Product price with tax.
         */
        public static function wwp_get_price_including_tax($product, $args)
        {
            return wc_get_price_including_tax($product, $args);
        }

        /**
         * Get product price excluding tax. WC 2.7.
         *
         * @since 1.3.1
         * @since 2.1.5 Remove legacy code related to pre-WC 3.0.
         * @access public
         *
         * @param WC_Product $product Product object.
         * @param array      $ars     Array of arguments data.
         * @return float Product price with no tax.
         */
        public static function wwp_get_price_excluding_tax($product, $args)
        {
            return wc_get_price_excluding_tax($product, $args);
        }

        /**
         * Get product id. WC 2.7.
         *
         * @since 1.3.1
         * @since 2.1.5 Remove legacy code related to pre-WC 3.0.
         * @access public
         *
         * @param WC_Product $product Product object.
         * @return int Product id.
         */
        public static function wwp_get_product_id($product)
        {

            if (is_a($product, 'WC_Product')) {
                return $product->get_id();
            } else {
                error_log('WWP Error : wwp_get_product_id helper functions expect parameter $product of type WC_Product.');
                return 0;
            }
        }

        /**
         * Get variation parent variable product id. WC 2.7.
         *
         * @since 1.3.1
         * @since 2.1.5 Remove legacy code related to pre-WC 3.0.
         * @access public
         *
         * @param WC_Product_Variation $variation Variation object.
         * @return int Variable product id.
         */
        public static function wwp_get_parent_variable_id($variation)
        {
            if (self::wwp_get_product_type($variation) === 'variation') {
                return $variation->get_parent_id();
            } else {
                error_log('WWP Error: wwp_get_parent_variable_id helper function expect parameter $variation as a product variation.');
                return 0;
            }
        }

        /**
         * Get product type. WC 2.7.
         *
         * @since 1.3.1
         * @since 2.1.5 Remove legacy code related to pre-WC 3.0.
         * @access public
         *
         * @param WC_Product $product Product type.
         * @return string Product type.
         */
        public static function wwp_get_product_type($product)
        {

            if (is_a($product, 'WC_Product')) {
                return $product->get_type();
            } else {
                error_log('WWP Error : wwp_get_product_type helper functions expect parameter $product of type WC_Product.');
                return 0;
            }
        }

        /**
         * Get product display price. WC 2.7.
         *
         * @since 1.3.1
         * @since 2.1.5 Remove legacy code related to pre-WC 3.0.
         * @access public
         *
         * @param WC_Product $product Product object.
         * @param array      $args    Array of additional data.
         * @return float Product display price.
         */
        public static function wwp_get_product_display_price($product, $args = array())
        {
            return wc_get_price_to_display($product, $args);
        }

        /**
         * Match a variation to a given set of attributes using a WP_Query. WC 2.7.
         *
         * @since 1.3.1
         * @since 2.1.5 Remove legacy code related to pre-WC 3.0.
         * @access public
         *
         * @param WC_Product_Variable $variable         Variable product.
         * @param array               $match_attributes Attributes to match with.
         * @return int Matched variation id.
         */
        public static function wwp_get_matching_variation($variable, $match_attributes = array())
        {
            $data_store = WC_Data_Store::load('product');
            return $data_store->find_matching_product_variation($variable, $match_attributes);
        }

        /**
         * This is a requirement for 'wwp_get_variable_product_variations' helper function.
         * You see 'get_available_variations' function indirectly calls 'get_price_html', which will then call 'woocommerce_get_price_html'.
         * So what happens is every time we call 'get_available_variations', we fire the 'woocommerce_get_price_html' filter, then our callbacks gets triggered.
         * In short we are executing our callbacks unnecessarily.
         *
         * @since 1.5.0
         * @access public
         *
         * @param boolean $return Boolean flag to determine to either show variation price or not.
         * @return boolean Hard boolean false.
         */
        public static function wwp_hide_woocommerce_show_variation_price($return)
        {

            return false;
        }

        /**
         * Efficient way of getting all variations of a variable product.
         * Please see 'wwp_hide_woocommerce_show_variation_price' above.
         *
         * @since 1.5.0
         * @access public
         *
         * @param WC_Product_Variable $variable_product Variable product object.
         * @return array Array of variable product variations.
         */
        public static function wwp_get_variable_product_variations($variable_product)
        {

            add_filter('woocommerce_show_variation_price', array('WWP_Helper_Functions', 'wwp_hide_woocommerce_show_variation_price'));
            $variations = $variable_product->get_available_variations();
            remove_filter('woocommerce_show_variation_price', array('WWP_Helper_Functions', 'wwp_hide_woocommerce_show_variation_price'));

            return $variations;
        }

        /**
         * Check validity of a save post action.
         *
         * @since 1.6.0
         * @access public
         *
         * @param int    $post_id   Id of the coupon post.
         * @param string $post_type Post type to check.
         * @return bool True if valid save post action, False otherwise.
         */
        public static function check_if_valid_save_post_action($post_id, $post_type)
        {

            if (get_post_type() != $post_type || empty($_POST) || wp_is_post_autosave($post_id) || wp_is_post_revision($post_id) || !current_user_can('edit_post', $post_id)) {
                return false;
            } else {
                return true;
            }
        }

        /**
         * Log deprecated function error to the debug.log file.
         *
         * @since 1.10.0
         * @access public
         *
         * @param array  $trace       debug_backtrace() output
         * @param string $function    Name of depecrated function.
         * @param string $version     Version when the function is set as depecrated since.
         * @param string $replacement Name of function to be replaced.
         */
        public static function deprecated_function($trace, $function, $version, $replacement = null)
        {

            $caller = array_shift($trace);

            $log_string = "The <em>{$function}</em> function is deprecated since version <em>{$version}</em>.";
            $log_string .= $replacement ? " Replace with <em>{$replacement}</em>." : '';
            $log_string .= ' Trace: <strong>' . $caller['file'] . '</strong> on line <strong>' . $caller['line'] . '</strong>';

            error_log(strip_tags($log_string));
        }

        /**
         * Convenience function to fetch WWP version
         *
         * @since 1.14
         * @access public
         *
         * @return string WWP version
         */
        public static function get_wwp_version()
        {
            global $wc_wholesale_prices;
            return $wc_wholesale_prices::VERSION;
        }

        /**
         * Convenience function to fetch WWPP version
         *
         * @since 1.14
         * @access public
         *
         * @return string WWPP version
         */
        public static function get_wwpp_version()
        {
            if (WWP_Helper_Functions::is_wwpp_active()) {
                global $wc_wholesale_prices_premium;
                return $wc_wholesale_prices_premium::VERSION;
            } else {
                return '';
            }
        }

        /**
         * Convenience function to fetch WWOF version
         *
         * @since 1.14
         * @access public
         *
         * @return string WWOF version
         */
        public static function get_wwof_version()
        {
            if (WWP_Helper_Functions::is_wwof_active()) {
                global $wc_wholesale_order_form;
                return $wc_wholesale_order_form::VERSION;
            } else {
                return '';
            }
        }

        /**
         * Convenience function to fetch WWLC version
         *
         * @since 1.14
         * @access public
         *
         * @return string WWLC version
         */
        public static function get_wwlc_version()
        {
            if (WWP_Helper_Functions::is_wwlc_active()) {
                global $wc_wholesale_lead_capture;
                return $wc_wholesale_lead_capture::VERSION;
            } else {
                return '';
            }
        }

        /**
         * Check to see if any paid plugin by Wholesale Suite is active
         *
         * @since 1.14
         * @access public
         *
         * @return bool If a paid plugin (WWPP, WWOF, or WWLC) is active or not
         */
        public static function has_paid_plugin_active()
        {
            return (WWP_Helper_Functions::is_wwpp_active() || WWP_Helper_Functions::is_wwof_active() || WWP_Helper_Functions::is_wwlc_active());
        }

        /**
         * Check to see if the given URL looks like a dev site
         *
         * @since 1.14
         * @access public
         *
         * @return bool If it appears to be a dev site
         */
        public static function is_dev_url($url = '')
        {
            $is_local_url = false;

            // Check if testing constant is set
            if (defined('WWS_TESTING_SITE') && WWS_TESTING_SITE) {
                return false;
            }

            // Use site's URL if nothing provided
            if (empty($url)) {
                $url = get_bloginfo('url');
            }

            // Trim it up
            $url = strtolower(trim($url));

            // Need to get the host...so let's add the scheme so we can use parse_url
            if (false === strpos($url, 'http://') && false === strpos($url, 'https://')) {
                $url = 'http://' . $url;
            }

            $url_parts = parse_url($url);
            $host      = !empty($url_parts['host']) ? $url_parts['host'] : false;

            if (!empty($url) && !empty($host)) {
                if (false !== ip2long($host)) {
                    if (!filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        $is_local_url = true;
                    }
                } else if ('localhost' === $host) {
                    $is_local_url = true;
                }

                $tlds_to_check = array('.local', ':8888', ':8080', ':8081', '.invalid', '.example', '.test');
                foreach ($tlds_to_check as $tld) {
                    if (false !== strpos($host, $tld)) {
                        $is_local_url = true;
                        break;
                    }
                }
                if (substr_count($host, '.') > 1) {
                    $subdomains_to_check = array('dev.', '*.staging.', 'beta.', 'test.');
                    foreach ($subdomains_to_check as $subdomain) {
                        $subdomain = str_replace('.', '(.)', $subdomain);
                        $subdomain = str_replace(array('*', '(.)'), '(.*)', $subdomain);
                        if (preg_match('/^(' . $subdomain . ')/', $host)) {
                            $is_local_url = true;
                            break;
                        }
                    }
                }
            }

            return $is_local_url;
        }

        /**
         * Retrieve all premium plugin license data if present
         *
         * @since 1.14
         * @access public
         *
         * @return array Array containing license data (if present, otherwise this will be empty)
         */
        public static function get_license_data()
        {
            $license_data = array();

            if (WWP_Helper_Functions::is_wwpp_active()) {
                $wwpp_license_email = is_multisite() ? get_site_option(WWPP_OPTION_LICENSE_EMAIL) : get_option(WWPP_OPTION_LICENSE_EMAIL);
                $wwpp_license_key   = is_multisite() ? get_site_option(WWPP_OPTION_LICENSE_KEY) : get_option(WWPP_OPTION_LICENSE_KEY);

                $license_data['wwpp_license_email'] = $wwpp_license_email;
                $license_data['wwpp_license_key']   = $wwpp_license_key;
            }

            if (WWP_Helper_Functions::is_wwof_active()) {
                $wwof_license_email = is_multisite() ? get_site_option(WWOF_OPTION_LICENSE_EMAIL) : get_option(WWOF_OPTION_LICENSE_EMAIL);
                $wwof_license_key   = is_multisite() ? get_site_option(WWOF_OPTION_LICENSE_KEY) : get_option(WWOF_OPTION_LICENSE_KEY);

                $license_data['wwof_license_email'] = $wwof_license_email;
                $license_data['wwof_license_key']   = $wwof_license_key;
            }

            if (WWP_Helper_Functions::is_wwlc_active()) {
                $wwlc_license_email = is_multisite() ? get_site_option(WWLC_OPTION_LICENSE_EMAIL) : get_option(WWLC_OPTION_LICENSE_EMAIL);
                $wwlc_license_key   = is_multisite() ? get_site_option(WWLC_OPTION_LICENSE_KEY) : get_option(WWLC_OPTION_LICENSE_KEY);

                $license_data['wwlc_license_email'] = $wwlc_license_email;
                $license_data['wwlc_license_key']   = $wwlc_license_key;
            }

            return $license_data;
        }

        /**
         * Helper function to fetch the current WooCommerce version and return it
         *
         * @since 1.14
         * @access public
         *
         * @return string WooCommerce version as reported by WooCommerce
         */
        public static function get_current_woocommerce_version()
        {
            $woocommerce_data = self::get_woocommerce_data();
            return isset($woocommerce_data['Version']) ? $woocommerce_data['Version'] : '';
        }

        /**
         * Load React Scripts.
         *
         * @since 2.0
         * @access public
         */
        public static function load_react_scripts($args)
        {

            global $wc_wholesale_prices;

            if (isset($args['dir_name'])) {

                // JS Files
                $js_path = $args['js_path'] . 'app/' . $args['dir_name'] . '/build/static/js';

                if (file_exists($js_path)) {

                    $js_files = scandir($js_path);

                    if ($js_files) {
                        foreach ($js_files as $key => $js_file) {

                            // Get the extension using pathinfo
                            $extension = pathinfo($js_file, PATHINFO_EXTENSION);

                            if ($extension === 'js') {
                                wp_enqueue_script($args['handle'] . $key, $args['js_url'] . 'app/' . $args['dir_name'] . '/build/static/js/' . $js_file, array('jquery'), $wc_wholesale_prices::VERSION, true);
                            }

                        }

                    }

                }

                // CSS Files
                $css_path = $args['js_path'] . 'app/' . $args['dir_name'] . '/build/static/css';

                if (file_exists($css_path)) {

                    $css_files = scandir($css_path);

                    if ($css_files) {

                        foreach ($css_files as $key => $css_file) {

                            // Get the extension using pathinfo
                            $extension = pathinfo($css_file, PATHINFO_EXTENSION);

                            if ($extension === 'css') {
                                wp_enqueue_style($args['handle'] . $key, $args['js_url'] . 'app/' . $args['dir_name'] . '/build/static/css/' . $css_file, array(), $wc_wholesale_prices::VERSION, 'all');
                            }

                        }

                    }

                }

            }

        }

        /**
         * Check if current WWP is v2.0.
         * Thi is used in other WWS Plugis.
         *
         * @since 2.0
         * @access public
         */
        public static function is_wwp_v2()
        {

            if (self::is_plugin_active('woocommerce-wholesale-prices/woocommerce-wholesale-prices.bootstrap.php')) {

                if (!function_exists('get_plugin_data')) {
                    require_once ABSPATH . '/wp-admin/includes/plugin.php';
                }

                $wwp_data = get_plugin_data(WWP_PLUGIN_PATH . 'woocommerce-wholesale-prices.bootstrap.php');

                if (version_compare($wwp_data['Version'], '2', '>=')) {
                    return true;
                }

            }

            return false;

        }

        /**
         * Check the min version of WWS Premium Plugins.
         *
         * @since 2.0
         * @access public
         */
        public static function check_wws_plugin_min_version($plugin, $version)
        {

            if (!function_exists('get_plugin_data')) {
                require_once ABSPATH . '/wp-admin/includes/plugin.php';
            }

            switch ($plugin) {
                case "wwof":

                    if (self::is_plugin_active('woocommerce-wholesale-order-form/woocommerce-wholesale-order-form.bootstrap.php')) {

                        $data = get_plugin_data(WWOF_MAIN_PLUGIN_FILE_PATH);

                        if (version_compare($data['Version'], $version, '>=')) {
                            return true;
                        }

                    }

                    break;

                case "wwlc":

                    if (self::is_plugin_active('woocommerce-wholesale-lead-capture/woocommerce-wholesale-lead-capture.bootstrap.php')) {

                        $data = get_plugin_data(WWLC_MAIN_PLUGIN_FILE_PATH);

                        if (version_compare($data['Version'], $version, '>=')) {
                            return true;
                        }

                    }

                    break;

                default:

                    if (self::is_plugin_active('woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php')) {

                        $data = get_plugin_data(WWPP_MAIN_PLUGIN_FILE_PATH);

                        if (version_compare($data['Version'], $version, '>=')) {
                            return true;
                        }

                    }

            }

            return false;

        }

    }
}
