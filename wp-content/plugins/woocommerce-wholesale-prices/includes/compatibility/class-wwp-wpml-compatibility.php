<?php defined('ABSPATH') || exit;

class WWP_WPML_Compatibility {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of WWP_WPML_Compatibility.
     *
     * @since 1.13
     * @access private
     * @var WWP_WPML_Compatibility
     */
    private static $_instance;

    /**
     * WWP_WPML_Compatibility constructor.
     *
     * @since 1.13
     * @access public
     */
    public function __construct() {}

    /**
     * Ensure that only one instance of WWP_WPML_Compatibility is loaded or can be loaded (Singleton Pattern).
     *
     * @since 1.12
     * @access public
     *
     * @param array $dependencies Array of instance objects of all dependencies of WWP_WPML_Compatibility model.
     * @return WWP_WPML_Compatibility
     */
    public static function instance($dependencies = array()) {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self($dependencies);
        }

        return self::$_instance;

    }

    /**
     * Checks if the order being updated comes from a wholesale customer,
     * then make sure to get the proper wholesale price of the product.
     * Fixes the issue where the updated order displays the regular price instead of the wholesale price.
     *
     * @since 1.13
     * @access public
     *
     * @param array $items
     * @param array $order
     * @return array
     */
    public function order_items_updated($items, $order) {

        if (isset($_POST['action']) && in_array(
            $_POST['action'],
            [
                'woocommerce_add_order_item',
                'woocommerce_save_order_items',
            ],
            true
        )) {

            foreach ($items as $item) {

                if ('line_item' === $item->get_type()) {

                    wc_delete_order_item_meta($item->get_id(), WCML_Multi_Currency_Orders::WCML_CONVERTED_META_KEY_PREFIX . 'total');
                    wc_delete_order_item_meta($item->get_id(), WCML_Multi_Currency_Orders::WCML_CONVERTED_META_KEY_PREFIX . 'subtotal');

                }

            }

            add_filter('woocommerce_product_get_price', array($this, 'get_wholesale_price'), 10, 2);
            add_filter('woocommerce_product_variation_get_price', array($this, 'get_wholesale_price'), 10, 2);

        }

        return $items;
    }

    /**
     * Return wholesale price if the order is coming from a wholesale customer.
     *
     * @since 1.13
     * @access public
     *
     * @param array $price
     * @param array $product
     * @return array
     */
    public function get_wholesale_price($price, $product) {

        $product_id = $product->get_id();
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';
        $products = [];

        if (isset($_POST['items']) && !empty($_POST['items'])) {
            parse_str($_POST['items'], $output);
            foreach ($output['order_item_id'] as $id) {
                $variation_id = wc_get_order_item_meta($id, '_variation_id', true);
                if (!empty($variation_id)) {
                    $products[$variation_id] = array(
                        'item_id' => $id,
                    );
                } else {
                    $products[wc_get_order_item_meta($id, '_product_id', true)] = array(
                        'item_id' => $id,
                    );
                }
            }
        }

        $item_id = $products[$product_id]['item_id'];
        $item_wholesale_role = wc_get_order_item_meta($item_id, '_wwp_wholesale_role', true);
        $item_wholesale_priced = wc_get_order_item_meta($item_id, '_wwp_wholesale_priced', true);

        if (!empty($order_id) && !empty($item_wholesale_role) && $item_wholesale_priced == 'yes') {

            $wholesale_data = WWP_Wholesale_Prices::get_product_wholesale_price_on_shop_v3($product_id, array($item_wholesale_role));

            return !empty($wholesale_data['wholesale_price']) ? $wholesale_data['wholesale_price'] : $price;
        }

        return $price;
    }

    /**
     * Execute the model.
     *
     * @since 1.13
     */
    public function run() {

        add_action('wcml_loaded', function () {

            global $woocommerce_wpml;

            // Multicurrency is enabled
            if ($woocommerce_wpml && $woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT) {

                add_filter('woocommerce_order_get_items', array($this, 'order_items_updated'), 20, 2);

            }

        });

    }

}
