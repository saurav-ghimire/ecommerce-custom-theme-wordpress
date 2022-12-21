<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

if (!class_exists('WWP_Products_CPT')) {

    /**
     * Model that houses the logic of extending WC products cpt.
     *
     * @since 1.3.0
     */
    class WWP_Products_CPT
    {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
         */

        /**
         * Property that holds the single main instance of WWP_Products_CPT.
         *
         * @since 1.3.0
         * @access private
         * @var WWP_Products_CPT
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
         * Model that houses logic of wholesale prices.
         *
         * @since 1.6.0
         * @access public
         * @var WWP_Wholesale_Prices
         */
        private $_wwp_wholesale_prices;

        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
         */

        /**
         * WWP_Products_CPT constructor.
         *
         * @since 1.3.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Products_CPT model.
         */
        public function __construct($dependencies)
        {

            $this->_wwp_wholesale_roles  = $dependencies['WWP_Wholesale_Roles'];
            $this->_wwp_wholesale_prices = $dependencies['WWP_Wholesale_Prices'];

        }

        /**
         * Ensure that only one instance of WWP_Products_CPT is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.3.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Products_CPT model.
         * @return WWP_Products_CPT
         */
        public static function instance($dependencies)
        {

            if (!self::$_instance instanceof self) {
                self::$_instance = new self($dependencies);
            }

            return self::$_instance;

        }

        /**
         * Add wholesale price column to the product listing page.
         *
         * @since 1.0.1
         * @since 1.3.0 Refactor codebase and move to its own model.
         *
         * @param array $columns Array of columns.
         * @return array Filtered array of columns.
         */
        public function add_wholesale_price_column_to_product_cpt_listing($columns)
        {

            $all_keys    = array_keys($columns);
            $price_index = array_search('price', $all_keys);

            return array_slice($columns, 0, $price_index + 1, true) + array('wholesale_price' => __('Wholesale Price', 'woocommerce-wholesale-prices')) + array_slice($columns, $price_index + 1, null, true);

        }

        /**
         * Add wholesale price column data for each product on the product listing page
         *
         * @since 1.0.1
         * @since 1.3.0 Refactor codebase and move to its model.
         * @since 1.6.0 Refactor codebase to reuse WWP_Wholesale_Price Model.
         *
         * @param string $column  Current column.
         * @param int    $post_id Product Id.
         */
        public function add_wholesale_price_column_value_to_product_cpt_listing($column, $post_id)
        {

            switch ($column) {

                case 'wholesale_price': ?>

                    <div class="wholesale_prices" id="wholesale_prices_<?php echo $post_id; ?>">

                        <style>ins { text-decoration: none !important; }</style>

                        <?php
$all_wholesale_roles = $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles();
                    $product            = wc_get_product($post_id);

                    foreach ($all_wholesale_roles as $roleKey => $role) {

                        // We pass 1 as the price, it doesn't matter, its not being used here in this scenario, as long as we pass truthy value we are good.
                        $wholesale_price = $this->_wwp_wholesale_prices->wholesale_price_html_filter(1, $product, array($roleKey), true);

                        if (strpos($wholesale_price, 'wholesale_price_container') !== false && !empty($wholesale_price)) {?>

                                <div id="<?php echo $roleKey; ?>_wholesale_price" class="wholesale_price">
                                    <div class="wholesale_role"><b><?php echo $role['roleName'] ?></b></div>
                                    <?php echo $wholesale_price; ?>
                                </div>

                            <?php }

                    }?>

                    </div>

                    <?php

                    break;

                default:
                    break;

            }

        }

        /**
         * Add wholesale price column styling.
         *
         * @since 1.6.3
         * @access public
         */
        public function add_wholesale_price_column_styling()
        {
            ?>

            <style>
                th#wholesale_price {
                    width: 10% !important;
                }

                table.wp-list-table .column-product_cat, table.wp-list-table .column-product_tag {
                    width:5%;
                }
            </style>

            <?php
}

        /*
        |--------------------------------------------------------------------------
        | Execute Model
        |--------------------------------------------------------------------------
         */

        /**
         * Execute model.
         *
         * @since 1.3.0
         * @access public
         */
        public function run()
        {

            if (get_option('wwpp_hide_wholesale_price_on_product_listing') === 'yes') {
                return;
            }

            add_filter('manage_product_posts_columns', array($this, 'add_wholesale_price_column_to_product_cpt_listing'), 99, 1);
            add_action('manage_product_posts_custom_column', array($this, 'add_wholesale_price_column_value_to_product_cpt_listing'), 99, 2);
            add_action('admin_head', array($this, 'add_wholesale_price_column_styling'));

        }

    }

}
