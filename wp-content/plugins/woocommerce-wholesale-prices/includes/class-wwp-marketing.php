<?php if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly

if ( ! class_exists( 'WWP_Marketing' ) ) {

    /**
     * Model that houses the logic of integrating with WooCommerce Marketing page.
     *
     * @since 1.5.0
     */
    class WWP_Marketing {


        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
         */

        /**
         * Property that holds the single main instance of WWP_Marketing.
         *
         * @since 1.5.0
         * @access private
         * @var WWP_Marketing
         */
        private static $_instance;

        /**
         * Model that houses the logic of retrieving information relating to wholesale role/s of a user.
         *
         * @since 1.5.0
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
         * WWP_Marketing constructor.
         *
         * @since 1.5.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Marketing model.
         */
        public function __construct( $dependencies ) {
            $this->_wwp_wholesale_roles = $dependencies['WWP_Wholesale_Roles'];
        }

        /**
         * Ensure that only one instance of WWP_Marketing is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.5.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Marketing model.
         * @return WWP_Marketing
         */
        public static function instance( $dependencies ) {
            if ( ! self::$_instance instanceof self ) {
                self::$_instance = new self( $dependencies );
            }

            return self::$_instance;
        }

        /**
         * Insert ACFW and WWS as recommended plugins in WooCommerce Marketing page.
         * Conditions:  Add ACFW as recommended if ACFW is not installed.
         *              Add WWS as recommended if only WWP is the only installed plugin.
         *
         * @since 1.11.5
         * @access public
         *
         * @param array $recommended_plugins List of recommended plugins.
         * @return array Filtered list of recommended plugins.
         */
        public function filter_wc_marketing_recommended_plugins( $recommended_plugins ) {
            // if WC hasn't fetched their data yet or transient has expired, then we shouldn't append our data yet.
            if ( false === $recommended_plugins ) {
                remove_filter( 'transient_wc_marketing_recommended_plugins', array( $this, 'filter_wc_marketing_recommended_plugins' ) );
                $wcmarketing         = new \Automattic\WooCommerce\Admin\Features\Marketing();
                $recommended_plugins = $wcmarketing->get_recommended_plugins();
            }

            // If WWP is the only installed plugin from WWS Bundle
            if (
                ! WWP_Helper_Functions::is_wwpp_installed() &&
                ! WWP_Helper_Functions::is_wwof_installed() &&
                ! WWP_Helper_Functions::is_wwlc_installed() &&
                WWP_Helper_Functions::is_wwp_installed()
            ) {

                $wws_check = ! empty( $recommended_plugins ) ? array_filter(
                    $recommended_plugins,
                    function ( $plugin ) {
                    return ( $plugin['product'] === 'wholesale-suite' );
                    }
                ) : array();

                if ( empty( $wws_check ) && ! empty( $recommended_plugins ) ) {

                    array_unshift(
                        $recommended_plugins,
                        array(
							'title'       => __( 'Wholesale Suite for WooCommerce', 'woocommerce-wholesale-prices' ),
							'description' => __( 'Get the #1 rated wholesale solution for WooCommerce.', 'woocommerce-wholesale-prices' ),
							'url'         => 'https://wholesalesuiteplugin.com/bundle/?utm_source=wwp&utm_medium=wcmarketing&utm_campaign=wcmarketingwwsbundleupsell',
							'icon'        => WWP_IMAGES_URL . 'wws-marketing-logo.png',
							'product'     => 'wholesale-suite',
							'plugin'      => 'wholesale-suite',
							'categories'  => array( 'marketing' ),
                        )
                    );
                }
            }

            // If ACFW is not installed we add it in the recommended
            if ( ! WWP_Helper_Functions::is_acfwf_installed() ) {

                $acfw_check = ! empty( $recommended_plugins ) ? array_filter(
                    $recommended_plugins,
                    function ( $plugin ) {
                    return ( $plugin['product'] === 'woocommerce-wholesale-prices' );
                    }
                ) : array();

                if ( empty( $acfw_check ) && ! empty( $recommended_plugins ) ) {

                    array_unshift(
                        $recommended_plugins,
                        array(
							'title'       => __( 'Advanced Coupons (Install Free Plugin)', 'woocommerce-wholesale-prices' ),
							'description' => __( 'Extends your coupon features so you can market your store better.', 'woocommerce-wholesale-prices' ),
							'url'         => htmlspecialchars_decode( wp_nonce_url( 'update.php?action=install-plugin&plugin=advanced-coupons-for-woocommerce-free', 'install-plugin_advanced-coupons-for-woocommerce-free' ) ),
							'icon'        => WWP_IMAGES_URL . 'acfw-marketing-logo.png',
							'product'     => 'woocommerce-wholesale-prices',
							'plugin'      => 'advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php',
							'categories'  => array( 'coupons', 'marketing' ),
                        )
                    );
                }
            }

            return $recommended_plugins;
        }

        /**
         * Insert ACFW and WWS ebook under WooCommerce Knowledge Base section in WooCommerce Marketing page.
         * Conditions:  Add ACFW as recommended if ACFW is not installed.
         *              Add WWS as recommended if only WWP is the only installed plugin.
         *
         * @since 1.11.5
         * @access public
         *
         * @param array $recommended_plugins List of recommended plugins.
         * @return array Filtered list of recommended plugins.
         */
        public function filter_wc_marketing_knowledge_base( $knowledge_base ) {
            // if WC hasn't fetched their data yet or transient has expired, then we shouldn't append our data yet.
            if ( false === $knowledge_base ) {
                remove_filter( 'transient_wc_marketing_knowledge_base', array( $this, 'filter_wc_marketing_knowledge_base' ) );
                $wcmarketing = new \Automattic\WooCommerce\Admin\Features\Marketing();

                if ( WWP_Helper_Functions::is_wc_four_point_three_and_up() ) {
                    $knowledge_base = $wcmarketing->get_knowledge_base_posts( '' );
                } else {
                    $knowledge_base = $wcmarketing->get_knowledge_base_posts();
                }
            }

            $acfw_ebook_check = ! empty( $knowledge_base ) ? array_filter(
                $knowledge_base,
                function ( $kb ) {
                return ( isset( $kb['id'] ) && $kb['id'] === 'acfwebook' );
                }
            ) : array();

            if ( empty( $acfw_ebook_check ) ) {
                array_unshift(
                    $knowledge_base,
                    array(
                        'id'            => 'acfwebook',
                        'title'         => __( 'How To Grow A WooCommerce Store Using Coupon Deals', 'woocommerce-wholesale-prices' ),
                        'date'          => date( 'Y-m-d\TH:i:s', time() ),
                        'link'          => 'https://advancedcouponsplugin.com/how-to-grow-your-woocommerce-store-with-coupons/?utm_source=wwp&utm_medium=wcmarketing&utm_campaign=knowledgebase',
                        'author_name'   => 'Josh Kohlbach',
                        'author_avatar' => 'https://secure.gravatar.com/avatar/2f2da8c07f7031a969ae1bd233437a29?s=32&amp;d=mm&amp;r=g',
                        'image'         => WWP_IMAGES_URL . 'acfw-free-ebook.png',
                    )
                );
            }

            $wws_ebook_check = ! empty( $knowledge_base ) ? array_filter(
                $knowledge_base,
                function ( $kb ) {
                return ( isset( $kb['id'] ) && $kb['id'] === 'wwsebook' );
                }
            ) : array();

            if ( empty( $wws_ebook_check ) ) {
                array_unshift(
                    $knowledge_base,
                    array(
                        'id'            => 'wwsebook',
                        'title'         => __( 'How To Setup Wholesale On Your WooCommerce Store', 'woocommerce-wholesale-prices' ),
                        'date'          => date( 'Y-m-d\TH:i:s', time() ),
                        'link'          => 'https://wholesalesuiteplugin.com/free-guide/?utm_source=wwp&utm_medium=wcmarketing&utm_campaign=knowledgebase',
                        'author_name'   => 'Josh Kohlbach',
                        'author_avatar' => 'https://secure.gravatar.com/avatar/2f2da8c07f7031a969ae1bd233437a29?s=32&amp;d=mm&amp;r=g',
                        'image'         => WWP_IMAGES_URL . 'wws-free-ebook.png',
                    )
                );
            }

            return $knowledge_base;
        }

        /**
         * Print wwp tag.
         *
         * @since 1.5.0
         * @access public
         */
        public function print_wwp_tag() {
            echo '<meta name="wwp" content="yes" />';
        }

        /**
         * Flag to show review request.
         *
         * @since 3.0.0
         * @access public
         */
        public function flag_show_review_request() {
            update_option( WWP_SHOW_REQUEST_REVIEW, 'yes' );
        }

        /**
         * Set flag to show acfwf install notice.
         *
         * @since 1.15.5
         * @access public
         */
        public function flag_show_install_acfwf_notice() {
            update_option( WWP_SHOW_INSTALL_ACFWF_NOTICE, 'yes' );
        }

        /**
         * Set flag to hide acfwf install notice.
         *
         * @since 1.15.5
         * @access public
         */
        public function wwp_hide_acfwf_install_notice() {
            if ( ! defined( 'DOING_AJAX' ) && ! wp_verify_nonce( $_POST['nonce'], 'wwp_hide_acfwf_install_notice_nonce' ) ) {
                // Security check failure.
                return;
            }
            update_option( WWP_SHOW_INSTALL_ACFWF_NOTICE, 'no' );
        }

        /**
         * Display install ACFWF notice after 30 days of WWP activation.
         *
         * @since 1.15.5
         * @since 1.12      Don't show notice in products listing.
         * @access public
         */
        public function install_acfwf_notice() {
            if ( isset( $_GET['action'] ) && $_GET['action'] === 'install-plugin' ) {
                return;
            }

            if ( current_user_can( 'administrator' ) && get_option( WWP_SHOW_INSTALL_ACFWF_NOTICE ) === 'yes' ) {

                if ( ! WWP_Helper_Functions::is_acfwf_installed() ) {

                    $screen = get_current_screen();
                    // Check if WWS license page
                    // Check if woocommerce pages ( orders, settings )
                    // Check if plugins page
                    if ( $screen->id === 'settings_page_wwc_license_settings' || in_array( $screen->parent_base, array( 'woocommerce', 'plugins' ) ) ) {
                        ?>

                        <div class="updated notice acfwf-cross-sell">
                            <div class="acfwf-logo">
                                <img src="<?php echo WWP_IMAGES_URL; ?>acfwf-logo.png" alt="" />
                                <h2>
                                    <?php _e( 'FREE PLUGIN AVAILABLE', 'woocommerce-wholesale-prices' ); ?>
                                </h2>
                            </div>

                            <p class="message"><?php _e( 'Hey store owner! Do you use coupons to promote your WooCommerce store? Wholesale Suite has a sister plugin called <b>Advanced Coupons</b> which extends the features of your WooCommerce coupons. Best of all, it\'s free! You can run BOGO deals, apply cart conditions (coupon rules), restrict coupons by role (including wholesale roles) and loads more!', 'woocommerce-wholesale-prices' ); ?>
                            <p><b><?php _e( 'Click here to install Advanced Coupons for WooCommerce Free', 'woocommerce-wholesale-prices' ); ?></b></p>
                            <p>
                                <a href="<?php echo wp_nonce_url( 'update.php?action=install-plugin&plugin=advanced-coupons-for-woocommerce-free', 'install-plugin_advanced-coupons-for-woocommerce-free' ); ?>" class="install-plugin">
                                    <?php _e( 'Install Plugin', 'woocommerce-wholesale-prices' ); ?>
                                </a>
                                <a href="#" class="acfwf-notice-dismiss"><?php _e( 'Dismiss', 'woocommerce-wholesale-prices' ); ?></a>
                            </p>
                        </div>
                        <?php

                    }
                }
            }
        }

        /**
         * Ajax request review response.
         *
         * @since 1.5.0
         * @access public
         */
        public function ajax_request_review_response() {

            if ( ! defined( 'DOING_AJAX' ) && ! wp_verify_nonce( $_POST['nonce'], 'wwp_request_review_nonce' ) ) {
                $response = array(
					'status'    => 'fail',
					'error_msg' => __( 'Security check failure', 'woocommerce-wholesale-prices' ),
				);
                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) ); // phpcs:ignore
                echo wp_json_encode( $response );
                wp_die();

            } elseif ( ! isset( $_POST['review_request_response'] ) ||
                ! in_array( $_POST['review_request_response'], array( 'review-later', 'review', 'never-show' ), true ) ) {

                $response = array(
					'status'    => 'fail',
					'error_msg' => __( 'Required parameter not passed', 'woocommerce-wholesale-prices' ),
				);
                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) ); // phpcs:ignore
                echo wp_json_encode( $response );
                wp_die();

            } else {
                // Sanitize.
                $review_request_response = sanitize_text_field( $_POST['review_request_response'] );

                switch ( $review_request_response ) {
                    case 'review-later':
                        // Schedule to show pop up next 14 days if Review Later is clicked.
                        wp_schedule_single_event( strtotime( '+14 days' ), WWP_CRON_REQUEST_REVIEW );

                        delete_option( WWP_SHOW_REQUEST_REVIEW );
                        break;
                    case 'review':
                    case 'never-show':
                        update_option( WWP_REVIEW_REQUEST_RESPONSE, $review_request_response );
                        break;
                    default:
                        break;
                }

                $response = array(
					'status'      => 'success',
					'success_msg' => __( 'Review request response saved', 'woocommerce-wholesale-prices' ),
				);
            }

            @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) ); // phpcs:ignore
            echo wp_json_encode( $response );
            wp_die();
        }

        /**
         * Register ajax handlers.
         *
         * @since 1.5.0
         * @access public
         */
        public function register_ajax_handlers() {
            add_action( 'wp_ajax_wwp_request_review_response', array( $this, 'ajax_request_review_response' ) );
            add_action( 'wp_ajax_wwp_hide_acfwf_install_notice', array( $this, 'wwp_hide_acfwf_install_notice' ) );
        }

        /**
         * Execute model.
         *
         * @since 1.5.0
         * @access public
         */
        public function run() {
            // Show review request
            add_action( 'wp_head', array( $this, 'print_wwp_tag' ) );
            add_action( WWP_CRON_REQUEST_REVIEW, array( $this, 'flag_show_review_request' ) );
            add_action( 'init', array( $this, 'register_ajax_handlers' ) );

            // Display install ACFWF notice after 30 days of WWP activation
            add_action( 'admin_notices', array( $this, 'install_acfwf_notice' ), 10 );
            add_action( WWP_CRON_INSTALL_ACFWF_NOTICE, array( $this, 'flag_show_install_acfwf_notice' ) );

            // Recommended plugins Section
            add_filter( 'transient_wc_marketing_recommended_plugins', array( $this, 'filter_wc_marketing_recommended_plugins' ) );

            // WC Marketing Section. Filter Changed in WC 4.3
            if ( WWP_Helper_Functions::is_wc_four_point_three_and_up() ) {
                add_filter( 'transient_wc_marketing_knowledge_base_marketing', array( $this, 'filter_wc_marketing_knowledge_base' ) );
            } else {
                add_filter( 'transient_wc_marketing_knowledge_base', array( $this, 'filter_wc_marketing_knowledge_base' ) );
            }
        }
    }
}
