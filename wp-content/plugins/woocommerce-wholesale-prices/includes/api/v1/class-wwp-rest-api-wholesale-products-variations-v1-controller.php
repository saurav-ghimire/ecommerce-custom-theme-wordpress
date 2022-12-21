<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

if (!class_exists('WWP_REST_Wholesale_Product_Variations_V1_Controller')) {

    /**
     * REST API Wholesale Product Variations Controller class.
     *
     * @since 1.12
     * @extends WC_REST_Product_Variations_Controller
     */
    class WWP_REST_Wholesale_Product_Variations_V1_Controller extends WC_REST_Product_Variations_Controller
    {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
         */

        /**
         * Endpoint namespace.
         *
         * @var string
         */
        protected $namespace = 'wholesale/v1';

        /**
         * Route base.
         *
         * @var string
         */
        protected $rest_base = 'products/(?P<product_id>[\d]+)/variations';

        /**
         * Post type.
         *
         * @var string
         */
        protected $post_type = 'product_variation';

        /**
         * WWP_REST_Wholesale_Products_v1_Controller instance.
         *
         * @var object
         */
        protected $wwp_rest_wholesale_products_v1_controller;

        /**
         * Wholesale Roles.
         *
         * @var array
         */
        protected $registered_wholesale_roles;

        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
         */

        /**
         * WWP_REST_Wholesale_Product_Variations_V1_Controller constructor.
         *
         * @since 1.12
         * @access public
         */
        public function __construct()
        {

            global $wc_wholesale_prices;

            $this->wwp_rest_wholesale_products_v1_controller = $wc_wholesale_prices->wwp_rest_api->wwp_rest_api_wholesale_products_controller;
            $this->registered_wholesale_roles                = $wc_wholesale_prices->wwp_wholesale_roles->getAllRegisteredWholesaleRoles();

            // Inherit routes from 'wc/v3' namespace
            $this->register_routes();

            // Include wholesale data into the response
            add_filter("woocommerce_rest_prepare_{$this->post_type}_object", array($this->wwp_rest_wholesale_products_v1_controller, "add_wholesale_data_on_response"), 10, 3);

            // Filter the query arguments of the request.
            add_filter("woocommerce_rest_{$this->post_type}_object_query", array($this, "query_args"), 10, 2);

            // Fires after a single object is created or updated via the REST API.
            add_action("woocommerce_rest_insert_{$this->post_type}_object", array($this->wwp_rest_wholesale_products_v1_controller, "create_update_wholesale_product"), 10, 3);

            // Insert '_have_wholesale_price' and '_variations_with_wholesale_price' meta after inserting variation
            add_action('wwp_after_variation_create_item', array($this, 'update_variable_wholesale_price_meta_flag'), 10, 2);

            // After Deleting Variation delete parent meta _variations_with_wholesale_price
            add_action('wwp_after_variation_delete_item', array($this, 'update_variable_wholesale_price_meta_flag'), 10, 2);

            // Filter the result returned
            add_filter('wwp_rest_response_product_object', array($this, 'filter_product_object'), 10, 3);

        }

        /**
         * Filter data result
         *
         * @since 1.16.1
         * @access public
         *
         * @param string|array $request
         * @return string|array $response
         */
        public function filter_product_object($response, $object, $request)
        {

            if (isset($request['fields']) && !empty($request['fields'])) {
                $data    = $response->get_data();
                $newdata = [];
                foreach (explode(",", $request['fields']) as $field) {
                    $newdata[$field] = $data[$field];
                }
                $response->set_data($newdata);
            }

            return $response;
        }

        /**
         * Override the parent method.
         *
         * @param array           $args    Request args.
         * @param WP_REST_Request $request Request data.
         *
         * @since 1.12
         * @access public
         * @return array
         */
        public function query_args($args, $request)
        {

            return $this->wwp_rest_wholesale_products_v1_controller->query_args($args, $request, $this->post_type);

        }

        /**
         * Custom method for updating _have_wholesale_price and _variations_with_wholesale_price meta in variable level if the variation is deleted.
         *
         * @param WP_REST_Request         $request
         * @param WP_REST_Response        $response
         *
         * @since 1.12
         * @since 1.16  Merge function set_wholesale_price_meta_variable into this function.
         * @access public
         * @return WP_REST_Response|WP_Error
         */
        public function update_variable_wholesale_price_meta_flag($request, $response)
        {

            global $wc_wholesale_prices;

            $method = $request->get_method();

            if (isset($request['product_id'])) {

                $variable_id  = intval($request['product_id']);
                $variation_id = $response->data['id'];

                // Variation is Deleted
                if ($method === 'DELETE') {

                    $wholesale_roles = $this->registered_wholesale_roles;
                    $product         = wc_get_product($variable_id);
                    $variations      = $product->get_available_variations();

                    if ($wholesale_roles) {

                        foreach ($wholesale_roles as $role => $data) {

                            delete_post_meta($variable_id, $role . '_variations_with_wholesale_price', $variation_id);

                            $price_arr = $wc_wholesale_prices->wwp_wholesale_prices->get_product_wholesale_price_on_shop_v3($variable_id, array($role));

                            if (!empty($price_arr['wholesale_price'])) {
                                update_post_meta($variable_id, $role . '_have_wholesale_price', 'yes');
                            } else {
                                delete_post_meta($variable_id, $role . '_have_wholesale_price');
                            }

                        }

                    }

                    // If all variations are removed then set stock status to outofstock
                    if (empty($variations)) {
                        update_post_meta($variable_id, '_stock_status', 'outofstock');
                    }

                }

                // Variation is Created
                if ($method === 'POST') {

                    $wholesale_role_dicounts = "";

                    if (isset($response->data['wholesale_data']) && isset($response->data['wholesale_data']['wholesale_price'])) {
                        $wholesale_role_dicounts = $response->data['wholesale_data']['wholesale_price'];
                    }

                    if ($wholesale_role_dicounts) {

                        foreach ($wholesale_role_dicounts as $role => $discount) {

                            update_post_meta($variable_id, $role . '_have_wholesale_price', 'yes');
                            add_post_meta($variable_id, $role . '_variations_with_wholesale_price', $variation_id);

                        }

                    }

                }

            }

        }

        /**
         * Override the parent method.
         * Add checking on the response when fetching variations.
         *
         * @param WP_REST_Request   $request Request data.
         *
         * @since 1.12
         * @access public
         * @return WP_REST_Response|WP_Error
         */
        public function get_items($request)
        {

            $extra_checks = apply_filters("wwp_before_get_items_{$this->post_type}_extra_check", array('is_valid' => true, 'message' => ''), $request);

            // Extra check for wholesale visibility.
            if (isset($extra_checks['is_valid']) && !$extra_checks['is_valid']) {
                return $extra_checks['message'];
            }

            do_action('wwp_before_variation_get_items', $request);

            $wholesale_role = isset($request['wholesale_role']) ? sanitize_text_field($request['wholesale_role']) : '';
            $product_id     = (int) $request['product_id'];

            if (!empty($wholesale_role) && !isset($this->registered_wholesale_roles[$wholesale_role])) {
                return new WP_Error('wholesale_rest_variation_cannot_view', __('Invalid wholesale role.', 'woocommerce-wholesale-prices'), array('status' => 400));
            }

            $response = parent::get_items($request);

            do_action('wwp_after_variation_get_items', $request, $response);

            return $response;

        }

        /**
         * Override the parent method.
         * Validate if fetched variation is wholesale product
         *
         * @param WP_REST_Request   $request
         *
         * @since 1.12
         * @access public
         * @return WP_REST_Response|WP_Error
         */
        public function get_item($request)
        {

            $extra_checks = apply_filters("wwp_before_get_item_{$this->post_type}_extra_check", array('is_valid' => true, 'message' => ''), $request);

            // Extra check for wholesale visibility.
            if (isset($extra_checks['is_valid']) && !$extra_checks['is_valid']) {
                return $extra_checks['message'];
            }

            do_action('wwp_before_variation_get_item', $request);

            $wholesale_role = isset($request['wholesale_role']) ? sanitize_text_field($request['wholesale_role']) : '';

            if (!empty($wholesale_role) && !isset($this->registered_wholesale_roles[$wholesale_role])) {
                return new WP_Error('wholesale_rest_variation_cannot_view', __('Invalid wholesale role.', 'woocommerce-wholesale-prices'), array('status' => 400));
            }

            $only_return_wholesale_products = !empty($request['return_wholesale_products']) ? filter_var($request['return_wholesale_products'], FILTER_VALIDATE_BOOLEAN) : false;

            // Skip checking if wholesale product when general discount is set for this current wholesale role
            // If wholesale_customer is set and if "only_return_wholesale_products" parameter is true OR WWPP "Only show wholesale products to wholesale users" option is enabled then proceed checking if valid wholesale product
            if (apply_filters('wwp_general_discount_is_set', false, $request) === false && ($only_return_wholesale_products || apply_filters('wwp_only_show_wholesale_products_to_wholesale_users', false, $request))) {

                if (empty($wholesale_role)) {
                    return new WP_Error('wholesale_rest_variation_cannot_view', __('Cannot view, please provide wholesale_role parameter.', 'woocommerce-wholesale-prices'), array('status' => 400));
                }

                $wholesale_data = WWP_Wholesale_Prices::get_product_wholesale_price_on_shop_v3($request['id'], array($wholesale_role));

                if (isset($wholesale_data['wholesale_price']) && empty($wholesale_data['wholesale_price'])) {
                    return new WP_Error('wholesale_rest_cannot_view', __('Not a wholesale product.', 'woocommerce-wholesale-prices'), array('status' => rest_authorization_required_code()));
                }

            }

            $response = parent::get_item($request);

            do_action('wwp_after_variation_get_item', $request, $response);

            return $response;

        }

        /**
         * Override the parent method.
         * Extra validation on variation creation.
         *
         * @param WP_REST_Request   $request
         *
         * @since 1.12
         * @access public
         * @return WP_REST_Response|WP_Error
         */
        public function create_item($request)
        {

            do_action('wwp_before_variation_create_item', $request);

            if (isset($request['wholesale_price'])) {

                // Check if wholesale price is set. Make wholesale price as the basis to create wholesale product.
                $error = $this->wwp_rest_wholesale_products_v1_controller->validate_wholesale_price($request, $this->registered_wholesale_roles, 'variation');

                if (is_a($error, 'WP_Error')) {
                    return $error;
                }

            }

            $response = parent::create_item($request);

            do_action('wwp_after_variation_create_item', $request, $response);

            return $response;

        }

        /**
         * Override the parent method.
         * Add wholesale price checks when updating wholesale product variation.
         *
         * @param WP_REST_Request   $request
         *
         * @since 1.16
         * @access public
         * @return WP_REST_Response|WP_Error
         */
        public function update_item($request)
        {

            do_action('wwp_before_variation_update_item', $request);

            if (isset($request['wholesale_price'])) {

                // Check if wholesale price is set. Make wholesale price as the basis to create wholesale product.
                $error = $this->wwp_rest_wholesale_products_v1_controller->validate_wholesale_price($request, $this->registered_wholesale_roles, 'variation');

                if (is_a($error, 'WP_Error')) {
                    return $error;
                }

            }

            $response = parent::update_item($request);

            do_action('wwp_after_variation_update_item', $request, $response);

            return $response;

        }

        /**
         * Override the parent method.
         * Check first if variation has wholesale price for it to be deleted.
         *
         * @param WP_REST_Request   $request    Request data.
         *
         * @since 1.12
         * @access public
         * @return array
         */
        public function delete_item($request)
        {

            do_action('wwp_before_variation_delete_item', $request);

            global $wc_wholesale_prices;

            $_REQUEST['request'] = $request;

            // Force Delete Variation
            $request->set_param('force', true);

            $response = parent::delete_item($request);

            do_action('wwp_after_variation_delete_item', $request, $response);

            return $response;

        }

        /**
         * Override the parent method.
         * Bulk create, update and delete items.
         * Note: This function is a copy of function batch_items from WC_REST_Product_Variations_V2_Controller.
         *       This will override the function set in parent (WC_REST_Product_Variations_V2_Controller).
         *       The only code added here is $request->set_route($route), this will be needed to perform create and update.
         *
         * @since  1.16
         * @param WP_REST_Request $request Full details about the request.
         * @return array Of WP_Error or WP_REST_Response.
         */
        public function batch_items($request)
        {

            $items       = array_filter($request->get_params());
            $params      = $request->get_url_params();
            $query       = $request->get_query_params();
            $product_id  = $params['product_id'];
            $body_params = array();

            foreach (array('update', 'create', 'delete') as $batch_type) {
                if (!empty($items[$batch_type])) {
                    $injected_items = array();
                    foreach ($items[$batch_type] as $item) {
                        $injected_items[] = is_array($item) ? array_merge(
                            array(
                                'product_id' => $product_id,
                            ), $item
                        ) : $item;
                    }
                    $body_params[$batch_type] = $injected_items;
                }
            }

            $route   = $request->get_route();
            $request = new WP_REST_Request($request->get_method());
            $request->set_body_params($body_params);
            $request->set_query_params($query);

            // Set the route. This is needed in order to perform create, update wholesale data.
            $request->set_route($route);

            return $this->wwp_rest_wholesale_products_v1_controller->batch_items($request, $this);

        }

        /**
         * Override the parent method.
         * Use function from WWP_REST_Wholesale_Products_V1_Controller
         *
         * @since 1.16
         * @return array
         */
        public function get_fields_for_response($request)
        {

            return $this->wwp_rest_wholesale_products_v1_controller->get_fields_for_response($request, $this->post_type);

        }

    }

}