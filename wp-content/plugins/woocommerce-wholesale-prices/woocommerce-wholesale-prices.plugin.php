<?php
if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

require_once 'includes/class-wwp-aelia-currency-switcher-integration-helper.php';
require_once 'includes/class-wwp-wholesale-roles.php';

require_once 'includes/class-wwp-bootstrap.php';
require_once 'includes/class-wwp-script-loader.php';
require_once 'includes/admin-custom-fields/products/class-wwp-admin-custom-fields-simple-product.php';
require_once 'includes/admin-custom-fields/products/class-wwp-admin-custom-fields-variable-product.php';
require_once 'includes/class-wwp-products-cpt.php';
require_once 'includes/class-wwp-wholesale-prices.php';
require_once 'includes/class-wwp-order.php';
require_once 'includes/class-wwp-duplicate-product.php';
require_once 'includes/class-wwp-marketing.php';
require_once 'includes/class-wwp-wholesale-roles-admin-page.php';
require_once 'includes/class-wwp-import-export.php';
require_once 'includes/class-wwp-product-visibility.php';
require_once 'includes/class-wwp-lead-capture.php';
require_once 'includes/class-wwp-order-form.php';
require_once 'includes/class-wwp-usage.php';
require_once 'includes/class-wwp-about-page.php';
require_once 'includes/class-wwp-help-page.php';
require_once 'includes/class-wwp-advanced-coupons-page.php';
require_once 'includes/class-wwp-upgrade-to-premium.php';

require_once 'includes/class-wwp-admin-menu.php';
require_once 'includes/class-wwp-dashboard.php';
require_once 'includes/class-wwp-wholesale-price-for-non-wholesale-customers.php';
require_once 'includes/class-wwp-wholesale-price-grouped-product.php';
require_once 'includes/api/class-wwp-rest-api-client.php';

// WC Admin Notes
require_once 'includes/wc-admin-notes/class-wwp-install-acfw.php';
require_once 'includes/wc-admin-notes/class-wwp-wws-bundle.php';
require_once 'includes/wc-admin-notes/class-wwp-store-owner-tips-fb.php';
require_once 'includes/wc-admin-notes/class-wwp-wws-youtube.php';
require_once 'includes/wc-admin-notes/class-wwp-wws-review.php';

// WWP Admin Notice Bar Lite
require_once 'includes/class-wwp-notice-bar.php';

// REST API
require_once 'includes/api/class-wwp-rest-api.php';

// Compatibility
require_once 'includes/compatibility/class-wwp-wpml-compatibility.php';

// License Manager
require_once 'includes/class-wwp-wws-license-manager.php';

/**
 * This is the main plugin class. It's purpose generally is for "ALL PLUGIN RELATED STUFF ONLY".
 * This file or class may also serve as a controller to some degree but most if not all business logic is distributed
 * across include files.
 *
 * Class WooCommerceWholeSalePrices
 */
class WooCommerceWholeSalePrices
{

    /*
    |------------------------------------------------------------------------------------------------------------------
    | Class Members
    |------------------------------------------------------------------------------------------------------------------
     */

    private static $_instance;

    public $wwp_wholesale_roles;
    private $_wwp_wholesale_prices;

    public $wwp_bootstrap;
    public $wwp_script_loader;
    public $wwp_admin_custom_fields_simple_product;
    public $wwp_admin_custom_fields_variable_product;
    public $wwp_products_cpt;
    public $wwp_wholesale_prices;
    public $wwp_order;
    public $wwp_duplicate_product;
    public $wwp_marketing;
    public $wwp_wholesale_roles_admin_page;
    public $wwp_import_export;
    public $wwp_rest_api;
    public $wwp_product_visibility;
    public $wwp_lead_capture;
    public $wwp_order_form;
    public $wwp_usage;
    public $wwp_for_non_wholesale_customer;
    public $wwp_wholesale_price_grouped_product;
    public $wwp_rest_api_client;
    public $wwp_admin_menu;
    public $wwp_dashboard;
    public $wwp_about_page;
    public $wwp_help_page;
    public $wwp_advanced_coupons_page;
    public $wwp_upgrade_to_premium_page;
    public $wwp_admin_notice_bar;
    public $wwp_wws_license_manager;

