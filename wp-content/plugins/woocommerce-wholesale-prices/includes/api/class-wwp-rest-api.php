<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

// Private REST route. We wont be including this in the doc. This is only used internally for this plugin.
require_once WWP_INCLUDES_PATH . 'api/v1/class-wwp-rest-api-keys-v1-controller.php';

if (!class_exists('WWP_REST_API')) {

    /**
     * Model that houses the logic of WWPP API.
     *
     * @since 1.12
     */
    class WWP_REST_API
    {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
         */

        /**
         * Property that holds the single main instance of WWP_REST_API.
         *
         * @since 1.12.0
         * @access private
         * @var WWP_REST_API
         */
        private static $_instance;

        /**
         * Property that holds WWP API Controllers.
         *
         * @since 1.12.0
         */
        public $wwp_rest_api_wholesale_products_controller;
        public $wwp_rest_api_wholesale_variations_controller;
        public $wwp_rest_api_wholesale_roles_controller;
        public $wwp_rest_api_keys_controller;

        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
         */

        /**
         * WWP_REST_API constructor.
         *
         * @since 1.12.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_REST_API model.
         */
        public function __construct($dependencies = array())
        {

            add_action('woocommerce_loaded', array($this, 'load_wwp_api'), 10);

        }

        /**
         * Load WWP API.
         *
         * @since 1.16.0
         * @access public
         */
        public function load_wwp_api()
        {

            // API controllers using wholesale/v1 namespace
            add_action('rest_api_init', array($this, 'load_api_wwp_controllers'), 5);

            // Authenticate users if api keys are provided
            add_action('woocommerce_rest_is_request_to_rest_api', array($this, 'authenticate_user'));

        }

        /**
         * Ensure that only one instance of WWP_REST_API is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.12.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_REST_API model.
         * @return WWP_REST_API
         */
        public static function instance($dependencies = array())
        {

            if (!self::$_instance instanceof self) {
                self::$_instance = new self($dependencies);
            }

            return self::$_instance;

        }

        /**
         * WWPP API controllers under wwpp/v1 namespace.
         *
         * @since 1.12
         * @access public
         */
        public function load_api_wwp_controllers()
        {

            require_once WWP_INCLUDES_PATH . 'api/v1/class-wwp-rest-api-wholesale-products-v1-controller.php';
            require_once WWP_INCLUDES_PATH . 'api/v1/class-wwp-rest-api-wholesale-products-variations-v1-controller.php';
            require_once WWP_INCLUDES_PATH . 'api/v1/class-wwp-rest-api-wholesale-roles-v1-controller.php';

            $this->wwp_rest_api_wholesale_products_controller   = new WWP_REST_Wholesale_Products_V1_Controller;
            $this->wwp_rest_api_wholesale_variations_controller = new WWP_REST_Wholesale_Product_Variations_V1_Controller;
            $this->wwp_rest_api_wholesale_roles_controller      = new WWP_REST_Wholesale_Roles_V1_Controller;

        }

        /**
         * Authenticate if user if using WWPP rest base if api keys are provided.
         *
         * @since 1.12
         * @access public
         *
         * @param bool $rest_request
         * @return boolean
         */
        public function authenticate_user($rest_request)
        {

            $rest_prefix = trailingslashit(rest_get_url_prefix());
            $request_uri = esc_url_raw(wp_unslash($_SERVER['REQUEST_URI']));

            // Authenticate if the request is using the wholesale/v1 endpoint.
            if ((false !== strpos($request_uri, $rest_prefix . 'wholesale/'))) {
                return true;
            }

            return $rest_request;

        }

    }

}
