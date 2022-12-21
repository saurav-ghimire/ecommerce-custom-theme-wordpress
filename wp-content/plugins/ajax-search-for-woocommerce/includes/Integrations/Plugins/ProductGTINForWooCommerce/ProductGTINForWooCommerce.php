<?php

namespace DgoraWcas\Integrations\Plugins\ProductGTINForWooCommerce;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Integration with Product GTIN (EAN, UPC, ISBN) for WooCommerce
 *
 * Plugin URL: https://wordpress.org/plugins/product-gtin-ean-upc-isbn-for-woocommerce/
 * Author: Emanuela Castorina
 */
class ProductGTINForWooCommerce
{
    /**
     * @var string EAN field key
     */
    private  $eanField = '_wpm_gtin_code' ;
    public function init()
    {
        if ( !defined( 'WPM_PRODUCT_GTIN_WC_VERSION' ) ) {
            return;
        }
        if ( version_compare( WPM_PRODUCT_GTIN_WC_VERSION, '1.1' ) < 0 ) {
            return;
        }
        if ( !function_exists( 'wpm_product_gtin_wc' ) ) {
            return;
        }
        // Disable plugin hook on WP_Query.
        if ( !is_admin() ) {
            
            if ( isset( wpm_product_gtin_wc()->frontend ) && get_option( 'wpm_pgw_search_by_code', 'no' ) === 'yes' ) {
                remove_action( 'pre_get_posts', array( wpm_product_gtin_wc()->frontend, 'extend_product_search' ), 10 );
                
                if ( !dgoraAsfwFs()->is_premium() ) {
                    add_filter( 'dgwt/wcas/native/search_query/join', array( $this, 'searchQueryJoin' ) );
                    add_filter(
                        'dgwt/wcas/native/search_query/search_or',
                        array( $this, 'searchQueryOr' ),
                        10,
                        2
                    );
                }
            
            }
        
        }
    }
    
    /**
     * Prepare join for EAN lookup
     *
     * @param string $join
     *
     * @return string
     */
    public function searchQueryJoin( $join )
    {
        global  $wpdb ;
        if ( !strpos( $join, 'dgwt_wcasmsku' ) !== false ) {
            $join .= " INNER JOIN {$wpdb->postmeta} AS dgwt_wcasmean ON ( {$wpdb->posts}.ID = dgwt_wcasmean.post_id )";
        }
        return $join;
    }
    
    /**
     * Inject EAN lookup into search query
     *
     * @param string $search
     * @param string $like
     *
     * @return string
     */
    public function searchQueryOr( $search, $like )
    {
        global  $wpdb ;
        
        if ( strpos( $search, 'dgwt_wcasmsku' ) !== false ) {
            $search .= $wpdb->prepare( " OR (dgwt_wcasmsku.meta_key=%s AND dgwt_wcasmsku.meta_value LIKE %s)", $this->eanField, $like );
        } else {
            $search .= $wpdb->prepare( " OR (dgwt_wcasmean.meta_key=%s AND dgwt_wcasmean.meta_value LIKE %s)", $this->eanField, $like );
        }
        
        return $search;
    }

}