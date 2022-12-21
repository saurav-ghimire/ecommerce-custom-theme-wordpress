<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WWP_Script_Loader' ) ) {

    /**
     * Model that houses the logic of loading in scripts to various pages of the plugin.
     *
     * @since 1.4.0
     */
    class WWP_Script_Loader { // phpcs:ignore


        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
         */

        /**
         * Property that holds the single main instance of WWP_Script_Loader.
         *
         * @since 1.4.0
         * @access private
         * @var WWP_Script_Loader
         */
        private static $_instance;

        /**
         * Model that houses the logic of retrieving information relating to wholesale role/s of a user.
         *
         * @since 1.4.0
         * @access private
         * @var WWP_Wholesale_Roles
         */
        private $_wwp_wholesale_roles;

        /**
         * Model that houses logic of wholesale prices.
         *
         * @since 1.4.0
         * @access private
         * @var WWP_Wholesale_Prices
         */
        private $_wwp_wholesale_prices;

        /**
         * Current WWP version.
         *
         * @since 1.3.1
         * @access private
         * @var int
         */
        private $_wwp_current_version;

        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
         */

        /**
         * WWP_Script_Loader constructor.
         *
         * @since 1.4.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Script_Loader model.
         */
        public function __construct( $dependencies ) {
            $this->_wwp_wholesale_roles  = $dependencies['WWP_Wholesale_Roles'];
            $this->_wwp_wholesale_prices = $dependencies['WWP_Wholesale_Prices'];
            $this->_wwp_current_version  = $dependencies['WWP_CURRENT_VERSION'];

        }

        /**
         * Ensure that only one instance of WWP_Script_Loader is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.4.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Script_Loader model.
         * @return WWP_Script_Loader
         */
        public static function instance( $dependencies ) {
            if ( ! self::$_instance instanceof self ) {
                self::$_instance = new self( $dependencies );
            }

            return self::$_instance;
        }

        /**
         * Load admin or backend related styles and scripts.
         * Only load em on the right time and on the right place.
         *
         * @since 1.0.0
         * @since 1.4.0 Refactor codebase and move to its own model.
         * @since 2.1.0 Added custom decimal place for calculation on percentage discount, added also filter so that this can be overridden based on your requirements.
         * @access public
         *
         * @param string $handle Hook suffix for the current admin page.
         */
        public function load_back_end_styles_and_scripts( $handle ) {
            // Prepare everything needed.
            $screen = get_current_screen();

            $post_type = get_post_type();
            if ( ! $post_type && isset( $_GET['post_type'] ) ) { // phpcs:ignore
                $post_type = sanitize_text_field( $_GET['post_type'] ); // phpcs:ignore
            }

            $review_response = get_option( WWP_REVIEW_REQUEST_RESPONSE );

            /***********************************************************************************************************
             * General Backend Overrides (Careful, this is loaded on all backend pages!)
             */
            wp_enqueue_style( 'wwp_wcoverrides_css', WWP_CSS_URL . 'wwp-back-end-wcoverrides.css', array(), $this->_wwp_current_version, 'all' );

            /***********************************************************************************************************
             * Show Review Request Popup
             */
            if ( is_admin() && current_user_can( 'manage_options' ) && get_option( WWP_SHOW_REQUEST_REVIEW ) === 'yes' && ( 'review-later' === $review_response || empty( $review_response ) ) ) {

                if ( WWP_Helper_Functions::is_wwpp_active() ) {

                    $msg = sprintf(
                        /* Translators: %1$s: Support URL */
                        __(
                            '<p>We see you have been using Wholesale Suite for a couple of weeks now - thank you once again for your purchase and we hope you are enjoying it so far!</p>
                                         <p>We\'d really appreciate it if you could take a few minutes to write a 5-star review of our Wholesale Prices plugin on WordPress.org!</p>
                                         <p>Your comment will go a long way to helping us grow and giving new users the confidence to give us a try.</p>
                                         <p>Thanks in advance, we are looking forward to reading it!</p>
                                         <p>PS. If you ever need support, please just <a href="%1$s" target="_blank">get in touch here.</a></p>',
                            'woocommerce-wholesale-prices'
                        ),
                        'https://wordpress.org/support/plugin/woocommerce-wholesale-prices/'
                    );
                } else {

                    $msg = __(
                        '<p>Thanks for using our free WooCommerce Wholesale Prices plugin - we hope you are enjoying it so far.</p>
                                <p>We\'d really appreciate it if you could take a few minutes to write a 5-star review of our Wholesale Prices plugin on WordPress.org!</p>
                                <p>Your comment will go a long way to helping us grow and giving new users the confidence to give us a try.</p>
                                <p>Thanks in advance, we are looking forward to reading it!</p>',
                        'woocommerce-wholesale-prices'
                    );
                }

                wp_enqueue_style( 'wwp-wwp-review', WWP_CSS_URL . '/wwp-review.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_style( 'wwp-vex-css', WWP_JS_URL . 'lib/vexjs/vex.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_style( 'wwp-vex-theme-plain-css', WWP_JS_URL . 'lib/vexjs/vex-theme-plain.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_script( 'wwp-vex-js', WWP_JS_URL . 'lib/vexjs/vex.combined.min.js', array(), $this->_wwp_current_version, true );
                wp_enqueue_script( 'wwp-review-request', WWP_JS_URL . 'backend/wwp-review-request.js', array(), $this->_wwp_current_version, true );
                wp_localize_script(
                    'wwp-review-request',
                    'review_request_args',
                    array(
						'js_url'      => WWP_IMAGES_URL,
						'msg'         => $msg,
						'review_link' => 'https://wordpress.org/support/plugin/woocommerce-wholesale-prices/reviews/?filter=5#new-post',
                        'nonce'       => wp_create_nonce( 'wwp_request_review_nonce' ),
                    )
                );
            }

            /**
             * Product Listing & Edit Product Pages
             */
            if ( in_array( $screen->id, array( 'edit-product' ), true ) ) {
                // Product listing.

                wp_enqueue_style( 'wwp_cpt_product_listing_admin_main_css', WWP_CSS_URL . 'backend/cpt/product/wwp-cpt-product-listing-admin-main.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_script( 'wwp_cpt_product_listing_admin_main_js', WWP_JS_URL . 'backend/cpt/product/wwp-cpt-product-listing-admin-main.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion' ), $this->_wwp_current_version, false ); // Must not be loaded on footer, else it won't work.

            } elseif ( ( 'post-new.php' === $handle || 'post.php' === $handle ) && 'product' === $post_type ) {
                // Single product admin page ( new and edit product ).

                // VEX.
                wp_enqueue_style( 'wwp-vex-css', WWP_JS_URL . 'lib/vexjs/vex.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_style( 'wwp-vex-theme-plain-css', WWP_JS_URL . 'lib/vexjs/vex-theme-plain.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_style( 'wwp-vex-styling-css', WWP_CSS_URL . 'wwp-vex-styling.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_script( 'wwp-vex-js', WWP_JS_URL . 'lib/vexjs/vex.combined.min.js', array(), $this->_wwp_current_version, true );

                // Chosen.
                wp_enqueue_style( 'wwp_chosen_css', WWP_JS_URL . 'lib/chosen/chosen.min.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_script( 'wwp_chosen_js', WWP_JS_URL . 'lib/chosen/chosen.jquery.min.js', array( 'jquery' ), $this->_wwp_current_version, true );

                // Single Product specific CSS/JS.
                wp_enqueue_style( 'wwp_cpt_product_single_admin_main_css', WWP_CSS_URL . 'backend/cpt/product/wwp-cpt-product-single-admin-main.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_script( 'wwp_cpt_product_single_admin_main_js', WWP_JS_URL . 'backend/cpt/product/wwp-cpt-product-single-admin-main.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion' ), $this->_wwp_current_version, true );
                wp_enqueue_script( 'wwp_single_variable_product_admin_custom_bulk_actions_js', WWP_JS_URL . 'backend/cpt/product/wwp-single-variable-product-admin-custom-bulk-actions.js', array( 'jquery' ), $this->_wwp_current_version, true );

                wp_localize_script(
                    'wwp_single_variable_product_admin_custom_bulk_actions_js',
                    'wwp_custom_bulk_actions_params',
                    array(
						'wholesale_roles'     => $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles(),
						'i18n_prompt_message' => __( 'Enter a value (leave blank to remove pricing)', 'woocommerce-wholesale-prices' ),
                    )
                );

                // Percentage Wholesale Pricing On Product Level.
                wp_enqueue_style( 'wwp_percentage_pricing_css', WWP_CSS_URL . 'backend/wwp-backend-percentage-pricing.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_script( 'wwp_currency_js', WWP_JS_URL . 'lib/currency/currency.min.js', array(), $this->_wwp_current_version, true );
                wp_enqueue_script( 'wwp_percentage_wholesale_pricing_on_product_level_js', WWP_JS_URL . 'backend/wwp-percentage-wholesale-pricing-on-product-level.js', array( 'jquery', 'jquery-tiptip' ), $this->_wwp_current_version, true );
                wp_localize_script(
                    'wwp_percentage_wholesale_pricing_on_product_level_js',
                    'wwp_percentage_wholesale_options',
                    array(
						'wholesale_roles'            => $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles(),
						'is_aelia_active'            => WWP_ACS_Integration_Helper::aelia_currency_switcher_active(),
						'decimal_sep'                => get_option( 'woocommerce_price_decimal_sep' ),
						'decimal_num'                => get_option( 'woocommerce_price_num_decimals' ),
						'thousand_sep'               => get_option( 'woocommerce_price_thousand_sep' ),
						'calculation_decimal_places' => apply_filters( 'wwp_percentage_discount_calculation_decimal_places', 6 ),
						'i18n_discount_greater_than_100_percent_error' => __( 'Please enter in a value not greater than 100 percent.', 'woocommerce-wholesale-prices' ),
						'i18n_discount_less_than_0_percent_error' => __( 'Please enter in a value not less than 0 percent', 'woocommerce-wholesale-prices' ),
                    )
                );

                // Upsell specific.
                if ( ! WWP_Helper_Functions::is_wwpp_active() ) {
                    wp_enqueue_style( 'wwp-backend-product-page-upsell', WWP_CSS_URL . 'wwp-backend-product-page-upsell.css', array(), $this->_wwp_current_version, 'all' );
                    wp_enqueue_script( 'wwp-backend-product-page-upsell', WWP_JS_URL . 'backend/wwp-backend-product-page-upsell.js', array(), $this->_wwp_current_version, true );

                    wp_localize_script(
                        'wwp-backend-product-page-upsell',
                        'backend_product_page_upsell_args',
                        array(
							'images_url'         => WWP_IMAGES_URL,
							'wholesale_prices'   => array(
								'title' => '<h4>' . __( 'Upgrade For Additional Wholesale Price Levels', 'woocommerce-wholesale-prices' ) . '</h4>',
								'msg'   => '<p>' . __(
									'WooCommerce Wholesale Prices Premium lets you add additional levels of pricing
                                    by adding more wholesale user roles. Click below for all the details.',
									'woocommerce-wholesale-prices'
                                ) . '</p>',
								'link'  => 'https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=wwp&utm_medium=upsell&utm_campaign=wwpproducteditadditionalprices',
							),
							'product_visibility' => array(
								'title' => '<h4>' . __( 'Upgrade For Product Visibility Features', 'woocommerce-wholesale-prices' ) . '</h4>',
								'msg'   => '<p>' . __(
									'WooCommerce Wholesale Prices Premium lets you change the visibility of
                                    your products by selecting which wholesale roles should be able to see it. Click below for all
                                    the details.',
									'woocommerce-wholesale-prices'
                                ) . '</p>',
								'link'  => 'https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=wwp&utm_medium=upsell&utm_campaign=wwpproducteditproductvisibility',
							),
							'button_text'        => __( 'See Features & Pricing', 'woocommerce-wholesale-prices' ),
							'bonus_text'         => __( "<p><span style='font-weight: bold;'>Bonus:</span> Wholesale Prices lite users get <span class='green-text'>50% off regular price</span> automatically applied at checkout.</p>", 'woocommerce-wholesale-prices' ),
                        )
                    );
                }
            }

            /**
             * Backend Common CSS
             */
            if ( get_option( 'wwp_admin_notice_getting_started_show' ) === 'yes' || get_option( WWP_SHOW_INSTALL_ACFWF_NOTICE ) === 'yes' || ( isset( $_GET['tab'] ) && 'wwp_settings' === $_GET['tab'] ) ) { // phpcs:ignore
                wp_enqueue_style( 'wwp_backend_main_css', WWP_CSS_URL . 'wwp-back-end-main.css', array(), $this->_wwp_current_version, 'all' );
            }

            /**
             * Notices
             */
            // Getting Started notice. Notice shows up on every page in the backend unless the message is dismissed.
            if ( get_option( 'wwp_admin_notice_getting_started_show' ) === 'yes' ) {
                wp_enqueue_script( 'wwp_getting_started_js', WWP_JS_URL . 'backend/wwp-getting-started.js', array( 'jquery' ), $this->_wwp_current_version, true );
                wp_localize_script(
                    'wwp_getting_started_js',
                    'wwp_getting_started_js_params',
                    array(
                        'nonce' => wp_create_nonce( 'wwp_getting_started_nonce' ),
                    )
                );
            }

            // Install ACFWF notice. Notice shows up on every page in the backend unless the message is dismissed.
            if ( get_option( WWP_SHOW_INSTALL_ACFWF_NOTICE ) === 'yes' ) {
                wp_enqueue_script( 'wwp_acfwf_install_notice_js', WWP_JS_URL . 'backend/wwp-acfwf-install-notice.js', array( 'jquery' ), $this->_wwp_current_version, true );
                wp_localize_script(
                    'wwp_acfwf_install_notice_js',
                    'wwp_acfwf_install_notice_js_params',
                    array(
                        'nonce' => wp_create_nonce( 'wwp_hide_acfwf_install_notice_nonce' ),
                    )
                );
            }

            /**
             * Wholesale Roles Page
             */
            // Load script if premium add on isn't present.
            if (
                strpos( $screen->id, 'wwpp-wholesale-roles-page' ) !== false &&
                ! WWP_Helper_Functions::is_wwpp_active()
            ) {

                // Vex.
                wp_enqueue_style( 'wwp-vex-css', WWP_JS_URL . 'lib/vexjs/vex.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_style( 'wwp-vex-theme-plain-css', WWP_JS_URL . 'lib/vexjs/vex-theme-plain.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_style( 'wwp-vex-styling-css', WWP_CSS_URL . 'wwp-vex-styling.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_script( 'wwp-vex-js', WWP_JS_URL . 'lib/vexjs/vex.combined.min.js', array(), $this->_wwp_current_version, true );

                // Toastr.
                wp_enqueue_style( 'wwp_toastr_css', WWP_JS_URL . 'lib/toastr/toastr.min.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_script( 'wwp_toastr_js', WWP_JS_URL . 'lib/toastr/toastr.min.js', array( 'jquery' ), $this->_wwp_current_version, true );

                // Roles page specific css styling.
                wp_enqueue_style( 'wwp_roles_page_css', WWP_CSS_URL . 'wwp-backend-wholesale-roles.css', array(), $this->_wwp_current_version, 'all' );

                wp_enqueue_script( 'wwp_backEndAjaxServices_js', WWP_JS_URL . 'app/modules/BackEndAjaxServices.js', array( 'jquery' ), $this->_wwp_current_version, true );
                wp_enqueue_script( 'wwp_wholesaleRolesFormActions_js', WWP_JS_URL . 'app/modules/WholesaleRolesFormActions.js', array( 'jquery' ), $this->_wwp_current_version, true );

                wp_enqueue_script( 'wwp_wholesaleRolesListingActions_js', WWP_JS_URL . 'app/modules/WholesaleRolesListingActions.js', array( 'jquery' ), $this->_wwp_current_version, true );
                wp_localize_script(
                    'wwp_wholesaleRolesListingActions_js',
                    'wwp_wholesaleRolesListingActions_params',
                    array(
						'i18n_yes' => __( 'Yes', 'woocommerce-wholesale-prices' ),
						'i18n_no'  => __( 'No', 'woocommerce-wholesale-prices' ),
                    )
                );

                wp_enqueue_script( 'wwp_wholesale_roles_main_js', WWP_JS_URL . 'app/wholesale-roles-main.js', array( 'jquery', 'jquery-tiptip' ), $this->_wwp_current_version, true );
                wp_localize_script(
                    'wwp_wholesale_roles_main_js',
                    'wwp_wholesale_roles_main_params',
                    array(
						'i18n_enter_role_name'          => __( 'Please Enter Role Name', 'woocommerce-wholesale-prices' ),
						'i18n_error_wholesale_form'     => __( 'Error in Wholesale Form', 'woocommerce-wholesale-prices' ),
						'i18n_enter_role_key'           => __( 'Please Enter Role Key', 'woocommerce-wholesale-prices' ),
						'i18n_role_successfully_edited' => __( 'Wholesale Role Successfully Edited', 'woocommerce-wholesale-prices' ),
						'i18n_successfully_edited_role' => __( 'Successfully Edited Role', 'woocommerce-wholesale-prices' ),
						'i18n_failed_edit_role'         => __( 'Failed to Edit Wholesale Role', 'woocommerce-wholesale-prices' ),
						'i18n_upsell_message'           => $this->role_page_upsell_message(),
						'nonce'                         => wp_create_nonce( 'wwp_role_edit_nonce' ),
                    )
                );
            }

            /**
             * Wholesale Lead Capture Page
             */
            // Load script if premium add on isn't present.
            if (
                strpos( $screen->id, 'wwp-lead-capture-page' ) !== false &&
                ! WWP_Helper_Functions::is_wwlc_active()
            ) {
                wp_enqueue_style( 'wwp_lead_capture_page_css', WWP_CSS_URL . 'wwp-lead-capture.css', array(), $this->_wwp_current_version, 'all' );
            }

            /**
             * Wholesale Order Form Page
             */
            // Load script if order form add on isn't present.
            if (
                strpos( $screen->id, 'order-forms' ) !== false &&
                ! WWP_Helper_Functions::is_wwof_active()
            ) {
                wp_enqueue_style( 'wwp_order_form_page_css', WWP_CSS_URL . 'wwp-order-form.css', array(), $this->_wwp_current_version, 'all' );
            }

            /**
             * Help Page
             */
            if ( strpos( $screen->id, 'help-page' ) !== false ) {
                wp_enqueue_style( 'wwp_help_page_css', WWP_CSS_URL . 'wwp-help-page.css', array(), $this->_wwp_current_version, 'all' );
            }

            /***********************************************************************************************************
             * About Page
             */
            if ( strpos( $screen->id, 'about-page' ) !== false ) {
                wp_enqueue_style( 'wwp_about_page_css', WWP_CSS_URL . 'wwp-about-page.css', array(), $this->_wwp_current_version, 'all' );
            }

            /**
             * Advanced Coupons Page
             */
            // Load script if ACFW is not present. Test if Advanced Coupon plugin is not installed or if it is, check if it's not active.
            if ( strpos( $screen->id, 'marketing_page_advanced-coupons-page' ) !== false &&
                (
                    ! WWP_Helper_Functions::is_acfwf_installed() ||
                    ( WWP_Helper_Functions::is_acfwf_installed() && ! WWP_Helper_Functions::is_acfwf_active() )
                )
            ) {
                wp_enqueue_style( 'wwp_advanced_coupons_page_css', WWP_CSS_URL . 'wwp-advanced-coupons-page.css', array(), $this->_wwp_current_version, 'all' );
            }

            /**
             * Upgrade To Premium Page
             */
            if ( strpos( $screen->id, 'upgrade-to-premium-page' ) !== false &&
                (
                    ! WWP_Helper_Functions::is_wwp_installed() ||
                    ( WWP_Helper_Functions::is_wwp_installed() && ! WWP_Helper_Functions::is_wwpp_active() )
                )
            ) {

                // Style from wc.
                wp_enqueue_style( 'woocommerce_admin_styles' );

                // Style from wp.
                wp_enqueue_style( 'wp_forms_css', site_url() . '/wp-admin/css/forms.css', array(), $this->_wwp_current_version, 'all' );

                wp_enqueue_style( 'wwp_wwp_upgrade_css', WWP_CSS_URL . 'wwp-upgrade.css', array(), $this->_wwp_current_version, 'all' );

            }

            /**
             * WWP Settings
             */
            if (
                ! WWP_Helper_Functions::is_wwpp_active() &&
                isset( $_GET['tab'] ) && 'wwp_settings' === $_GET['tab'] // phpcs:ignore
            ) {

                // Queue up stuff that is used on all tabs.
                wp_enqueue_style( 'wwp-vex-css', WWP_JS_URL . 'lib/vexjs/vex.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_style( 'wwp-vex-theme-plain-css', WWP_JS_URL . 'lib/vexjs/vex-theme-plain.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_style( 'wwp-vex-styling-css', WWP_CSS_URL . 'wwp-vex-styling.css', array(), $this->_wwp_current_version, 'all' );
                wp_enqueue_script( 'wwp-vex-js', WWP_JS_URL . 'lib/vexjs/vex.combined.min.js', array(), $this->_wwp_current_version, true );

                // WWP Free Training Guide.
                wp_enqueue_style( 'wwp-free-training-guide-css', WWP_CSS_URL . 'backend/wwp-free-training-guide.css', array(), $this->_wwp_current_version, 'all' );

                // Handle each section of the settings (General, Price, Tax, Upgrade).
                if ( isset( $_GET['section'] ) ) { // phpcs:ignore
                    switch ( $_GET['section'] ) { // phpcs:ignore
                        case 'wwpp_setting_price_section':
                            wp_enqueue_script( 'wwp-price-settings', WWP_JS_URL . 'backend/wwp-price-setting.js', array( 'select2' ), $this->_wwp_current_version, true );
                            wp_localize_script(
                                'wwp-price-settings',
                                'price_settings_args',
                                array(
									'images_url'        => WWP_IMAGES_URL,
									'use_regular_price' => array(
										'title' => '<h4>' . __( 'Define Prices By Percentage Globally Or On Categories', 'woocommerce-wholesale-prices' ) . '</h4>',
										'msg'   => '<p>' . __(
											'In WooCommerce Wholesale Prices Premium you can set your wholesale prices by a percentage on a category or site-wide general level.
                                            This can save heaps of time instead of setting wholesale pricing on individual products. Read more about it below.',
											'woocommerce-wholesale-prices'
                                        ) . '</p>',
										'link'  => 'https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=wwp&utm_medium=upsell&utm_campaign=wwppricesettingsalwaysuseregularwwpplink',
									),
									'variable_product_price_display' => array(
										'title' => '<h4>' . __( 'Change How Variable Product Prices Are Displayed', 'woocommerce-wholesale-prices' ) . '</h4>',
										'msg'   => '<p>' . __(
											'Changing how your variable product prices are displayed can reduce the amount of computational work WooCommerce does on load, making your site faster.
                            Access this optimization option and more in the WooCommerce Wholesale Prices Premium plugin.',
											'woocommerce-wholesale-prices'
                                        ) . '</p>',
										'link'  => 'https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=wwp&utm_medium=upsell&utm_campaign=wwppricesettingsvariabledisplaywwpplink',
									),
									'button_text'       => __( 'See Features & Pricing', 'woocommerce-wholesale-prices' ),
                                )
                            );

                            // Load related styles and scripts for non wholesale users.
                            $this->load_wwp_prices_settings_for_non_wholesale_users_styles_and_scripts();
                            break;

                        case 'wwpp_setting_tax_section':
                            wp_enqueue_style( 'wwp-tax-css', WWP_CSS_URL . 'wwp-tax-settings.css', array(), $this->_wwp_current_version, 'all' );
                            wp_enqueue_script( 'wwp-tax-settings', WWP_JS_URL . 'backend/wwp-tax-setting.js', array(), $this->_wwp_current_version, true );
                            wp_localize_script(
                                'wwp-tax-settings',
                                'tax_settings_args',
                                array(
									'images_url'       => WWP_IMAGES_URL,
									'tax_exemption'    => array(
										'title' => '<h4>' . __( 'Upgrade To Wholesale Suite For Tax Exemption', 'woocommerce-wholesale-prices' ) . '</h4>',
										'msg'   => __(
											'<p>Wholesale Suite is the #1 best rated wholesale solution for WooCommerce.</p>
                                                    <p>Prices Premium (one of the three plugins) features in-depth tax exemption controls including being able to turn on/off tax exemption just for specific wholesale roles.</p>',
											'woocommerce-wholesale-prices'
                                        ),
										'link'  => 'https://wholesalesuiteplugin.com/bundle/?utm_source=wwp&utm_medium=upsell&utm_campaign=wwptaxexemptionpopupbutton',
									),
									'tax_display'      => array(
										'title' => '<h4>' . __( 'Upgrade To Wholesale Suite For Advanced Tax Display', 'woocommerce-wholesale-prices' ) . '</h4>',
										'msg'   => '<p>' . __( 'Wholesale Suite is the #1 best rated wholesale solution for WooCommerce. Prices Premium (one of the three plugins) features in-depth tax display controls.', 'woocommerce-wholesale-prices' ) . '</p>',
										'link'  => 'https://wholesalesuiteplugin.com/bundle/?utm_source=wwp&utm_medium=upsell&utm_campaign=wwptaxdisplaypopupbutton',
									),
									'suffix_overrides' => array(
										'title' => '<h4>' . __( 'Upgrade To Wholesale Suite For Suffix Overrides', 'woocommerce-wholesale-prices' ) . '</h4>',
										'msg'   => __(
											'<p>Wholesale Suite is the #1 best rated wholesale solution for WooCommerce. Prices Premium (one of three plugins) features advanced price suffix controls.</p>
                                                    <p>This can help in complex tax situations where prices suffixes should be different for wholesale customers.</p>',
											'woocommerce-wholesale-prices'
                                        ),
										'link'  => 'https://wholesalesuiteplugin.com/bundle/?utm_source=wwp&utm_medium=upsell&utm_campaign=wwppricesuffixpopupbutton',
									),
									'button_text'      => __( 'See Features & Pricing', 'woocommerce-wholesale-prices' ),
                                )
                            );
                            break;

                        case 'wwp_upgrade_section':
                            wp_enqueue_style( 'wwp_wwp_upgrade_css', WWP_CSS_URL . 'wwp-upgrade.css', array(), $this->_wwp_current_version, 'all' );
                            break;
                        case 'wwp_license_section':
                            wp_enqueue_style( 'wwp-license-upgrade-css', WWP_CSS_URL . 'wwp-license-upgrade.css', array(), $this->_wwp_current_version, 'all' );
                            break;

                        default:
                            break;
                    }
                } else {
                    // General page.
                    wp_enqueue_style( 'wwp-general-css', WWP_CSS_URL . 'wwp-general-settings.css', array(), $this->_wwp_current_version, 'all' );
                }
            } else {
                if ( isset( $_GET['section'] ) ) { // phpcs:ignore
                    switch ( $_GET['section'] ) { // phpcs:ignore
                        case 'wwpp_setting_price_section':
                            $this->load_wwp_prices_settings_for_non_wholesale_users_styles_and_scripts();
                            break;
                        default:
                            break;
                    }
                }
            }

            // enqueue script to replace icons in wc-admin marketing.
            if ( 'woocommerce_page_wc-admin' === $screen->id || 'edit-shop_coupon' === $screen->id ) {
                wp_enqueue_script( 'wwp-wc-admin-icons', WWP_JS_URL . 'backend/wwp-wc-admin-icons.js', array( 'jquery' ), $this->_wwp_current_version, true );
                wp_localize_script(
                    'wwp-wc-admin-icons',
                    'wwpAdminIcons',
                    array(
						'imgUrl' => WWP_IMAGES_URL,
                    )
                );
            }

        }

        /**
         * Load wwp prices settings for non wholesale users ralated styles and scripts
         *
         * @since 1.15.0
         * @access private
         */
        private function load_wwp_prices_settings_for_non_wholesale_users_styles_and_scripts() {
            // Check if WWLC is installed.
            $wwlc_plugin_data     = WWP_Helper_Functions::get_plugin_data( 'woocommerce-wholesale-lead-capture/woocommerce-wholesale-lead-capture.bootstrap.php' );
            $admin_notice_message = '';

            // Only display if WWLC is not installed.
            if ( empty( $wwlc_plugin_data ) ) {

                $admin_notice_message = '<a href="https://wholesalesuiteplugin.com/woocommerce-wholesale-lead-capture/?utm_source=wwp&utm_medium=upsell&utm_campaign=wwlcshowtononwholesale" target="_blank">' . __( 'Get WooCommerce Wholesale Lead Capture to unlock this powerful feature + more', 'woocommerce-wholesale-prices' ) . '&rarr;</a>';

            }

            // Toastr.
            wp_enqueue_style( 'wwp_toastr_css', WWP_JS_URL . 'lib/toastr/toastr.min.css', array(), $this->_wwp_current_version, 'all' );
            wp_enqueue_script( 'wwp_toastr_js', WWP_JS_URL . 'lib/toastr/toastr.min.js', array( 'jquery' ), $this->_wwp_current_version, true );

            // Price tab CSS.
            wp_enqueue_style( 'wwp-price-non-wholesale-css', WWP_CSS_URL . 'wwp-price-settings.css', array(), $this->_wwp_current_version, 'all' );

            // Load Custom JS script.
            wp_enqueue_script( 'wwp-price-settings-non-wholesale_js', WWP_JS_URL . 'backend/wwp-price-settings-non-wholesale.js', array( 'jquery', 'jquery-tiptip', 'select2' ), $this->_wwp_current_version, true );

            wp_localize_script(
                'wwp-price-settings-non-wholesale_js',
                'Options',
                array(
					'wwp_price_settings_register_text'    => get_option( 'wwp_price_settings_register_text' ),
					'wwp_price_settings_register_text_tooltip' => wc_help_tip( __( 'This text is linked to the defined registration page in WooCommerce Wholesale Lead Capture Settings.', 'woocommerce-wholesale-prices' ) ),
					'wwp_see_wholesale_prices_replacement_text' => get_option( 'wwp_see_wholesale_prices_replacement_text' ),
					'wwp_see_wholesale_prices_replacement_text_tooltip' => wc_help_tip( __( 'The \'Click to See Wholesale Prices\' text seen in frontend.', 'woocommerce-wholesale-prices' ) ),
					'wwp_wholesale_role_select_select2_tooltip' => wc_help_tip( __( 'The selected wholesale roles and pricing that should show to non-wholesale customers on the front end.', 'woocommerce-wholesale-prices' ) ),
					'wholesale_role_data_placeholder_txt' => __( 'Choose wholesale role...', 'woocommerce-wholesale-prices' ),
					'wholesale_role_options'              => get_option( 'wwp_non_wholesale_wholesale_role_select2' ),
					'wholesale_roles'                     => $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles(),
					'wwlc_admin_notice'                   => $admin_notice_message,
					'is_wwpp_active'                      => WWP_Helper_Functions::is_wwpp_active(),
					'is_wwlc_active'                      => WWP_Helper_Functions::is_wwlc_active(),
					'is_wwof_active'                      => WWP_Helper_Functions::is_wwof_active(),
					'show_in_shop'                        => get_option( 'wwp_non_wholesale_show_in_shop' ),
					'show_in_products'                    => get_option( 'wwp_non_wholesale_show_in_products' ),
					'show_in_wwof'                        => get_option( 'wwp_non_wholesale_show_in_wwof' ),
					'base_url'                            => get_site_url(),
                    // =================================================
                    // WooCommerce API Settings
                    // =================================================
                    'i18n_woocommerce_api_title'          => __( 'WooCommerce API', 'woocommerce-wholesale-prices' ),
                    'i18n_woocommerce_api_help_desc'      => __( 'The WooCommerce REST API works on a key system to control access. These keys are linked to WordPress users on your website.<br><p>You will need this API Keys in order for Show Wholesale Prices to Non Wholesale Users option to be activated. </p>', 'woocommerce-wholesale-prices' ),
                    'i18n_consumer_key'                   => __( 'Consumer Key', 'woocommerce-wholesale-prices' ),
                    'i18n_consumer_secret'                => __( 'Consumer Secret', 'woocommerce-wholesale-prices' ),
                    'i18n_generate_api_key'               => __( 'Generate API Keys', 'woocommerce-wholesale-prices' ),
                    'i18n_auto_generate_key'              => __( 'Auto Generate', 'woocommerce-wholesale-prices' ),
                    'i18n_api_key_is_valid_msg'           => __( 'API keys are valid.', 'woocommerce-wholesale-order-form' ),
                    'i18n_api_key_is_invalid_msg'         => __( 'Please enter a valid API keys.', 'woocommerce-wholesale-order-form' ),
                    'is_api_key_valid'                    => WWP_REST_API_Keys::is_api_key_valid(),

					/**
                     * Show Wholesale Prices to Non Wholesale Customers
                     */
					'i18n_show_wholesale_price_settings_title' => __( 'Show Wholesale Prices Box For Non Wholesale Customers', 'woocommerce-wholesale-prices' ),
					'i18n_locations_title'                => __( 'Locations', 'woocommerce-wholesale-prices' ),
					'i18n_click_to_see_wholesale_price_title' => __( 'Click to See Wholesale Prices Text', 'woocommerce-wholesale-prices' ),
					'i18n_wholesale_role_title'           => __( 'Wholesale Roles(s)', 'woocommerce-wholesale-prices' ),
					'i18n_register_title'                 => __( 'Register Text', 'woocommerce-wholesale-prices' ),
					'i18n_locations_shop'                 => __( 'Shop Archives', 'woocommerce-wholesale-prices' ),
					'i18n_locations_single_product'       => __( 'Single Product', 'woocommerce-wholesale-prices' ),
					'i18n_locations_order_form'           => __( 'Wholesale Order Form', 'woocommerce-wholesale-prices' ),
					'i18n_bonus_text'                     => __( "<p><span style='font-weight: bold;'>Bonus:</span> Wholesale Prices lite users get <span class='green-text'>50% off regular price</span> automatically applied at checkout.</p>", 'woocommerce-wholesale-prices' ),
					'i18n_register_text_notice'           => __( "<div class='notice wwp-wwlc-inactive'><p><i class='fa fa-star checked'></i><strong> Recommended Plugin: <a href='https://wholesalesuiteplugin.com/woocommerce-wholesale-lead-capture/?utm_source=wwp&utm_medium=upsell&utm_campaign=wholesaletoguestwwlcrecommend' target='_blank'>WooCommerce Wholesale Lead Capture</a></strong></p><p>Lead Capture adds an additional 'register text' link to the wholesale prices box on the front end to help you capture even more wholesale leads.</p><p>", 'woocommerce-wholesale-prices' ),
					'i18n_wwof_inactive_notice'           => '<small>' . __( 'To use this option, you must have <strong>WooCommerce Wholesale Order Form</strong> plugin installed and activated.', 'woocommerce-wholesale-prices' ) . '</small>',
                )
            );

            // Generate API Key JS script.
            wp_enqueue_script( 'wwp-price-settings-generate-api-key-js', WWP_JS_URL . 'backend/wwp-generate-api-key.js', array( 'jquery' ), $this->_wwp_current_version, true );
            wp_localize_script(
                'wwp-price-settings-generate-api-key-js',
                'api_keys',
                apply_filters(
                    'wwp_generate_api_args',
                    array(
						'root'                  => esc_url_raw( rest_url() ),
						'nonce'                 => wp_create_nonce( 'wp_rest' ),
						'security_generate_key' => wp_create_nonce( 'update-api-key' ),
						'user_id'               => get_current_user_id(),
						'description'           => __( 'Wholesale Prices API Keys', 'woocommerce-wholesale-prices' ),
						'success_message'       => __( 'Successfully created API key.', 'woocommerce-wholesale-prices' ),
						'permissions'           => 'read_write', // Valid values read, write, read_write.
						'i18n'                  => array(
							'success' => __( 'Success!', 'woocommerce-wholesale-prices' ),
							'fail'    => __( 'Fail!', 'woocommerce-wholesale-prices' ),
						),
                    )
                )
            );

        }

        /**
         * Load frontend related styles and scripts.
         * Only load em on the right time and on the right place.
         *
         * @since 1.0.0
         * @since 1.4.0 Refactor codebase and move to its own model.
         * @since 1.15.3 Load scripts for showing wholesale prices to non wholesale customers
         * @access public
         */
        public function load_front_end_styles_and_scripts() {
            global $post;

            if ( is_product() ) {

                /**
                 * This is about the issue where if variable product has variation with all having the same price.
                 * Wholesale price for a selected variation won't show on the single variable product page.
                 *
                 * This issue is already fixed in wwpp. Now if wwpp is installed and active, let wwpp fix this issue.
                 * Only fix this issue here in wwp if wwpp is not present.
                 *
                 * Note the fix on WWPP is different from the fix here coz WWPP has additional features to consider compared to WWP.
                 */
                if ( ! WWP_Helper_Functions::is_wwpp_active() ) {

                    wp_enqueue_style( 'wwp_single_product_page_css', WWP_CSS_URL . 'frontend/product/wwp-single-product-page.css', array(), $this->_wwp_current_version, 'all' );

                    if ( 'product' === $post->post_type ) {

                        $product = wc_get_product( $post->ID );

                        if ( WWP_Helper_Functions::wwp_get_product_type( $product ) === 'variable' ) {

                            $userWholesaleRole = $this->_wwp_wholesale_roles->getUserWholesaleRole();
                            $variationsArr     = array();

                            if ( ! empty( $userWholesaleRole ) ) {

                                $variations = WWP_Helper_Functions::wwp_get_variable_product_variations( $product );

                                foreach ( $variations as $variation ) {

                                    $variationProduct = wc_get_product( $variation['variation_id'] );

                                    $currVarPrice    = $variation['display_price'];
                                    $price_arr       = $this->_wwp_wholesale_prices->get_product_wholesale_price_on_shop_v3( $variation['variation_id'], $userWholesaleRole );
                                    $wholesalePrice  = $price_arr['wholesale_price'];
                                    $variationsArr[] = array(
                                        'variation_id' => $variation['variation_id'],
                                        'raw_regular_price' => (float) $currVarPrice,
                                        'raw_wholesale_price' => (float) $wholesalePrice,
                                        'has_wholesale_price' => is_numeric( $wholesalePrice ),
                                    );
                                }

                                /**
                                 * #WWP-51
                                 * Check if variable product has same regular price and same wholesale price
                                 * If true then don't load the script below
                                 */
                                $same_reg_price       = true;
                                $temp_reg_price       = null;
                                $same_wholesale_price = true;
                                $temp_wholesale_price = null;

                                foreach ( $variationsArr as $varData ) {

                                    if ( is_null( $temp_reg_price ) ) {
                                        $temp_reg_price = $varData['raw_regular_price'];
                                    } elseif ( $same_reg_price ) {
                                        $same_reg_price = ( $temp_reg_price === $varData['raw_regular_price'] );
                                    }

                                    if ( is_null( $temp_wholesale_price ) ) {
                                        $temp_wholesale_price = $varData['raw_wholesale_price'];
                                    } elseif ( $same_wholesale_price ) {
                                        $same_wholesale_price = ( $temp_wholesale_price === $varData['raw_wholesale_price'] );
                                    }
                                }

                                $same_prices = $same_reg_price && $same_wholesale_price;

                                if ( ! $same_prices ) {
                                    add_filter(
                                        'woocommerce_show_variation_price',
                                        function () {
                                        return true;
                                        }
                                    );
                                }
                            }
                        }
                    }
                }
            }

            $this->load_non_wholesale_users_frontend_styles_and_scripts();

        }

        /**
         * Upsell message shown as popup in wholesale roles page.
         *
         * @since 1.11
         * @access public
         */
        private function role_page_upsell_message() {
            ob_start();?>

            <div class="upsell-area">
                <h2><?php _e( 'Additional Wholesale Roles (Premium)', 'woocommerce-wholesale-prices' ); // phpcs:ignore ?></h2>
                <p><?php _e( 'You\'re currently using the free version of WooCommerce Wholesale Prices which lets you have one level of wholesale customers.', 'woocommerce-wholesale-prices' ); // phpcs:ignore ?>
                </p>
                <p>
                <?php
                echo sprintf(
                    /* Translators: %1$s: Wholesale Prices Premium plugin URL */
                    __( // phpcs:ignore
                        'In the <a href="%1$s" target="_blank">Premium add-on</a> you can add multiple wholesale roles. This will let you create separate "levels" of wholesale customers,
                                                        each of which can have separate wholesale pricing, shipping and payment mapping, order minimums and more.',
                        'woocommerce-wholesale-prices'
                    ),
                    'https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=wwp&utm_medium=upsell&utm_campaign=wwprolespagelinkpopup'
                );
                    ?>
                </p>
                <p>
                    <a class="button"
                        href="https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=wwp&utm_medium=upsell&utm_campaign=wwprolespagebuttonpopup"
                        target="_blank">
                        <?php _e( 'See the full feature list', 'woocommerce-wholesale-prices' ); // phpcs:ignore ?>
                        <span class="dashicons dashicons-arrow-right-alt" style="margin-top: 7px"></span>
                    </a>
                    <img class="fivestar" src="<?php echo WWP_IMAGES_URL; //phpcs:ignore ?>/5star.png" />
                </p>
            </div>
            <?php

            return ob_get_clean();
        }

        /**
         * Load scripts and styles for showing whoesale prices to non wholesale users
         * load only if show wholesale prices to non wholesale users is enabled in settings
         *
         * @since 1.15.0
         * @access public
         */
        public function load_non_wholesale_users_frontend_styles_and_scripts() {
            if ( ! WWP_REST_API_Keys::is_api_key_valid() ) {
                return;
            }

            global $wp_scripts;

            $user_wholesale_role                    = $this->_wwp_wholesale_roles->getUserWholesaleRole();
            $show_wholesale_prices_to_non_wholesale = get_option( 'wwp_prices_settings_show_wholesale_prices_to_non_wholesale' );

            /**
             * Check if Show wholesale price to non wholesale users is enabled.
             * Only Shop Manager, Administrator, Guest, and Regular Customer is allowed to view this.
             */
            if ( empty( $user_wholesale_role ) && ! is_checkout() ) {

                wp_enqueue_style( 'wwp-price-non-wholesale-css', WWP_CSS_URL . 'frontend/product/wwp-show-wholesale-prices-to-non-wholesale-users.css', array(), $this->_wwp_current_version, 'all' );

                wp_enqueue_script( 'wwp-prices-non-wholesale_js', WWP_JS_URL . 'app/wwp-prices-non-wholesales.js', array( 'jquery' ), WWP_Helper_Functions::get_wwp_version(), true );

                // Load Localize script.
                wp_localize_script(
                    'wwp-prices-non-wholesale_js',
                    'options',
                    array(
						'popover_header_title' => apply_filters( 'wwp_popover_header_title', __( 'Wholesale Prices', 'woocommerce-wholesale-prices' ) ),
						'is_wwlc_active'       => WWP_Helper_Functions::is_wwlc_active(),
						'nonce'                => wp_create_nonce( 'wwp_nonce' ),
						'ajaxurl'              => admin_url( 'admin-ajax.php' ),
						'show_wholesale_price_to_non_wholesale_customer' => get_option( 'wwp_prices_settings_show_wholesale_prices_to_non_wholesale' ),
                    )
                );

                /**
                 * Check if bootstrap.bundle.min.js is already loaded
                 */
                $bootstrap_enqueued = false;
                foreach ( $wp_scripts->registered as $scripts ) {
                    if ( stristr( $scripts->src, 'bootstrap.bundle.min.js' ) !== false && wp_script_is( $scripts->handle, 'enqueued' ) ) {
                        $bootstrap_enqueued = true;
                        break;
                    }
                }

                // Load getbootstrap bundle with popper.
                if ( ! $bootstrap_enqueued ) {
                    wp_enqueue_script( 'wwp-bootstrap-bundle-min', WWP_JS_URL . 'lib/bootstrap/bootstrap.bundle.min.js', array(), $this->_wwp_current_version, true );
                }
}

        }

        /**
         * Here we load our scripts to be use by Store Management Quick links
         *
         * @since 2.1.2
         */
        public function wwp_store_management_link_script() {
            // We check if all plugins are activated, so we can hide Upgrade Wholesale Suite link in the WooCommerce > Home > Store Management Quick links.
            $has_all_premiums = 'false';
            if ( WWP_Helper_Functions::is_wwpp_active() && WWP_Helper_Functions::is_wwlc_active() && WWP_Helper_Functions::is_wwof_active() ) {
                $has_all_premiums = 'true';
            }

            wp_enqueue_script( 'wwp-store-management-quick-link-js', WWP_JS_URL . 'app/wc-store-management-quick-links/build/static/js/wwp-store-management-quick-links.js', array( 'wp-hooks' ), $this->_wwp_current_version, false );

            wp_localize_script(
                'wwp-store-management-quick-link-js',
                'options',
                array(
					'has_all_premiums' => $has_all_premiums,
                )
            );
        }

        /**
         * This is use in the Store Management Quick links under WooCommerce > Home, in the Upgrade Wholesale Suite button under EXTENSIONS category, for the link, currently WooCommerce Store Management Quick links, does not accept "External Links", if you directly input the external links it will hide the "Upgrade Wholesale Suite" menu inside Store Management.
         *
         * @since 2.1.2
         */
        public function wchome_wws_upgrade_to_premium() {
            if ( isset( $_GET['page'] ) && 'wchome-wws-upgrade' === $_GET['page'] ) { // phpcs:ignore
                wp_safe_redirect( 'https://wholesalesuiteplugin.com/bundle/?utm_source=wwp&utm_medium=upsell&utm_campaign=wchomeupgradelink' );
                exit;
            }
        }

        /**
         * Load scripts and style for showing wholesale price lite notice bar
         *
         * @since 2.1.2
         * @access public
         */
        public function load_wwp_admin_notice_bar_lite_styles_and_scripts() {
            if ( ! WWP_Notice_Bar::has_wws_premiums() ) {
                wp_enqueue_style( 'wwp-notice-bar-lite-css', WWP_CSS_URL . 'wwp-admin-notice-bar.css', array(), $this->_wwp_current_version, 'all' );
            }
        }

        /**
         * Load scripts and style for license upsell upgrade to premium
         *
         * @since 2.1.3
         * @access public
         */
        public function load_wws_license_upsell_upgrade_to_premium_styles_and_scripts() {
            if ( isset( $_GET['page'] ) && $_GET['page'] == 'wwc_license_settings' ) { // phpcs:ignore
                wp_enqueue_style( 'wws-wwp-license-upsell-upgrade-css', WWP_CSS_URL . 'backend/wwp-license-upsell-upgrade.css', array(), $this->_wwp_current_version, 'all' );
            }
        }

        /**
         * Execute model.
         *
         * @since 1.4.0
         * @access public
         */
        public function run() {
            add_action( 'wp_loaded', array( $this, 'wchome_wws_upgrade_to_premium' ), 10 );
            add_action( 'admin_enqueue_scripts', array( $this, 'load_back_end_styles_and_scripts' ), 10, 1 );
            add_action( 'wp_enqueue_scripts', array( $this, 'load_front_end_styles_and_scripts' ), 11 );
            add_action( 'admin_enqueue_scripts', array( $this, 'wwp_store_management_link_script' ), 10 );
            add_action( 'admin_enqueue_scripts', array( $this, 'load_wwp_admin_notice_bar_lite_styles_and_scripts' ), 10 );
            add_action( 'admin_enqueue_scripts', array( $this, 'load_wws_license_upsell_upgrade_to_premium_styles_and_scripts' ), 10 );
        }
    }
}
