/* global JQuery */
jQuery( document).ready( function( $ ) {

    // For Variable products. Only triggered on variable products.
    $( document.body ).on( 'woocommerce_variations_loaded' , '#woocommerce-product-data' , function() {

        $( ".wholesale-price-per-role-and-country-accordion" ).accordion( {
            collapsible: true,
            heightStyle: "content"
        } );

    });

    // For simple products. Triggered too on variable products but has no effect.
    $( ".wholesale-price-per-role-and-country-accordion" ).accordion( {
        collapsible: true,
        heightStyle: "content"
    } );

} );