    const VERSION = '2.1.5.1';

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * WooCommerceWholeSalePrices constructor.
     *
     * @since 1.0.0
     * @since 1.14.0
     * @access public
     */
    public function __construct()
    {

        $this->wwp_wholesale_roles = WWP_Wholesale_Roles::getInstance();

        $this->wwp_rest_api_client = WWP_Rest_API_Client::instance(array());

        $this->wwp_for_non_wholesale_customer           = WWP_Wholesale_Prices_For_Non_Wholesale_Customers::instance(array('WWP_Wholesale_Roles' => $this->wwp_wholesale_roles, 'WWP_Rest_API_Client' => $this->wwp_rest_api_client));
        $this->wwp_wholesale_prices                     = WWP_Wholesale_Prices::instance(array('WWP_Wholesale_Roles' => $this->wwp_wholesale_roles));
        $this->wwp_wholesale_price_grouped_product      = WWP_Wholesale_Price_Grouped_Product::instance(array('WWP_Wholesale_Roles' => $this->wwp_wholesale_roles));
        $this->wwp_bootstrap                            = WWP_Bootstrap::instance(array('WWP_Wholesale_Roles' => $this->wwp_wholesale_roles, 'WWP_CURRENT_VERSION' => self::VERSION, 'WWP_Wholesale_Prices_For_Non_Wholesale_Customers' => $this->wwp_for_non_wholesale_customer));
        $this->wwp_script_loader                        = WWP_Script_Loader::instance(array('WWP_Wholesale_Roles' => $this->wwp_wholesale_roles, 'WWP_Wholesale_Prices' => $this->wwp_wholesale_prices, 'WWP_CURRENT_VERSION' => self::VERSION));
        $this->wwp_admin_custom_fields_simple_product   = WWP_Admin_Custom_Fields_Simple_Product::instance(array('WWP_Wholesale_Roles' => $this->wwp_wholesale_roles));
        $this->wwp_admin_custom_fields_variable_product = WWP_Admin_Custom_Fields_Variable_Product::instance(array('WWP_Wholesale_Roles' => $this->wwp_wholesale_roles));
        $this->wwp_products_cpt                         = WWP_Products_CPT::instance(array(
            'WWP_Wholesale_Roles'  => $this->wwp_wholesale_roles,
            'WWP_Wholesale_Prices' => $this->wwp_wholesale_prices,
        ));
        $this->wwp_order                      = WWP_Order::instance(array('WWP_Wholesale_Roles' => $this->wwp_wholesale_roles));
        $this->wwp_duplicate_product          = WWP_Duplicate_Product::instance(array('WWP_Wholesale_Roles' => $this->wwp_wholesale_roles));
        $this->wwp_marketing                  = WWP_Marketing::instance(array('WWP_Wholesale_Roles' => $this->wwp_wholesale_roles));
        $this->wwp_wholesale_roles_admin_page = WWP_Wholesale_Roles_Admin_Page::instance(array('WWP_Wholesale_Roles' => $this->wwp_wholesale_roles));
        $this->wwp_import_export              = WWP_Import_export::instance(array('WWP_Wholesale_Roles' => $this->wwp_wholesale_roles));
        $this->wwp_wpml_compatibility         = WWP_WPML_Compatibility::instance();
        $this->wwp_product_visibility         = WWP_Product_Visibility::instance(array('WWP_Wholesale_Roles' => $this->wwp_wholesale_roles));
        $this->wwp_lead_capture               = WWP_Lead_Capture::instance();
        $this->wwp_order_form                 = WWP_Order_Form::instance();
        $this->wwp_usage                      = WWP_Usage::instance();
        $this->wwp_admin_menu                 = WWP_Admin_Menu::instance(array());
        $this->wwp_dashboard                  = WWP_Dashboard::instance(array('WWP_Wholesale_Roles' => $this->wwp_wholesale_roles));
        $this->wwp_about_page                 = WWP_About_Page::instance();
        $this->wwp_help_page                  = WWP_Help_Page::instance();
        $this->wwp_advanced_coupons_page      = WWP_Advanced_Coupons_Page::instance();
        $this->wwp_upgrade_to_premium_page    = WWP_Upgrade_To_Premium_Page::instance();
        $this->wwp_admin_notice_bar           = WWP_Notice_Bar::instance(array());
        $this->wwp_wws_license_manager        = WWP_WWS_License_Manager::instance();

        // REST API
        $this->wwp_rest_api = WWP_REST_API::instance(array());

    }

