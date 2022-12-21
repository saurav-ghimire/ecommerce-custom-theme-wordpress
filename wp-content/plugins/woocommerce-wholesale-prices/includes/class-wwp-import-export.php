<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

if (!class_exists('WWP_Import_Export')) {

    /**
     * Model that houses the logic of wholesale roles admin page.
     *
     * @since 1.11.5
     */
    class WWP_Import_Export
    {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
         */

        /**
         * Property that holds the single main instance of WWP_Import_Export.
         *
         * @since 1.11.5
         * @access private
         * @var WWP_Import_Export
         */
        private static $_instance;

        /**
         * Model that houses the logic of retrieving information relating to wholesale role/s of a user.
         *
         * @since 1.11.5
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
         * WWP_Import_Export constructor.
         *
         * @since 1.11.5
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Import_Export model.
         */
        public function __construct($dependencies)
        {

            $this->_wwp_wholesale_roles = $dependencies['WWP_Wholesale_Roles'];

        }

        /**
         * Ensure that only one instance of WWP_Import_Export is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.11.5
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Import_Export model.
         * @return WWP_Import_Export
         */
        public static function instance($dependencies = array())
        {

            if (!self::$_instance instanceof self) {
                self::$_instance = new self($dependencies);
            }

            return self::$_instance;

        }

        /**
         * Bug Fix: When decimal separator is set to comma, the wholesale price is not imported properly.
         *
         * @since 1.11.5
         * @since 1.14.2    This is supposed to be not needed since we already have export code that will properly set the
         *                  decimal based on wc decimal separator. But since, csv file can be manually updated by shop owners we will
         *                  need to properly import wholesale price based on wc decimal sepator.
         * @access public
         *
         * @param array     $data       WC Product Data
         * @param object    $importer   WC_Product_CSV_Importer Object
         * @return array
         */
        public function wholesale_price_import($data)
        {

            $decimal_separator = get_option('woocommerce_price_decimal_sep');

            if ($decimal_separator !== '.' && !empty($data['meta_data'])) {

                $wholesale_prices_meta = $this->wholesale_prices_meta();

                foreach ($data['meta_data'] as $index => $meta) {
                    if (in_array($meta['key'], $wholesale_prices_meta)) {
                        $data['meta_data'][$index]['value'] = str_replace(',', '.', $meta['value']);
                    }

                }

            }

            return $data;

        }

        /**
         * Properly apply decimal based in WC Decimal Separator setting on product export.
         *
         * @since 1.14.2
         * @access public
         *
         * @param mixed     $value      Mixed value.
         * @param object    $meta       WC_Meta_Data Object
         * @param object    $product    WC_Product_Simple | WC_Product_Variable object. etc
         * @param array     $row        Array of exported product data
         * @return mixed String|Int|Object
         */
        public function wholesale_price_export($value, $meta, $product, $row)
        {

            $wholesale_prices_meta = $this->wholesale_prices_meta();

            if (in_array($meta->key, $wholesale_prices_meta)) {

                if (floatval($value) > 0) {
                    return wc_format_localized_price($value);
                }

            }

            return $value;

        }

        /**
         * Wholesale prices meta keys. Store in cache, expensive operation if there are multiple products since this is called every loop.
         *
         * @since 1.14.2
         * @access public
         * @return array
         */
        public function wholesale_prices_meta()
        {

            $cache = get_transient('wwpp_wholesale_prices_meta_transient');

            if (!empty($cache)) {
                return $cache;
            }

            // Lets just get all registered wholesale roles even if WWPP is not active
            $all_registered_wholesale_roles = maybe_unserialize(get_option(WWP_OPTIONS_REGISTERED_CUSTOM_ROLES));
            $wholesale_price_meta           = array();

            if (!is_array($all_registered_wholesale_roles)) {
                $all_registered_wholesale_roles = array();
            }

            foreach ($all_registered_wholesale_roles as $role_key => $role_data) {
                $wholesale_price_meta[] = $role_key . "_wholesale_price";
            }

            // Delete in 2 Minutes
            set_transient('wwpp_wholesale_prices_meta_transient', $wholesale_price_meta, 120);

            return $wholesale_price_meta;

        }

        /*
        |---------------------------------------------------------------------------------------------------------------
        | Execute model
        |---------------------------------------------------------------------------------------------------------------
         */

        /**
         * Execute model.
         *
         * @since 1.11.5
         * @access public
         */
        public function run()
        {

            // Import wholesale prices with proper decimal separator
            add_filter('woocommerce_product_import_process_item_data', array($this, 'wholesale_price_import'), 10, 1);

            // Export wholesale prices with proper decimal separator.
            add_filter('woocommerce_product_export_meta_value', array($this, 'wholesale_price_export'), 10, 4);
        }

    }

}
