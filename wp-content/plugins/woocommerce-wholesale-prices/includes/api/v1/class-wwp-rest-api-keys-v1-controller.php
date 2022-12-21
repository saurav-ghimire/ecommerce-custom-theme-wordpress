<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

if (!class_exists('WWP_REST_API_Keys')) {

    /**
     * WWP REST API Keys Controller class.
     *
     * @since 1.16.1
     * @extends WC_REST_Products_Controller
     */
    class WWP_REST_API_Keys
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
        protected $rest_base = 'api-keys';

        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
         */

        /**
         * WWP_REST_Wholesale_Products_V1_Controller constructor.
         *
         * @since 1.16.1
         * @access public
         */
        public function __construct()
        {

            add_action('rest_api_init', array($this, 'register_routes'));

        }

        /**
         * REST API register routes.
         *
         * @since 1.16.1
         * @access public
         *
         * @param WP_REST_Request $request
         */
        public function register_routes($request)
        {

            register_rest_route(
                $this->namespace,
                '/' . $this->rest_base,
                array(
                    array(
                        'methods'             => WP_REST_Server::READABLE,
                        'callback'            => array($this, 'get_key'),
                        'permission_callback' => array($this, 'permissions_check'),
                    ),
                    array(
                        'methods'             => WP_REST_Server::CREATABLE,
                        'callback'            => array($this, 'create_update_key'),
                        'permission_callback' => array($this, 'permissions_check'),
                    ),
                    array(
                        'methods'             => WP_REST_Server::EDITABLE,
                        'callback'            => array($this, 'create_update_key'),
                        'permission_callback' => array($this, 'permissions_check'),
                    ),
                    array(
                        'methods'             => WP_REST_Server::DELETABLE,
                        'callback'            => array($this, 'revoke_key'),
                        'permission_callback' => array($this, 'permissions_check'),
                    ),
                    'schema' => null,
                )
            );

        }

        /**
         * REST API permission check
         *
         * @since 1.16.1
         * @param  WP_REST_Request
         * @return WP_Error|boolean
         */
        public function permissions_check($request)
        {

            // Bypass wp cookie auth
            if (defined('WWP_DEV') && WWP_DEV) {
                return true;
            }

            // Grant permission if admin or shop manager
            if (current_user_can('administrator') || current_user_can('manage_woocommerce')) {
                return true;

            }

            return new WP_Error('rest_cannot_view', __('Invalid Request.', 'woocommerce-wholesale-prices'), array('status' => rest_authorization_required_code()));

        }

        /**
         * Get API Keys.
         *
         * @since 1.16.1
         * @param WP_REST_Request $request Full details about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function get_key($request)
        {

            if (self::is_api_key_valid()) {

                return rest_ensure_response(self::get_keys());

            } else {

                $data = array(
                    'status'  => 'fail',
                    'message' => __('Invalid API keys.', 'woocommerce-wholesale-prices'),
                );

                rest_ensure_response($data);

            }

        }

        /**
         * Create or Update API Keys.
         *
         * @since 1.16.1
         * @param WP_REST_Request $request Full details about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function create_update_key($request)
        {

            $option_key    = $request['api_option_key'];
            $option_secret = $request['api_option_secret'];

            if (isset($request['data']) && isset($request['data']['consumer_key'])) {

                update_option('wwp_woocommerce_api_consumer_key', sanitize_text_field($request['data']['consumer_key']));
                update_option('wwp_woocommerce_api_consumer_secret', sanitize_text_field($request['data']['consumer_secret']));

                $data = array(
                    'status'  => 'success',
                    'message' => __('API keys updated.', 'woocommerce-wholesale-prices'),
                );

            } else {

                $data = array(
                    'status'  => 'fail',
                    'message' => __('Unable to update API Keys.', 'woocommerce-wholesale-prices'),
                );

            }

            $response = rest_ensure_response($data);

            return $response;

        }

        /**
         * Delete API Keys.
         *
         * @since 1.16.1
         * @param WP_REST_Request $request Full details about the request.
         * @return WP_Error|WP_REST_Response
         */
        public function revoke_key($request)
        {

            global $wpdb;

            if (isset($request['secret'])) { // WPCS: input var okay, CSRF ok.
                $secret = sanitize_text_field($request['secret']);
                $result = $wpdb->get_results($wpdb->prepare("SELECT key_id, user_id FROM {$wpdb->prefix}woocommerce_api_keys WHERE consumer_secret = %s", $secret));

                if (!empty($result)) {
                    $key_id  = $result[0]->key_id;
                    $user_id = $result[0]->user_id;

                    if ($key_id && $user_id && (current_user_can('edit_user') || get_current_user_id() === $user_id)) {
                        if ($this->remove_key($key_id)) {
                            $data = array(
                                'status'  => 'success',
                                'message' => __('Successfully revoked API Key.', 'woocommerce-wholesale-prices'),
                            );
                        }
                    }
                }

            }

            if (!isset($data)) {
                $data = array(
                    'status'  => 'fail',
                    'message' => __('You do not have permission to revoke this API Key', 'woocommerce-wholesale-prices'),
                );
            }

            $response = rest_ensure_response($data);

            return $response;

        }

        /**
         * WC API Keys.
         *
         * @since 1.16.1
         * @return array
         */
        public static function get_keys()
        {
            return array(
                'consumer_key'    => get_option('wwp_woocommerce_api_consumer_key'),
                'consumer_secret' => get_option('wwp_woocommerce_api_consumer_secret'),
            );
        }

        /**
         * Remove key.
         *
         * @param  int $key_id API Key ID.
         * @since 1.16.1
         * @return bool
         */
        private function remove_key($key_id)
        {
            global $wpdb;

            $delete = $wpdb->delete($wpdb->prefix . 'woocommerce_api_keys', array('key_id' => $key_id), array('%d'));

            return $delete;
        }

        /**
         * Validate API Keys saved in WWP.
         *
         * @since 1.16.1
         * @return bool
         */
        public static function is_api_key_valid()
        {
            global $wpdb;

            $api_keys = self::get_keys();

            if (strlen($api_keys['consumer_key']) != 43 || strlen($api_keys['consumer_secret']) != 43) {
                return false;
            }

            if (!empty($api_keys['consumer_key']) && !empty($api_keys['consumer_secret'])) {

                // Check if consumer secret exist in db
                $tuncated_key = $wpdb->get_var($wpdb->prepare("SELECT truncated_key FROM {$wpdb->prefix}woocommerce_api_keys WHERE consumer_secret = %s", $api_keys['consumer_secret']));

                // Check if consumer key match with the save option
                if (strpos($api_keys['consumer_key'], $tuncated_key) !== false) {
                    return true;
                }

            }

            return false;

        }

    }

    return new WWP_REST_API_Keys;

}