    /**
     * Singleton Pattern.
     * Ensure that only one instance of WooCommerceWholeSalePrices is loaded or can be loaded (Singleton Pattern).
     *
     * @since 1.0.0
     * @since 1.14.0
     * @access public
     *
     * @return WooCommerceWholeSalePrices
     */
    public static function instance()
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    // @deprecated Will be remove on future versions
    public static function getInstance()
    {
        return self::instance();
    }

    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Plugin Settings
    |-------------------------------------------------------------------------------------------------------------------
     */

    /**
     * Activate plugin settings.
     *
     * @since 1.0.0
     * @since 1.14.0 Refactor codebase.
     * @access public
     */
    public function activate_plugin_settings()
    {

        add_filter("woocommerce_get_settings_pages", array($this, 'initialize_plugin_settings'));
    }

    /**
     * Initialize plugin settings.
     *
     * @since 1.0.0
     * @since 1.14.0 Refactor codebase.
     * @access public
     *
     * @param array $settings Array of WC settings.
     * @return array Filtered array of wc settings.
     */
    public function initialize_plugin_settings($settings)
    {

        $settings[] = include WWP_INCLUDES_PATH . "class-wwp-settings.php";

        return $settings;
    }

    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Execution WWPP
    |-------------------------------------------------------------------------------------------------------------------
     */

    /**
     * Execute WWP. Triggers the execution codes of the plugin models.
     *
     * @since 1.3.0
     * @access public
     */
    public function run()
    {

        $this->wwp_marketing->run();
        $this->wwp_wholesale_roles->run();
        $this->wwp_bootstrap->run();
        $this->wwp_script_loader->run();
        $this->wwp_admin_custom_fields_simple_product->run();
        $this->wwp_admin_custom_fields_variable_product->run();
        $this->wwp_wholesale_prices->run();
        $this->wwp_order->run();
        $this->wwp_duplicate_product->run();
        $this->wwp_products_cpt->run();
        $this->wwp_wholesale_roles_admin_page->run();
        $this->wwp_import_export->run();
        $this->wwp_wpml_compatibility->run();
        $this->wwp_product_visibility->run();
        $this->wwp_lead_capture->run();
        $this->wwp_order_form->run();
        $this->wwp_usage->run();
        $this->wwp_for_non_wholesale_customer->run();
        $this->wwp_wholesale_price_grouped_product->run();
        $this->wwp_admin_menu->run();
        $this->wwp_dashboard->run();
        $this->wwp_about_page->run();
        $this->wwp_help_page->run();
        $this->wwp_advanced_coupons_page->run();
        $this->wwp_upgrade_to_premium_page->run();
        $this->wwp_admin_notice_bar->run();
        $this->wwp_wws_license_manager->run();

        // Load default prices settings content if premium add on isn't present
        if (!WWP_Helper_Functions::is_wwpp_active()) {

            // Initialize Plugin Settings only if WWPP is not active
            $this->activate_plugin_settings();
        }
    }
}
