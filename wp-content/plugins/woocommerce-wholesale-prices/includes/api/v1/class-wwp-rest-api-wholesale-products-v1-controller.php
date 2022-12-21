<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

if (!class_exists('WWP_REST_Wholesale_Products_V1_Controller')) {

    /**
     * REST API Wholesale Products Controller class.
     *
     * @since 1.12
     * @extends WC_REST_Products_Controller
     */
    class WWP_REST_Wholesale_Products_V1_Controller extends WC_REST_Products_Controller
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
        protected $rest_base = 'products';

        /**
         * Post type.
         *
         * @var string
         */
        protected $post_type = 'product';

        /**
         * Wholesale Roles.
         *
         * @var array
         */
        protected $registered_wholesale_roles = array();

        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
         */

        /**
         * WWP_REST_Wholesale_Products_V1_Controller constructor.
         *
         * @since 1.12
         * @access public
         */
        public function __construct()
        {

            global $wc_wholesale_prices;
            $this->registered_wholesale_roles = $wc_wholesale_prices->wwp_wholesale_roles->getAllRegisteredWholesaleRoles();

            // Inherit routes from 'wc/v3' namespace
            $this->register_routes();

            // Filter the query arguments of the request.
            add_filter("woocommerce_rest_{$this->post_type}_object_query", array($this, "query_args"), 10, 2);

            // Include wholesale data into the response
            add_filter("woocommerce_rest_prepare_{$this->post_type}_object", array($this, "add_wholesale_data_on_response"), 10, 3);

            // Fires after a single object is created or updated via the REST API.
            add_action("woocommerce_rest_insert_{$this->post_type}_object", array($this, "create_update_wholesale_product"), 10, 3);

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
         * Overloaded the method, added $post_type parameter.
         * This method is also used in class WWP_REST_Wholesale_Product_Variations_V1_Controller.
         *
         * @param array             $args       Request args.
         * @param WP_REST_Request   $request    Request data.
         * @param string            $post_type  Product post type.
         *
         * @since 1.12
         * @access public
         * @return array
         */
        public function query_args($args, $request, $post_type = 'product')
        {

            // Check if not wholesale endpoint
            if (!$this->is_wholesale_endpoint($request)) {
                return $args;
            }

            $args_copy = (array) $args;

            // Get request role type
            $wholesale_role = isset($request['wholesale_role']) ? sanitize_text_field($request['wholesale_role']) : '';

            // Wholesale Meta Query
            $wholesale_products_meta_query = $this->get_wholesale_products_meta_query($wholesale_role, $args_copy, $request, $post_type);

            if (!isset($args_copy['meta_query'])) {
                $args_copy['meta_query'] = array();
            }

            $args_copy['meta_query'] = array_merge($args_copy['meta_query'], $wholesale_products_meta_query);

            // Wholesale Tax Query
            $wholesale_products_tax_query = $this->get_wholesale_products_tax_query($wholesale_role, $args_copy, $request, $post_type);

            if (!isset($args_copy['tax_query'])) {
                $args_copy['tax_query'] = array();
            }

            $args_copy['tax_query'] = array_merge($args_copy['tax_query'], $wholesale_products_tax_query);

            return apply_filters('wwp_rest_wholesale_' . $post_type . '_query_args', $args_copy, $args, $request, $post_type);

        }

        /**
         * Custom method to set wholesale products meta query.
         *
         * @param string            $wholesale_role
         * @param array             $args_copy
         * @param WP_REST_Request   $request
         * @param string            $post_type
         *
         * @since 1.12
         * @since 1.16  Renamed function to get_wholesale_products_meta_query
         * @since 2.1.5 Add additional filter in condition that will check if general discount is set for the current wholesale user
         * @access public
         * @return array
         */
        public function get_wholesale_products_meta_query($wholesale_role, $args_copy, $request, $post_type)
        {

            $meta_query = array();

            // Only show wholesale products request
            $only_return_wholesale_products = !empty($request['return_wholesale_products']) ? filter_var($request['return_wholesale_products'], FILTER_VALIDATE_BOOLEAN) : false;

            if ( ($only_return_wholesale_products || apply_filters('wwp_only_show_wholesale_products_to_wholesale_users', false, $request)) && apply_filters('wwp_general_discount_is_set', false, $request) === false ) {

                $meta_query = array(
                    'relation' => 'OR',
                    array(
                        'key'     => $wholesale_role . '_have_wholesale_price',
                        'value'   => 'yes',
                        'compare' => '=',
                    ),
                    array(
                        'key'     => $wholesale_role . '_wholesale_price',
                        'value'   => 0,
                        'compare' => '>',
                        'type'    => 'NUMERIC',
                    ),
                );

            }

            return apply_filters('wwp_rest_wholesale_' . $post_type . '_meta_query', $meta_query, $wholesale_role, $args_copy, $request);

        }

        /**
         * Custom method to set wholesale products taxonomy query.
         *
         * @param string            $wholesale_role
         * @param array             $args_copy
         * @param WP_REST_Request   $request
         * @param string            $post_type
         *
         * @since 1.16
         * @access public
         * @return array
         */
        public function get_wholesale_products_tax_query($wholesale_role, $args_copy, $request, $post_type)
        {

            $tax_query = array();

            return apply_filters('wwp_rest_wholesale_' . $post_type . '_tax_query', $tax_query, $wholesale_role, $args_copy, $request);

        }

        /**
         * Custom method to modify the response to include WWP wholesale data.
         *
         * @param WP_REST_Response         $response
         * @param WC_Product              $object
         * @param WP_REST_Request         $request
         *
         * @since 1.12
         *
         * @access public
         * @return array
         */
        public function add_wholesale_data_on_response($response, $object, $request)
        {

            do_action('wwp_before_adding_wholesale_data_on_response', $response, $object, $request);

            // Check if not wholesale endpoint
            if (!$this->is_wholesale_endpoint($request)) {
                return $response;
            }

            // Add wholesale data. Add also WWPP meta data.
            $response->data['wholesale_data'] = $this->get_wwp_meta_data($object, $request);

            // Remove WWPP meta in meta data
            // Only show meta_data if 'show_meta_data=true' is provided in the request parameter else hide it by default.
            if (isset($request['show_meta_data']) && filter_var($request['show_meta_data'], FILTER_VALIDATE_BOOLEAN) === true) {
                $response->data['meta_data'] = $this->remove_wwpp_meta($response->data['meta_data']);
            } else {
                unset($response->data['meta_data']);
            }

            // Only show categories if 'show_categories=true' is provided in the api request else hide by default.
            if (!isset($request['show_categories']) || (isset($request['show_categories']) && filter_var($request['show_categories'], FILTER_VALIDATE_BOOLEAN) === false)) {
                unset($response->data['categories']);
            }

            // Remove links in response
            $links = $response->get_links();
            if (!empty($links)) {
                foreach ($links as $key => $link) {
                    $response->remove_link($key);
                }
            }

            return apply_filters("wwp_rest_response_{$this->post_type}_object", $response, $object, $request);

        }

        /**
         * Custom method to get wholesale meta data.
         *
         * @param WC_Product        $product
         * @param WP_REST_Request   $request
         *
         * @since 1.12
         * @access public
         * @return array
         */
        public function get_wwp_meta_data($product, $request)
        {

            $product_id = $product->get_id();

            $meta_data = array(
                'wholesale_price' => array(),
            );

            // Get formatted Wholesale Price
            if (isset($request['wholesale_role'])) {

                global $wc_wholesale_prices;

                $wwp_wholesale_prices_instance = new WWP_Wholesale_Prices(array());
                $wholesale_role                = sanitize_text_field($request['wholesale_role']);

                $meta_data['price_html'] = $wwp_wholesale_prices_instance->wholesale_price_html_filter($product->get_price_html(), $product, array($wholesale_role));

            }

            foreach ($this->registered_wholesale_roles as $role => $data) {

                $wholesale_price = get_post_meta($product_id, $role . '_wholesale_price', true);

                if (!empty($wholesale_price)) {
                    $meta_data['wholesale_price'] = array_merge($meta_data['wholesale_price'], array($role => $wholesale_price));
                }

            }

            return apply_filters('wwp_meta_data', array_filter($meta_data), $product, $request);

        }

        /**
         * Custom method to unset WWP and WWPP meta in meta_data property.
         * WWPP meta will be transfered to its own property called wholesale_data.
         *
         * @param array         $meta_data
         *
         * @since 1.12
         * @access public
         * @return array
         */
        public function remove_wwpp_meta($meta_data)
        {

            $meta_to_remove = apply_filters('wwp_meta_to_hide_from_response', array(
                'wwpp_ignore_cat_level_wholesale_discount',
                'wwpp_ignore_role_level_wholesale_discount',
                'wwpp_post_meta_quantity_discount_rule_mapping',
                'wwpp_product_wholesale_visibility_filter',
                'wwpp_post_meta_enable_quantity_discount_rule',
            ), $meta_data);

            $new_meta_data = $meta_data;

            if (!empty($new_meta_data)) {

                foreach ($new_meta_data as $key => $data) {

                    if (in_array($data->key, $meta_to_remove)) {
                        unset($new_meta_data[$key]);
                    } else if (strpos($data->key, '_wholesale_price') !== false ||
                        strpos($data->key, '_have_wholesale_price') !== false ||
                        strpos($data->key, '_wholesale_minimum_order_quantity') !== false ||
                        strpos($data->key, '_wholesale_order_quantity_step') !== false) {
                        unset($new_meta_data[$key]);
                    }

                }

            }

            return apply_filters('remove_wwpp_meta', array_values($new_meta_data), $meta_data);

        }

        /**
         * Custom method to check if the request coming from wholesale endpoint.
         *
         * @param WP_REST_Request $request
         *
         * @since 1.12
         * @access public
         * @return bool
         */
        public static function is_wholesale_endpoint($request)
        {

            return apply_filters('wwp_is_wholesale_endpoint', is_a($request, 'WP_REST_Request') && strpos($request->get_route(), 'wholesale/v1') !== false ? true : false, $request);

        }

        /**
         * Custom method to create or update product wholesale data.
         * Fires after a single object is created or updated via the REST API.
         *
         * @param WC_Product              $product
         * @param WP_REST_Request         $request
         * @param Boolean                 $create_product     True is creating, False is updating
         *
         * @since 1.12
         * @access public
         */
        public function create_update_wholesale_product($product, $request, $create_product)
        {

            do_action('wwp_before_create_update_wholesale_product', $product, $request, $create_product);

            // Import variables into the current symbol table from an array
            extract($request->get_params());

            // Get product type
            $product_type = WWP_Helper_Functions::wwp_get_product_type($product);

            // The product id
            $product_id = $product->get_id();

            // Product types to check
            $product_types = apply_filters('wwp_create_update_product_types', array('simple', 'variation', 'bundle', 'composite'));

            // Check if wholesale price is set
            if (isset($wholesale_price) && in_array($product_type, $product_types)) {

                // Multiple wholesale price is set
                if (is_array($wholesale_price)) {

                    foreach ($wholesale_price as $role => $price) {

                        // Validate if wholesale role exist
                        if (is_numeric($price) && array_key_exists($role, $this->registered_wholesale_roles)) {

                            update_post_meta($product_id, $role . '_wholesale_price', $price);
                            update_post_meta($product_id, $role . '_have_wholesale_price', 'yes');

                        }

                        // If user updates the wholesale and if its empty still do update the meta
                        if (!$create_product && empty($price)) {
                            update_post_meta($product_id, $role . '_wholesale_price', $price);
                        }

                    }

                }

            }

            do_action('wwp_after_create_update_wholesale_product', $product, $request, $create_product);

        }

        /**
         * Custom method that checks if wholesale price is valid
         *
         * @param WP_REST_Request       $request
         * @param array                 $registered_wholesale_roles
         *
         * @since 1.16
         * @access public
         * @return bool|WP_Error
         */
        public static function validate_wholesale_price($request, $registered_wholesale_roles, $type = 'product')
        {

            $method = $request->get_method();
            switch ($method) {
                case 'PUT':
                    $method = 'update';
                    break;
                case 'POST':
                    $method = 'create';
                    break;
            }

            if (isset($request['wholesale_price']) && is_array($request['wholesale_price'])) {

                $invalid_roles           = array();
                $invalid_wholesale_price = array();

                foreach ($request['wholesale_price'] as $role => $wholesale_price) {
                    if (!array_key_exists($role, $registered_wholesale_roles)) {
                        $invalid_roles[] = $role;
                    }

                    if (!is_numeric($wholesale_price)) {
                        $invalid_wholesale_price[$role] = $wholesale_price;
                    }
                }

                if (!empty($invalid_roles) || !empty($invalid_wholesale_price)) {
                    return new WP_Error('wholesale_rest_' . $type . '_cannot_' . $method, sprintf(__('Unable to %s. Invalid wholesale price.', 'woocommerce-wholesale-prices'), $method), array('status' => 400, 'invalid_roles' => $invalid_roles, 'invalid_wholesale_price' => $invalid_wholesale_price));
                } else {
                    return true; // This is a valid parameter
                }

            }

            return new WP_Error('wholesale_rest_' . $type . '_cannot_' . $method, sprintf(__('Unable to %s. Invalid wholesale price.', 'woocommerce-wholesale-prices'), $method), array('status' => 400));

        }

        /**
         * Override the parent method.
         * Added validation for wholesale role.
         *
         * @param WP_REST_Request   $request
         *
         * @since 1.12
         * @access public
         * @return WP_REST_Response|WP_Error
         */
        public function get_items($request)
        {

            do_action('wwp_before_product_get_items', $request);

            $wholesale_role = isset($request['wholesale_role']) ? sanitize_text_field($request['wholesale_role']) : '';

            if (!empty($wholesale_role) && !isset($this->registered_wholesale_roles[$wholesale_role])) {
                return new WP_Error('wholesale_rest_product_cannot_view', __('Invalid wholesale role.', 'woocommerce-wholesale-prices'), array('status' => 400));
            }

            $response = parent::get_items($request);

            do_action('wwp_after_product_get_items', $request, $response);

            return $response;

        }

        /**
         * Override the parent method.
         * Added validation.
         *
         * @param WP_REST_Request         $request
         *
         * @since 1.12
         * @access public
         * @return WP_REST_Response|WP_Error
         */
        public function get_item($request)
        {

            $extra_checks = apply_filters("wwp_before_get_item_{$this->post_type}_extra_check", array('is_valid' => true, 'message' => ''), $request);

            if (isset($extra_checks['is_valid']) && !$extra_checks['is_valid']) {
                return $extra_checks['message'];
            }

            do_action('wwp_before_product_get_item', $request);

            $wholesale_role = isset($request['wholesale_role']) ? sanitize_text_field($request['wholesale_role']) : '';

            if (!empty($wholesale_role) && !isset($this->registered_wholesale_roles[$wholesale_role])) {
                return new WP_Error('wholesale_rest_product_cannot_view', __('Invalid wholesale role.', 'woocommerce-wholesale-prices'), array('status' => 400));
            }

            $only_return_wholesale_products = !empty($request['return_wholesale_products']) ? filter_var($request['return_wholesale_products'], FILTER_VALIDATE_BOOLEAN) : false;

            // Skip checking if wholesale product when general discount is set for this current wholesale role
            // If wholesale_customer is set and if "only_return_wholesale_products" parameter is true OR WWPP "Only show wholesale products to wholesale users" option is enabled then proceed checking if valid wholesale product
            if (apply_filters('wwp_general_discount_is_set', false, $request) === false && ($only_return_wholesale_products || apply_filters('wwp_only_show_wholesale_products_to_wholesale_users', false, $request))) {

                if (empty($wholesale_role)) {
                    return new WP_Error('wholesale_rest_product_cannot_view', __('Cannot view, please provide wholesale_role parameter.', 'woocommerce-wholesale-prices'), array('status' => 400));
                }

                $product              = wc_get_product($request['id']);
                $wholesale_data       = WWP_Wholesale_Prices::get_product_wholesale_price_on_shop_v3($request['id'], array($wholesale_role));
                $wholesale_variations = get_post_meta($request['id'], $wholesale_role . '_variations_with_wholesale_price');

                // If not variable type and wholesale price is empty return error
                // If variable type and no wholesale variation then return error
                if (
                    (!$product->is_type('variable') && isset($wholesale_data['wholesale_price']) && empty($wholesale_data['wholesale_price']))
                    ||
                    ($product->is_type('variable') && empty($wholesale_variations))
                ) {
                    return new WP_Error('wholesale_rest_cannot_view', __('Not a wholesale product.', 'woocommerce-wholesale-prices'), array('status' => rest_authorization_required_code()));
                }

            }

            $response = parent::get_item($request);

            do_action('wwp_after_product_get_item', $request, $response);

            return $response;

        }

        /**
         * Override the parent method.
         * Added validation.
         *
         * @param WP_REST_Request         $request
         *
         * @since 1.12
         * @access public
         * @return WP_REST_Response|WP_Error
         */
        public function create_item($request)
        {

            do_action('wwp_before_product_create_item', $request);

            if (isset($request['wholesale_price']) && $request['type'] != 'variable') {

                // Check if wholesale price is set. Make wholesale price as the basis to create wholesale product.
                $error = self::validate_wholesale_price($request, $this->registered_wholesale_roles);

                if (is_a($error, 'WP_Error')) {
                    return $error;
                }

            }

            $response = parent::create_item($request);

            do_action('wwp_after_product_create_item', $request, $response);

            return $response;

        }

        /**
         * Override the parent method.
         * Added validation.
         *
         * @param WP_REST_Request   $request
         *
         * @since 1.16
         * @access public
         * @return WP_REST_Response|WP_Error
         */
        public function update_item($request)
        {

            do_action('wwp_before_product_update_item', $request);

            if (isset($request['wholesale_price'])) {

                // Check if wholesale price is set. Make wholesale price as the basis to create wholesale product.
                $error = self::validate_wholesale_price($request, $this->registered_wholesale_roles);

                if (is_a($error, 'WP_Error')) {
                    return $error;

                }
            }

            $response = parent::update_item($request);

            do_action('wwp_after_product_update_item', $request, $response);

            return $response;

        }

        /**
         * Override the parent method.
         *
         * @param  WP_REST_Request $request Full details about the request.
         *
         * @since 1.12.0
         * @access public
         * @return bool|WP_Error
         */
        public function delete_item($request)
        {

            do_action('wwp_before_product_delete_item', $request);

            $response = parent::delete_item($request);

            do_action('wwp_after_product_delete_item', $request, $response);

            return $response;

        }

        /**
         * Override the parent method.
         * Bulk create, update and delete items.
         * Note: This function is a copy of function batch_items from WC_REST_Controller.
         *       This will override the function set in parent (WC_REST_Controller).
         *       The only code added here is $_item->set_route($request->get_route()), this will be needed to perform create and update.
         *       Also added a code in update and delete that passes the query parmeters in order to trigger our custom parameters
         *       like show_meta_data, show_categories, and return_wholesale_products. This is the code added $_item->set_query_params($query);
         *
         * @since  1.16
         * @param WP_REST_Request   $request        Full details about the request.
         * @param Object            $controller     WWP_REST_Wholesale_Products_V1_Controller|WWP_REST_Wholesale_Product_Variations_V1_Controller.
         * @return array Of WP_Error or WP_REST_Response.
         */
        public function batch_items($request, $controller = null)
        {

            if (is_null($controller)) {
                $controller = $this;
            }

            do_action('wwp_before_product_batch_update', $request);

            /**
             * REST Server
             *
             * @var WP_REST_Server $wp_rest_server
             */
            global $wp_rest_server;

            // Get the request params.
            $items    = array_filter($request->get_params());
            $query    = $request->get_query_params();
            $response = array();

            // Check batch limit.
            $limit = $controller->check_batch_limit($items);
            if (is_wp_error($limit)) {
                return $limit;
            }

            if (!empty($items['create'])) {
                foreach ($items['create'] as $item) {
                    $_item = new WP_REST_Request('POST');

                    // Set the route. This is needed in order to perform create, update wholesale data.
                    $_item->set_route($request->get_route());

                    // Default parameters.
                    $defaults = array();
                    $schema   = $controller->get_public_item_schema();
                    foreach ($schema['properties'] as $arg => $options) {
                        if (isset($options['default'])) {
                            $defaults[$arg] = $options['default'];
                        }
                    }
                    $_item->set_default_params($defaults);

                    // Set request parameters.
                    $_item->set_body_params($item);

                    // Set query (GET) parameters.
                    $_item->set_query_params($query);

                    // Create item
                    $_response = $controller->create_item($_item);

                    if (is_wp_error($_response)) {
                        $response['create'][] = array(
                            'id'    => 0,
                            'error' => array(
                                'code'    => $_response->get_error_code(),
                                'message' => $_response->get_error_message(),
                                'data'    => $_response->get_error_data(),
                            ),
                        );
                    } else {
                        $response['create'][] = $wp_rest_server->response_to_data($_response, '');
                    }
                }
            }

            if (!empty($items['update'])) {
                foreach ($items['update'] as $item) {
                    $_item = new WP_REST_Request('PUT');

                    // Set the route. This is needed in order to perform create, update wholesale data.
                    $_item->set_route($request->get_route());

                    // Set query (GET) parameters.
                    $_item->set_query_params($query);

                    // Set request parameters.
                    $_item->set_body_params($item);

                    // Update item
                    $_response = $controller->update_item($_item);

                    if (is_wp_error($_response)) {
                        $response['update'][] = array(
                            'id'    => $item['id'],
                            'error' => array(
                                'code'    => $_response->get_error_code(),
                                'message' => $_response->get_error_message(),
                                'data'    => $_response->get_error_data(),
                            ),
                        );
                    } else {
                        $response['update'][] = $wp_rest_server->response_to_data($_response, '');
                    }
                }
            }

            if (!empty($items['delete'])) {
                foreach ($items['delete'] as $id) {
                    $id = (int) $id;

                    if (0 === $id) {
                        continue;
                    }

                    $_item = new WP_REST_Request('DELETE');

                    // Set the route. This is needed in order to perform create, update wholesale data.
                    $_item->set_route($request->get_route());

                    // Set query (GET) parameters.
                    $_item->set_query_params(
                        array_merge(
                            array(
                                'id'    => $id,
                                'force' => true,
                            ),
                            $query
                        )
                    );

                    // Delete item
                    $_response = $controller->delete_item($_item);

                    if (is_wp_error($_response)) {
                        $response['delete'][] = array(
                            'id'    => $id,
                            'error' => array(
                                'code'    => $_response->get_error_code(),
                                'message' => $_response->get_error_message(),
                                'data'    => $_response->get_error_data(),
                            ),
                        );
                    } else {
                        $response['delete'][] = $wp_rest_server->response_to_data($_response, '');
                    }
                }
            }

            do_action('wwp_after_product_batch_update', $request, $response);

            return $response;

        }

        /**
         * Override the parent method.
         * Add wholesale product query params.
         *
         * @since 1.16
         * @return array
         */
        public function get_collection_params()
        {

            $wholesale_price_properties = array();

            foreach ($this->registered_wholesale_roles as $role => $data) {
                $wholesale_price_properties[$role] = array(
                    'description'       => sprintf(__('Wholesale price for %s', 'woocommerce-wholesale-prices'), $data['roleName']),
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                );
            }

            $params = array(
                'wholesale_price' => array(
                    'description' => __('The product wholesale price.', 'woocommerce-wholesale-prices'),
                    'type'        => 'object',
                    'properties'  => $wholesale_price_properties,
                ),
            );

            $params = array_merge(parent::get_collection_params(), $params);

            return apply_filters('wwp_rest_wholesale_product_get_collection_params', $params, $this);

        }

        /**
         * Override the parent method.
         * Option to filter properties to return.
         * This useful if we only return specific fields in the response.
         *
         * @since 1.16
         * @return array
         */
        public function get_fields_for_response($request, $post_type = 'product')
        {
            $properties = parent::get_fields_for_response($request);

            return apply_filters("wwp_rest_wholesale_{$post_type}_properties", $properties, $request);

        }

    }

}