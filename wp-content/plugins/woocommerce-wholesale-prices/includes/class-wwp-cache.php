<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'WWP_Cache' ) ) {

    /**
     * Model that houses logic relating to caching.
     *
     * @since 1.6.0
     */
    class WWP_Cache {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of WWP_Cache.
         *
         * @since 1.6.0
         * @access private
         * @var WWP_Cache
         */
        private static $_instance;
        



        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
        */

        /**
         * WWP_Cache constructor.
         *
         * @since 1.6.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Cache model.
         */
        public function __construct( $dependencies ) {}
        
        /**
         * Ensure that only one instance of WWP_Cache is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.6.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Cache model.
         * @return WWP_Cache
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }




        /*
        |-------------------------------------------------------------------------------------------------------------------
        | Hashing
        |-------------------------------------------------------------------------------------------------------------------
        */

        /**
         * Set settings meta hash.
         * 
         * @since 1.6.0
         * @access public
         */
        public function set_settings_meta_hash() {

            update_option( 'wwp_settings_hash' , apply_filters( 'wwp_settings_hash' , uniqid( '' , true ) ) );

        }

        /**
         * Set product category meta hash.
         * 
         * @since 1.6.0
         * @access public
         * 
         * @param int    $term_id          Term Id.
         * @param int    $taxonomy_term_id Taxonomy term id.
         * @param string $taxonomy         Taxonomy
         */
        public function set_product_category_meta_hash( $term_id , $taxonomy_term_id , $taxonomy = 'product_cat' ) {

            if ( $taxonomy === 'product_cat' )
                update_option( 'wwp_product_cat_hash' , apply_filters( 'wwp_product_cat_hash' , uniqid( '' , true ) , $term_id , $taxonomy_term_id , $taxonomy ) );

        }

        /**
         * Set product category meta hash.
         * 
         * @since 1.6.0
         * @access public
         * 
         * @param int    $term_id          Term Id.
         * @param int    $taxonomy_term_id Taxonomy term id.
         * @param object $deleted_term     Deleted term object.
         * @param array  $object_ids       List of term object ids.
         */
        public function set_product_category_meta_hash_delete_term( $term_id , $taxonomy_term_id , $deleted_term , $object_ids ) {

            $this->set_product_category_meta_hash( $taxonomy_term_idterm_id , $taxonomy_term_id , 'product_cat' );

        }

        /**
         * Set product meta hash.
         * 
         * @since 1.6.0
         * @access public
         * 
         * @param int $post_id Post id.
         */
        public function set_product_meta_hash( $post_id ) {

            if ( WWP_Helper_Functions::check_if_valid_save_post_action( $post_id , 'product' ) )
                update_post_meta( $post_id , 'wwp_product_hash' , apply_filters( 'wwp_product_hash' , uniqid( '' , true ) , $post_id ) );

        }
        

        

        /*
        |-------------------------------------------------------------------------------------------------------------------
        | Public Functions
        |-------------------------------------------------------------------------------------------------------------------
        */

        public function check_variable_product_price_range_cache_if_valid( $user_id , $product , $cache_data ) {

            if ( WWP_Helper_Functions::wwp_get_product_type( $product ) === "variable" )
                return get_option( 'wwp_settings_hash' ) === $cache_data[ 'wwp_settings_hash' ] && get_option( 'wwp_product_cat_hash' ) === $cache_data[ 'wwp_product_cat_hash' ] && get_option( 'wwp_product_hash' ) === $cache_data[ 'wwp_product_hash' ];

            return false;

        }

        public function set_variable_product_price_range_cache( $user_id , $product , $args ) {

            // TODO: Add filter for extensibility

            if ( WWP_Helper_Functions::wwp_get_product_type( $product ) === "variable" ) {

                $product_id = WWP_Helper_Functions::wwp_get_product_id( $product );

                $user_cached_data = get_user_meta( $user_id , 'wwpp_variable_product_price_range_cache' , true );
                if ( !is_array( $user_cached_data ) )
                    $user_cached_data = array();

                $harhes_arr = array(
                    'wwp_settings_hash'    => get_option( 'wwp_settings_hash' ),
                    'wwp_product_cat_hash' => get_option( 'wwp_product_cat_hash' ),
                    'wwp_product_hash'     => get_option( 'wwp_product_hash' )
                );

                $cache_data = wp_parse_args( $args , $harhes_arr );

                $user_cached_data[ $product_id ] = $cache_data;

            }

        }

        public function get_cache_variable_product_price_range_cache( $user_id , $product ) {

            if ( WWP_Helper_Functions::wwp_get_product_type( $product ) === "variable" ) {

                $product_id = WWP_Helper_Functions::wwp_get_product_id( $product );

                $user_cached_data = get_user_meta( $user_id , 'wwpp_variable_product_price_range_cache' , true );
                if ( !is_array( $user_cached_data ) )
                    $user_cached_data = array();

                return array_key_exists( $product_id , $user_cached_data ) ? $user_cached_data[ $product_id ] : false;

            }

            return false;

        }




        /*
        |-------------------------------------------------------------------------------------------------------------------
        | Execute Model
        |-------------------------------------------------------------------------------------------------------------------
        */

        /**
         * Execute model.
         *
         * @since 1.6.0
         * @access public
         */
        public function run() {

            // On every product category change, WC settings change and Product update, we create new hashes
            add_action( 'woocommerce_settings_saved' , array( $this , 'set_settings_meta_hash' )                     , 10 );
            add_action( 'created_product_cat'        , array( $this , 'set_product_category_meta_hash' )             , 10 , 2 ); // New Product Cat
            add_action( 'edit_term'                  , array( $this , 'set_product_category_meta_hash' )             , 10 , 3 ); // Edit Product Cat
            add_action( 'delete_product_cat'         , array( $this , 'set_product_category_meta_hash_delete_term' ) , 10 , 4 ); // Delete Product Cat
            add_action( 'save_post'                  , array( $this , 'set_product_meta_hash' )                      , 10 , 1 );

        }

    }

}