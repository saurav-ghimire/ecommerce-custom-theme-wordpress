<?php

namespace DgoraWcas\Integrations;

use  DgoraWcas\Helpers ;
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Class Solver
 *
 * Solve conflicts with other plugins
 */
class Solver
{
    public function __construct()
    {
        $this->solveSearchWPWooCommerceIntegration();
        $this->solveDiviWithBuilderWC();
        $this->solveMedicorCoreScrips();
        $this->solveGeoTargetingWPScripts();
        $this->solveEmptyImages();
    }
    
    /**
     * Solves conflict with SearchWP WooCommerce Integration by SearchWP, LLC
     * Tested version: plugin SearchWP WooCommerce Integration by SearchWP v1.2.1
     *
     * Reason: Empty search page (no results). The plugin removes query_vars['s']
     *
     * @return void
     */
    public function solveSearchWPWooCommerceIntegration()
    {
        
        if ( isset( $_GET['dgwt_wcas'] ) ) {
            add_filter( 'searchwp_woocommerce_forced', '__return_false', PHP_INT_MAX );
            add_filter( 'searchwp_short_circuit', '__return_true', PHP_INT_MAX );
        }
    
    }
    
    /**
     * Solves conflict with the DIVI builder
     * Tested version: theme DIVI v3.19.18
     *
     * Reason: WP Query for search results was overwritten ih the hook pre_get_posts
     */
    public function solveDiviWithBuilderWC()
    {
        add_action( 'init', function () {
            if ( isset( $_GET['dgwt_wcas'] ) ) {
                remove_action( 'pre_get_posts', 'et_builder_wc_pre_get_posts', 10 );
            }
        } );
    }
    
    /**
     * Medicor plugin by WpOpal uses wp_dequeue_style( 'dgwt-wcas-style' ); in their code.
     * I don't know why they block my CSS, but I have to force to restore it.
     */
    private function solveMedicorCoreScrips()
    {
        if ( class_exists( 'MedicorCore' ) ) {
            add_action( 'wp_print_styles', function () {
                wp_enqueue_style( 'dgwt-wcas-style' );
            }, PHP_INT_MAX );
        }
    }
    
    /**
     * Preventing the GeoTargetingWP plugin from loading scripts in the settings page
     * because the Selectize.js script is loaded twice
     *
     * @return void
     */
    public function solveGeoTargetingWPScripts()
    {
        if ( !Helpers::isSettingsPage() ) {
            return;
        }
        add_action( 'admin_enqueue_scripts', function () {
            wp_dequeue_script( 'geot' );
            wp_dequeue_script( 'geot-chosen' );
            wp_dequeue_script( 'geot-selectize' );
        }, 999 );
    }
    
    /**
     * Preventing empty image URLs (null) from being passed to the indexer
     *
     * @return void
     */
    public function solveEmptyImages()
    {
        add_filter(
            'dgwt/wcas/product/thumbnail_src',
            function ( $url, $id, $product ) {
            return ( empty($url) ? wc_placeholder_img_src() : $url );
        },
            PHP_INT_MAX - 5,
            3
        );
        add_filter(
            'dgwt/wcas/variation/thumbnail_src',
            function ( $url, $parentID, $variationID ) {
            return ( empty($url) ? wc_placeholder_img_src() : $url );
        },
            PHP_INT_MAX - 5,
            3
        );
        add_filter(
            'dgwt/wcas/term/thumbnail_src',
            function (
            $url,
            $termID,
            $size,
            $term
        ) {
            return ( empty($url) ? wc_placeholder_img_src() : $url );
        },
            PHP_INT_MAX - 5,
            4
        );
    }

}