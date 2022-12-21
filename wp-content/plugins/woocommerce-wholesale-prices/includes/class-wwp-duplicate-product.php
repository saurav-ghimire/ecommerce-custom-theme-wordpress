<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WWP_Duplicate_Product' ) ) {

    /**
     * Model that houses the logic of integrating with WooCommerce duplicate product function.
     *
     * @since 1.14.4
     */
    class WWP_Duplicate_Product {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of WWP_Bootstrap.
         *
         * @since 1.14.4
         * @access private
         * @var WWP_Duplicate_Product
         */
        private static $_instance;

        /**
         * Model that houses the logic of retrieving information relating to wholesale role/s of a user.
         *
         * @since 1.14.4
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
         * WWP_Duplicate_Product constructor.
         *
         * @since 1.14.4
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Bootstrap model.
         */
        public function __construct( $dependencies ) {

            $this->_wwp_wholesale_roles = $dependencies[ 'WWP_Wholesale_Roles' ];

        }

        /**
         * Ensure that only one instance of WWP_Duplicate_Product is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.14.4
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Duplicate_Product model.
         * @return WWP_Duplicate_Product
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Run product duplicate main function for WWP
         *
         * @since 1.14.4
         * @access public
         *
         * @param WC_Product $duplicate     product object. the duplicated product
         * @param WC_Product $product       product object. product to duplicate
         */
        public function wwp_run_product_duplicate( $duplicate , $product ) {

        	$duplicate_id    = WWP_Helper_Functions::wwp_get_product_id( $duplicate );
        	$product_id      = WWP_Helper_Functions::wwp_get_product_id( $product );
            $wholesale_roles = $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles();

            // duplicate general meta data
            $this->wwp_duplicate_meta( $duplicate_id , $product_id );

            // duplicate role based meta data
            $this->wwp_duplicate_role_based_meta( $wholesale_roles , $duplicate_id , $product_id );

            // duplicate variable product
            $this->wwp_duplicate_variable_product( $duplicate , $product , $duplicate_id , $product_id , $wholesale_roles );

            // action hook to support WWPP and third party plugins
            do_action( 'wwp_run_product_duplicate' , $duplicate_id , $product_id , $duplicate , $product );

        }

        /**
         * Duplicate general meta data
         *
         * @since 1.14.4
         * @access public
         *
         * @param int   $duplicate_id   ID of the duplicated product
         * @param int   $product_id     ID of the product to duplicate
         */
        function wwp_duplicate_meta( $duplicate_id , $product_id ) {

            $wwp_meta  = apply_filters( 'wwp_duplicate_meta', array() );

            if ( empty( $wwp_meta ) )
                return;

        	foreach ( $wwp_meta as $meta ) {

        		if ( $value = get_post_meta( $product_id , $meta , true ) )
        			update_post_meta( $duplicate_id , $meta , $value );
        	}
        }

        /**
         * Duplicate role based meta data
         *
         * @since 1.14.4
         * @access public
         *
         * @param Array $wholesale_roles    list of registered wholesale roles
         * @param int   $duplicate_id       ID of the duplicated product
         * @param int   $product_id         ID of the product to duplicate
         */
        function wwp_duplicate_role_based_meta( $wholesale_roles , $duplicate_id , $product_id ) {

            $wholesale_meta  = apply_filters( 'wwp_duplicate_role_based_meta',
                array(
            		'_wholesale_price',
            		'_have_wholesale_price'
            	)
            );

        	if ( ! empty( $wholesale_roles ) ) {

                foreach ( $wholesale_roles as $key => $role_data ) {

            		foreach ( $wholesale_meta as $meta ) {

            			if ( $value = get_post_meta( $product_id, $key . $meta , true ) )
            				update_post_meta( $duplicate_id , $key . $meta , $value );

            		}
            	}
            }

        }

        /**
         * Run product duplicate main function for WWP
         *
         * @since 1.14.4
         * @access public
         *
         * @param WC_Product    $duplicate          product object. the duplicated product
         * @param WC_Product    $product            product object. product to duplicate
         * @param int           $duplicate_id       ID of the duplicated product
         * @param int           $product_id         ID of the product to duplicate
         * @param Array         $wholesale_roles    list of registered wholesale roles
         */
        function wwp_duplicate_variable_product( $duplicate , $product , $duplicate_id , $product_id , $wholesale_roles ) {

            if ( WWP_Helper_Functions::wwp_get_product_type( $duplicate ) != 'variable' || WWP_Helper_Functions::wwp_get_product_type( $product ) != 'variable' )
                return;

            // handle duplicate for each variation
            $product_children = $product->get_children();

            if ( ! empty( $product_children ) ) {

                foreach ( $product_children as $product_variation_id ) {

            		$product_variation 		   = wc_get_product( $product_variation_id );
            		$duplicate_variation_id    = WWP_Helper_Functions::wwp_get_matching_variation( $duplicate , $product_variation->get_variation_attributes() );

                    // duplicate general meta data
                    $this->wwp_duplicate_meta( $duplicate_variation_id , $product_variation_id );

                    // duplicate role based meta data
                    $this->wwp_duplicate_role_based_meta( $wholesale_roles , $duplicate_variation_id , $product_variation_id );

                    // create _variations_with_wholesale_price meta on the variable product
                    foreach ( $wholesale_roles as $key => $role_data ) {

                        if ( get_post_meta( $duplicate_variation_id , $key . '_wholesale_price' , true ) )
                            add_post_meta( $duplicate_id , $key . '_variations_with_wholesale_price' , $duplicate_variation_id );
                    }

                    // action hook to support WWPP and third party plugins
                    do_action( 'wwp_duplicate_variation' , $duplicate_variation_id , $product_variation_id );
            	}
            }

            // action hook to support WWPP and third party plugins
            do_action( 'wwp_duplicate_variable_product' , $duplicate , $product , $duplicate_id , $product_id , $wholesale_roles );
        }




        /*
         |------------------------------------------------------------------------------------------------------------------
         | Execute Model
         |------------------------------------------------------------------------------------------------------------------
         */

        /**
         * Execute model.
         *
         * @since 1.14.4
         * @access public
         */
        function run() {

            // Save wholesale data on product duplicate
            add_action( 'woocommerce_product_duplicate' , array( $this , 'wwp_run_product_duplicate' ) , 10 , 2 );
        }

    }
}
