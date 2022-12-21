<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

if (!class_exists('WWP_Wholesale_Price_Grouped_Product')) {

    /**
     * Class that houses the logic of integrating wwp with grouped products.
     *
     * @since 2.0.2
     */
    class WWP_Wholesale_Price_Grouped_Product
    {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
         */

        /**
         * Property that holds the single main instance of WWP_Wholesale_Price_Grouped_Product.
         *
         * @since 2.0.2
         * @access private
         * @var WWP_Wholesale_Price_Grouped_Product
         */
        private static $_instance;

        /**
         * Model that houses the logic of retrieving information relating to wholesale role/s of a user.
         *
         * @since 2.0.2
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
         * WWP_Wholesale_Price_Grouped_Product constructor.
         *
         * @since 2.0.2
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Wholesale_Price_Grouped_Product model.
         */
        public function __construct($dependencies)
        {

            $this->_wwp_wholesale_roles = $dependencies['WWP_Wholesale_Roles'];

        }

        /**
         * Ensure that only one instance of WWP_Wholesale_Price_Grouped_Product is loaded or can be loaded (Singleton Pattern).
         *
         * @since 2.0.2
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Wholesale_Price_Grouped_Product model.
         * @return WWP_Wholesale_Price_Grouped_Product
         */
        public static function instance($dependencies)
        {

            if (!self::$_instance instanceof self) {
                self::$_instance = new self($dependencies);
            }

            return self::$_instance;

        }

        /**
         * Filter grouped product price range to apply wholesale pricing.
         *
         * @since 2.0.2
         * @access public
         *
         * @param string             $price   Price html.
         * @param WC_Product_Grouped $product Grouped product object.
         * @return string Filtered price html.
         */
        public function wholesale_grouped_price_html($price, $product)
        {

            $user_wholesale_role = $this->_wwp_wholesale_roles->getUserWholesaleRole();

            if (!empty($user_wholesale_role)) {

                $tax_display_mode = get_option('woocommerce_tax_display_shop');
                $child_prices = array();
                $wholesale_price_range = "";
                $has_member_with_wholesale_price = false;

                foreach ($product->get_children() as $child_id) {

                    $child_price = get_post_meta($child_id, '_price', true);
                    $price_arr = WWP_Wholesale_Prices::get_product_wholesale_price_on_shop_v3($child_id, $user_wholesale_role);
                    $child_wholesale_price = $price_arr['wholesale_price_raw'];

                    if (!$has_member_with_wholesale_price && $child_wholesale_price) {
                        $has_member_with_wholesale_price = true;
                    }

                    $child_prices[] = ($child_wholesale_price) ? $child_wholesale_price : $child_price;

                }

                // Only do this if at least one member of this bundle product has wholesale price
                if ($has_member_with_wholesale_price) {

                    $child_prices = array_unique($child_prices);
                    $get_price_method = $tax_display_mode === 'incl' ? 'wwp_get_price_including_tax' : 'wwp_get_price_excluding_tax';

                    if (!empty($child_prices)) {

                        $min_price = min($child_prices);
                        $max_price = max($child_prices);

                    } else {

                        $min_price = '';
                        $max_price = '';

                    }

                    if ($min_price) {

                        if ($min_price == $max_price) {
                            $display_price = WWP_Helper_Functions::wwp_formatted_price(WWP_Helper_Functions::$get_price_method($product, array('qty' => 1, 'price' => $min_price)));
                        } else {

                            $from = WWP_Helper_Functions::wwp_formatted_price(WWP_Helper_Functions::$get_price_method($product, array('qty' => 1, 'price' => $min_price)));
                            $to = WWP_Helper_Functions::wwp_formatted_price(WWP_Helper_Functions::$get_price_method($product, array('qty' => 1, 'price' => $max_price)));

                            $display_price = wc_format_price_range($from, $to);

                        }

                        $wholesale_price_range .= $display_price . WWP_Wholesale_Prices::get_wholesale_price_suffix($product, $user_wholesale_role, $price_arr['wholesale_price_with_no_tax']);

                    }

                    if (strcasecmp($wholesale_price_range, '') != 0) {

                        if (get_option('wwpp_settings_hide_original_price') !== "yes") {

                            // Crush out existing prices, regular and sale
                            if (strpos($price, 'ins') !== false) {
                                $wholesale_price_html = str_replace('ins', 'del', $price);
                            } else {

                                $wholesale_price_html = str_replace('<span', '<del><span', $price);
                                $wholesale_price_html = str_replace('</span>', '</span></del>', $wholesale_price_html);

                            }

                        } else {
                            $wholesale_price_html = '';
                        }

                        $wholesale_price_title_text = __('Wholesale Price:', 'woocommerce-wholesale-prices');
                        $wholesale_price_title_text = apply_filters('wwp_filter_wholesale_price_title_text', $wholesale_price_title_text);

                        $wholesale_price_html .= '<span style="display: block;" class="wholesale_price_container">
                                                    <span class="wholesale_price_title">' . $wholesale_price_title_text . '</span>
                                                    <ins>' . $wholesale_price_range . '</ins>
                                                </span>';

                        return apply_filters('wwp_filter_wholesale_price_html', $wholesale_price_html, $price, $product, $user_wholesale_role, $wholesale_price_title_text, '', '');

                    }

                }

            }

            return $price;

        }

        /**
         * Set <wholesale_role>_have_wholesale_price meta into the parent group product.
         *
         * @since 2.0.2
         * @access public
         *
         * @param int   $product_id   Post ID.
         */
        public function insert_have_wholesale_price_meta($product_id)
        {

            global $wpdb;

            $product = wc_get_product($product_id);

            if (is_a($product, 'WC_Product') && $product->get_type() === 'grouped') {

                // Remove meta
                $wpdb->query("
                    DELETE FROM $wpdb->postmeta
                    WHERE meta_key LIKE '%_have_wholesale_price'
                        AND post_id = $product_id
                ");

                $grouped_products = get_post_meta($product_id, '_children', true);

                if (!empty($grouped_products)) {

                    $have_wholesale_prices = $wpdb->get_results("
                                        SELECT distinct pm.meta_key
                                        FROM $wpdb->postmeta pm
                                        WHERE pm.post_id IN ( " . implode(',', $grouped_products) . " )
                                            AND pm.meta_key LIKE '%_have_wholesale_price'
                                            AND pm.meta_value = 'yes'");

                    if (!empty($have_wholesale_prices)) {

                        // Set parent group product <wholesale_role>_have_wholesale_price meta so that it will be visible when "Only Show Wholesale Products To Wholesale Users" is enabled
                        foreach ($have_wholesale_prices as $have_wholesale_price) {
                            $wpdb->query($wpdb->prepare(
                                "INSERT INTO $wpdb->postmeta (post_id,meta_key,meta_value) VALUES (%d,%s,%s)",
                                $product_id,
                                $have_wholesale_price->meta_key,
                                "yes"
                            ));
                        }

                    }

                }

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Execute model
        |--------------------------------------------------------------------------
         */

        /**
         * Execute model.
         *
         * since 2.0.2
         * @access public
         */
        public function run()
        {
            if( !WWP_Helper_Functions::is_wwpp_active() || 
                ( WWP_Helper_Functions::is_wwpp_active() && WWP_Helper_Functions::get_wwpp_version() >= '1.27.8' ) ) {
                add_filter('woocommerce_grouped_price_html', array($this, 'wholesale_grouped_price_html'), 10, 2);
                add_action('save_post', array($this, 'insert_have_wholesale_price_meta'), 10, 1);
            }

        }

    }

}
