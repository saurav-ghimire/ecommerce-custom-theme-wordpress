/* global jQuery */
jQuery( document ).ready( function( $ ) {

    // Quick Edit Screen Mods
    $( ".wholesale-price-per-role-and-country-accordion" ).accordion( {
        collapsible: true,
        heightStyle: "content",
        active: false
    } );

    $( '#the-list' ).on( 'click' , '.editinline' , function() {

        /**
         * Here is the logic.
         *
         * When we add custom field for wholesale prices using the (woocommerce_product_quick_edit_end) action hook,
         * we don't have access to the post id, therefore we couldn't load the custom field value, so we need to find a way.
         *
         * We add custom wholesale price fields via hook above, load up the value of those fields as a hidden meta via the
         * (manage_product_posts_custom_column) action hook. Here we have access to the post id so we can get the ids.
         *
         * Then here, upon user clicking the quick edit link, we dynamically populate the value from the meta to the
         * wholesale custom price field. Works great :)
         *
         * On version 1.2.0 we added support for Aelia Currency Switcher Plugin.
         */

        /**
         * Extract the wholesale price custom field data and put it as a value for the wholesale price custom form field
         */
        inlineEditPost.revert();

        var post_id = jQuery( this ).closest( 'tr' ).attr( 'id' );

        post_id = post_id.replace( "post-" , "" );

        var $wwop_inline_data = jQuery( '#wholesale_prices_inline_' + post_id ),
            $base_currency = $wwop_inline_data.find( ".product_base_currency" );

        $wwop_inline_data.find( ".whole_price" )
            .each( function( index ) {

                if ( $base_currency.length > 0 ) {
                    // Aelia currency switcher plugin is installed and active

                    if ( jQuery( this ).attr( 'data-currencyCode' ) == $base_currency.text() ) {
                        // Base Currency

                        // We get the appropriate wholesale custom price field (note we outputed it with name that has the Currency code in it)
                        var $wholesale_price_field = jQuery( 'input[name="' + jQuery( this ).attr( 'data-wholesalePriceKeyWithCurrency' ) + '"]' , '.inline-edit-row' );

                        if ( $wholesale_price_field.length <= 0 ) // meaning we already modified the name, so we use the name with no currency instead
                            $wholesale_price_field = jQuery( 'input[name="' + jQuery( this ).attr( 'id' ) + '"]' , '.inline-edit-row' );

                        // Set the value
                        $wholesale_price_field.val( jQuery( this ).text() );

                        // We remove placeholder text
                        $wholesale_price_field.attr( 'placeholder' , '' );

                        // We change label text
                        $wholesale_price_field.siblings( '.title' ).html( $wholesale_price_field.siblings( '.title' ).html() + ' <em><b>Base Currency</b></em>' );

                        // Change name with the one with no currency code
                        $wholesale_price_field.attr( "name" , jQuery( this ).attr( 'id' ) );

                        // Move on top of the list
                        var $parent_section_container = $wholesale_price_field.closest( ".section-container" );
                        $wholesale_price_field.closest( "label" ).detach().prependTo( $parent_section_container );

                    } else
                        jQuery( 'input[name="' + jQuery( this ).attr( 'id' ) + '"]' , '.inline-edit-row' ).val( jQuery( this ).text() );

                } else
                    jQuery( 'input[name="' + jQuery( this ).attr( 'id' ) + '"]' , '.inline-edit-row' ).val( jQuery( this ).text() );

            } );

        /**
         * Only show wholesale price custom field for appropriate types of products (simple)
         */
        var $wc_inline_data = jQuery( '#woocommerce_inline_' + post_id ),
            product_type = $wc_inline_data.find( '.product_type' ).text(),
            allowed_product_types = jQuery( '.wholesale_price_fields_allowed_product_types_' + post_id ).data( 'product_types' );

        if ( jQuery.inArray( product_type , allowed_product_types ) >= 0 ) {
            jQuery( '.quick_edit_wholesale_prices' , '.inline-edit-row' ).show();
        } else {
            jQuery( '.quick_edit_wholesale_prices' , '.inline-edit-row' ).hide();
        }

    } );

} );
