/* global jQuery */
jQuery( document ).ready( function( $ ) {

    /**
     * Register wholesale price bulk action.
     * 
     * @since 1.2.9
     */
    function register_wholesale_price_bulk_action() {

        var wholesale_roles = wwp_custom_bulk_actions_params.wholesale_roles;

        function wholesale_role_wholesale_prices( event , data ) {

            var value = window.prompt( wwp_custom_bulk_actions_params.i18n_prompt_message );

            if ( value !== null || value !== undefined )
                data.value = value;

            return data;

        }

        for ( var role in wholesale_roles )
            if ( wholesale_roles.hasOwnProperty( role ) )
                $( 'select.variation_actions' ).on( role + "_wholesale_price_ajax_data" , wholesale_role_wholesale_prices );
        
    }

    register_wholesale_price_bulk_action();

    // When variation attribute changes, re-register wholesale price bulk action
    $( '#variable_product_options' ).on( 'reload' , function() {

        register_wholesale_price_bulk_action();

    } );

} );
