/**
 * Percentage Wholesale Pricing on Product Level
 * 
 * @since 2.1.0
 */

 jQuery(document).ready(function($){
    var   $options                        = wwp_percentage_wholesale_options,
          $wholesale_roles                = Object.keys(JSON.parse(JSON.stringify($options.wholesale_roles))),
          $wholesale_price_old_data_map   = new Map(),
          $product_type                   = $('#product-type').val(), 
          $variable_product_variations    = ($product_type === 'simple') ? {length:0} : $('#variable_product_options_inner .woocommerce_variations');

    /**
     *  we will insert an entry to woocommerce_admin local variable, so we can take advantage of the error wc_error_tip of woocommerce.
     * 
     * @since 2.1.1
     */ 
    Object.assign(woocommerce_admin,{
        'i18n_discount_greater_than_100_percent_error'  : $options.i18n_discount_greater_than_100_percent_error,
        'i18n_discount_less_than_0_percent_error'       : $options.i18n_discount_less_than_0_percent_error
    });

    /**
     * This block of code runs only on variable product with variations, this will check if we have changes in the variations regular price.
     * 
     * @since 2.1.0
     * @since 2.1.5 Change the conditional logic when getting the index of the variation to avoid fecthing wrong price field
     */
    $('body').on('keyup','.wc_input_price', function(e){
        var regular_price             = $(this).val(),
            id                        = $(this).attr('id'),
            variation_loop_index_id   = '';
        
            $variable_product_variations = ($product_type === 'simple') ? {length:0} : $('#variable_product_options_inner .woocommerce_variations');

        if(id.match('^variable_regular_price') && $variable_product_variations.length > 0){
            variation_loop_index_id = id.match(/\d/g).join('');
        }

        if($(this).prop('class').indexOf('wholesale_price') < 0 && $(this).prop('class').indexOf('wholesale_discount') < 0 && (id !== '_sale_price' && id !== 'variable_sale_price' + variation_loop_index_id)){

            // Get the percentage wholesale price
            getPercentageWholesalePriceByRegularPrice(variation_loop_index_id, regular_price);

        }else{

            if($(this).prop('class').indexOf('wholesale_discount') < 0){
                $(this).attr('data-fixed_price', regular_price);
            }

        }

    });

    /**
     * Calculate the percentage wholesale price every time we change the value of the discount input field and if the
     * discount input field is not empty. 
     * 
     * @since 2.1.0
     * @since 2.1.1 
     * - Added validation of discount value (percent), the percent or discount value should not be greater than  100 and should not be less than zero.
     * - Added wc_error_tip if the discount input field is greater than 100, and if the discount input field is less than 0
     */
    $('body').on('keyup', '.wholesale_discount', function(e){
        $variable_product_variations = ($product_type === 'simple') ? {length:0} : $('#variable_product_options_inner .woocommerce_variations');

        var discount                        = $(this).val(),
            role                            = $(this).data('wholesale_role'),
            variation_loop_index_id         = $variable_product_variations.length > 0 ? $(this).data('loop_id') : '',
            regular_price_id                = $variable_product_variations.length > 0 ? 'variable_regular_price_' + variation_loop_index_id :      '_regular_price',
            price                           = getProductRegularPrice(regular_price_id),
            wholesale_price_field_id        = $variable_product_variations.length > 0 ? role + '_wholesale_prices\\[' + variation_loop_index_id + '\\]' : role + '_wholesale_price',
            el_wholesale_price_field        = $('#' + wholesale_price_field_id);


        discount = (discount !== "" && $options.decimal_sep !== '.') ? discount.toString().replace($options.decimal_sep,'.') : discount;

        if( parseFloat(discount) > 100 ){
            
            $( document.body ).triggerHandler( 'wc_add_error_tip', [ $(this), 'i18n_discount_greater_than_100_percent_error' ] );
        }else if(parseFloat(discount) == 100){

            $( document.body ).triggerHandler( 'wc_remove_error_tip', [ $(this), 'i18n_discount_greater_than_100_percent_error' ] );

            $( document.body ).triggerHandler( 'wc_remove_error_tip', [ $(this), 'i18n_discount_less_than_0_percent_error' ] );

            el_wholesale_price_field.val(0);

        }else if(parseFloat(discount) < 0){

            $( document.body ).triggerHandler( 'wc_add_error_tip', [ $(this), 'i18n_discount_less_than_0_percent_error' ] );

        }else if(parseFloat(discount) > 0){

            $( document.body ).triggerHandler( 'wc_remove_error_tip', [ $(this), 'i18n_discount_greater_than_100_percent_error' ] );

            $( document.body ).triggerHandler( 'wc_remove_error_tip', [ $(this), 'i18n_discount_less_than_0_percent_error' ] );

            var discounted_price = calculateDiscountedPrice(price, discount);
            discounted_price = removeTrailingZeros(discounted_price);

            el_wholesale_price_field.val(discounted_price);
        }else{
            $( document.body ).triggerHandler( 'wc_remove_error_tip', [ $(this), 'i18n_discount_less_than_0_percent_error' ] );

            el_wholesale_price_field.val('');
        }
        
    });

    /**
     * Calculate the percentage wholesale price every time the value is change and if you loss focus in the discount field input or if encounters an error on decimal separator or if the discount input field is greater than 100 or if the discount input field is less than 0.
     * 
     * @since 2.1.1 
     */
    $('body').on('change', '.wholesale_discount', function(e){
        $variable_product_variations = ($product_type === 'simple') ? {length:0} : $('#variable_product_options_inner .woocommerce_variations');

        var discount                        = $(this).val(),
            role                            = $(this).data('wholesale_role'),
            variation_loop_index_id         = $variable_product_variations.length > 0 ? $(this).data('loop_id') : '',
            regular_price_id                = $variable_product_variations.length > 0 ? 'variable_regular_price_' + variation_loop_index_id :      '_regular_price',
            price                           = getProductRegularPrice(regular_price_id),
            wholesale_price_field_id        = $variable_product_variations.length > 0 ? role + '_wholesale_prices\\[' + variation_loop_index_id + '\\]' : role + '_wholesale_price',
            el_wholesale_price_field        = $('#' + wholesale_price_field_id);

            discount = (discount !== "" && $options.decimal_sep !== '.') ? discount.toString().replace($options.decimal_sep,'.') : discount;

            if( parseFloat(discount) > 100 ){

                $(this).val( '' );
                el_wholesale_price_field.val('');

            }else if(parseFloat(discount) == 100){

                el_wholesale_price_field.val(0);

            }else if(parseFloat(discount) < 0){

                $(this).val( '' );
                el_wholesale_price_field.val('');
    
            }else if(parseFloat(discount) > 0){
    
                var discounted_price = calculateDiscountedPrice(price, discount);
                discounted_price = removeTrailingZeros(discounted_price);
    
                el_wholesale_price_field.val(discounted_price);

            }else{
                el_wholesale_price_field.val('');
            }
    });

    /**
     * This block of code is similar to wc_input_price for regular price text field on keyup, this will only be triggered when there is a change and losses it's focus in the regular price text field, where there is an error for the decimal separator or thousand separator. It will auto re-calcuate the wholesale price if the discount type is percentage.
     * 
     * @since 2.1.0
     * @since 2.1.5 Change the conditional logic when getting the index of the variation to avoid fecthing wrong price field
     */
    $('body').on('change','.wc_input_price', function(e){
        var regular_price             = $(this).val(),
            id                        = $(this).attr('id');
            variation_loop_index_id   = '';
        
            $variable_product_variations = ($product_type === 'simple') ? {length:0} : $('#variable_product_options_inner .woocommerce_variations');

        if((id.match('^variable_regular_price') || id.match('^variable_sale_price')) && $variable_product_variations.length > 0){
            variation_loop_index_id = id.match(/\d/g).join('');
        }

        if($(this).prop('class').indexOf('wholesale_price') < 0 && $(this).prop('class').indexOf('wholesale_discount') < 0 && (id !== '_sale_price' && id !== 'variable_sale_price' + variation_loop_index_id) ){

            getPercentageWholesalePriceByRegularPrice(variation_loop_index_id, regular_price);
        
        }else{

            if($(this).prop('class').indexOf('wholesale_discount') < 0){
                $(this).attr('data-fixed_price', regular_price);
            }

        }
    });

    /**
     * Calculate the percentage wholesale price if the selected type is percentage and only if the discount input field
     * is not empty. If the selected type is fixed then the previous value of the wholesale price will be used then
     * vise-versa with the discount input field.
     * 
     * @since 2.1
     */
    $('body').on('change', '.wholesale_discount_type', function(e) {
        $variable_product_variations = ($product_type === 'simple') ? {length:0} : $('#variable_product_options_inner .woocommerce_variations');


        var selected_discount_type              = $(this).val(),
            role                                = $(this).data('wholesale_role'),
            variation_loop_index_id             = $variable_product_variations.length > 0 ? $(this).data('loop_id') : '',
            discount_field_id                   = $variable_product_variations.length > 0 ? role + '_wholesale_percentage_discount\\[' + variation_loop_index_id + '\\]' : role + '_wholesale_percentage_discount',
            wholesale_price_field_id             = $variable_product_variations.length > 0 ? role + '_wholesale_prices\\[' + variation_loop_index_id + '\\]' : role + '_wholesale_price',
            class_discount_field                = $variable_product_variations.length > 0 ? '.form-field.' + role + '_wholesale_percentage_discount\\['+ variation_loop_index_id + '\\]_field' : '.form-field.' + role + '_wholesale_percentage_discount_field',
            form_wholesale_price_field          = $variable_product_variations.length > 0 ? '.form-field.' + role + '_wholesale_prices\\[' + variation_loop_index_id + '\\]_field' : '.form-field.' + role + '_wholesale_price_field',
            wholesale_price_data_map_key        = $variable_product_variations.length > 0 ? role + '_' + variation_loop_index_id : role, 
            el_discount_field                   = $('#' + discount_field_id),
            el_wholesale_price_field            = $('#' + wholesale_price_field_id);

        if(selected_discount_type  === 'percentage'){
            var regular_price_id     = $variable_product_variations.length > 0 ? 'variable_regular_price_' + variation_loop_index_id : '_regular_price',
                price                = getProductRegularPrice(regular_price_id),
                discount             = el_discount_field.val();

            // show discount input field
            $(class_discount_field).show();

            // disable wholesale price input field
            el_wholesale_price_field.attr('readonly', true);

            // wholesale price field tooltip
            reinitializeTooltip(form_wholesale_price_field, el_wholesale_price_field.attr('data-field_desc_percentage'));

            if(discount != "" && discount > 0){
                var discounted_price = calculateDiscountedPrice(price, discount);
                discounted_price     = removeTrailingZeros(discounted_price);

                el_wholesale_price_field.val(discounted_price);
            }else{

                el_wholesale_price_field.attr('data-fixed_price', el_wholesale_price_field.val());
                el_wholesale_price_field.val(null);
            }

        }else{

            // hide discount input field
            $(class_discount_field).hide();

            // enable wholesale price input field
            el_wholesale_price_field.attr('readonly', false);

            // wholesale price field tooltip
            reinitializeTooltip(form_wholesale_price_field, el_wholesale_price_field.attr('data-field_desc_fixed'));

            if(!!el_wholesale_price_field.attr('data-fixed_price')){
                el_wholesale_price_field.val(el_wholesale_price_field.attr('data-fixed_price'));
            }else if(!!el_wholesale_price_field.attr('data-fixed_price') == false && el_discount_field.val() != ""){
                el_wholesale_price_field.val(null);
            }else if(!!el_wholesale_price_field.attr('data-fixed_price') == false && el_discount_field.val() == ""){
                el_wholesale_price_field.val(null);
            }

        }
    });

    /**
     * Listen to the change event on the product-type dropdown.
     * 
     * This event will only monitor if the selection is variable product, we need to do this in order to precess new product variables created.
     * 
     * @since 2.1.0
     * 
     */
    $('body').on('change', '#product-type', function(e) {
        $product_type = $(this).val();

        if($product_type === 'variable'){
            processProductVariations();
        }else{
            processSimpleProducts();
        }

    });


    /**
     * Re-Initialize tooltip
     * 
     * This code will re-initalize wc_help_tip this is a tooltip that woocommerce use, you can override the content of the tooltip base on your requirements.
     * This function will only run, if you will include the "jquery-tiptip" in your enqueue scripts.
     * 
     * @since 2.1.0
     * @param {*} element This is the parent element of the tooltip you wish to override
     * @param {*} message This contains a custom message
     */
    function reinitializeTooltip(element, message){
        $(element + " span.woocommerce-help-tip").tipTip({ content: message });
    };

    /**
     * Process variable Products for wholesale percentage discount
     * 
     * This function will process the variable products with variations, if the discount type is Percentage or Fixed price.
     * 
     * In this code block we use meta-characters (!"#$%&'()*+,./:;<=>?@[\]^``{|}~) in the element ID or class name, ours we use square brackets [], if we are going to use JQUERY to access our elements. It must be escaped with two backslashes \\. For eample id="foo[bar]", can use the selector $("#foo\\[bar\\]")
     * 
     * @since 2.1.0
     */
    function processProductVariations(){
        var target            = $('#variable_product_options')[0],
            MutationObserver  = window.MutationObserver || 
                                window.WebKitMutationObserver || 
                                window.MozMutationObserver,
            config            = { childList: true, characterData: false, subtree: true };

        var observer          = new MutationObserver( function(mutations) {

            var discount_type = $('.wholesale_discount_type');

            discount_type.each( function (i) {
                if( i < discount_type.length ){
                    var role                         = $(this).attr('data-wholesale_role'),
                    variation_loop_index_id          = $(this).attr('data-loop_id'),
                    percentage_input_field_container = $('.form-field.' + role + '_wholesale_percentage_discount\\[' + variation_loop_index_id + '\\]_field'),
                    el_wholesale_price_field         = $('#' + role + '_wholesale_prices\\[' + variation_loop_index_id + '\\]');

                    if(variation_loop_index_id !== null){

                        if(el_wholesale_price_field !== null){

                            el_wholesale_price_field.val(removeTrailingZeros(el_wholesale_price_field.val()));
                            $wholesale_price_old_data_map.set(role + '_' + variation_loop_index_id, el_wholesale_price_field.val());
                        }


                        if($(this).val() === 'fixed'){

                            percentage_input_field_container.hide();
                            el_wholesale_price_field.attr('readonly', false);

                        }else{

                            percentage_input_field_container.show();
                            el_wholesale_price_field.attr('readonly', true);
                        }

                    }
                }
            } );

        });
        
        if(target) observer.observe(target, config);
    }

    /**
     * Process Simple Products for wholesale percentage discount
     * 
     * This function will process the simple products, if the discount type is Percentage or Fixed price.
     * 
     * @since 2.1.0
     */
    function processSimpleProducts (){
        $wholesale_roles.forEach((role) => {
            var select_discount_type_id             = role + '_wholesale_discount_type',
                wholesale_price_field_id            = role + '_wholesale_price',
                el_selected_discount_type           = $('#' + select_discount_type_id),
                el_wholesale_price_field            = $('#' + wholesale_price_field_id),
                el_discount_field                   = $('#' + role + '_wholesale_percentage_discount'),
                percentage_input_field_container    = $('.form-field.' + role + '_wholesale_percentage_discount_field');

            if(el_selected_discount_type !== null){
                if((el_selected_discount_type.val() === 'percentage' && el_discount_field.val() < 100) || (el_selected_discount_type.val() === 'fixed')){

                    el_wholesale_price_field.text(removeTrailingZeros(el_wholesale_price_field.val()));
                }

                // Set wholesale price old data, this will be use later under selection of discount type
                $wholesale_price_old_data_map.set(role, el_wholesale_price_field.val());

                if(el_selected_discount_type.val() === 'percentage'){
                    el_wholesale_price_field.attr('readonly', true);
                    percentage_input_field_container.show();
                }else{
                    el_wholesale_price_field.attr('readonly', false);
                    percentage_input_field_container.hide();
                }
            }
        });
    }

    /**
     * Get Percentage Wholesale Price By Regular Price
     * 
     * This will get the percentage wholesale price base on regular price if the discount type is percentage. If the discount type is fixed then we set the wholesale price field text to empty if it has no existing value and vice versa.
     * 
     * @param {integer} variation_loop_index_id  This is the index id of variations in a variable product, if we have a total of 4(Four variations) its loop index will be like this "0,1,2,3" like an array index.
     * @param {float} regular_price This is the products regular price, we will base here our computation for the percentage discount.
     */
    function getPercentageWholesalePriceByRegularPrice(variation_loop_index_id, regular_price){

        $wholesale_roles.forEach(function(role){

            var select_discount_type_id         = $variable_product_variations.length > 0 ? 
                                                role + '_wholesale_discount_type\\[' + variation_loop_index_id +'\\]' : role + '_wholesale_discount_type',
                discount_field_id               = $variable_product_variations.length > 0 ? 
                                                role + '_wholesale_percentage_discount\\[' + variation_loop_index_id + '\\]' : role + '_wholesale_percentage_discount',
                wholesale_field_id              = $variable_product_variations.length > 0 ? 
                                                role + '_wholesale_prices\\[' + variation_loop_index_id + '\\]' : role + '_wholesale_price',
                wholesale_price_data_map_key    = $variable_product_variations.length > 0 ? 
                                                role + '_' + variation_loop_index_id : role,

                el_wholesale_price_field        = $('#' + wholesale_field_id),
                el_discount_field               = $('#' + discount_field_id),
                el_discount_type                = $('#' + select_discount_type_id),
                selected_options                = el_discount_type ? el_discount_type.val() : null;

            if(selected_options == null || el_discount_field == null){
                return false;
            };

            var discount = ($options.decimal_sep !== '.') ? el_discount_field.val().replace($options.decimal_sep, '.') : el_discount_field.val();

            if(selected_options === 'percentage' && regular_price !== "" && el_discount_field.val() !== ""){
                var discounted_price = calculateDiscountedPrice(regular_price, discount);

                if(discount < 100){
                    el_wholesale_price_field.val(removeTrailingZeros(discounted_price));
                }else{
                    el_wholesale_price_field.val(0);
                }

            }else if(selected_options === 'fixed') {
                if( !!el_wholesale_price_field.attr('data-fixed_price') ){
                    el_wholesale_price_field.val(el_wholesale_price_field.attr('data-fixed_price'));
                }else{
                    var old_wholesale_price_value = $wholesale_price_old_data_map.get(wholesale_price_data_map_key);
                    el_wholesale_price_field.val(old_wholesale_price_value);
                }
            }else{
                el_wholesale_price_field.val(null);
            }

        });         
    }

    /**
     * Remove all trailing zeros after decimal
     * 
     * - Remove all trailing zero's after decimal
     * - If the decimal separator is comma(",") we convert it to dot(".") separator, if we dont do this it will spit out a wrong value. (e.g: parseFloat(22,20) will output "22" instead of "22.2").
     * 
     * @since 2.1.0
     * @param float price - The price of the product/item
     * @returns 
     */
    function removeTrailingZeros (price){

        try {

            if((price !== "" && price !== null)){
                // First we convert if has decimal separator of ',' to '.'
                price = $options.decimal_sep !== '.' ? price.toString().replace($options.decimal_sep,".") : price;

                // we remove all trailing zeros using parseFloat() function
                price = parseFloat(price);

                // we check if the decimal separator is comma (','), we change it back to the decimal separator that is set in wordpress settings.
                if( ! isNaN(price) ){
                    price = $options.decimal_sep !== '.' ? price.toString().replace('.',$options.decimal_sep) : price;
                }else{
                    price = '';
                }
            }

            return price;

        } catch (e) {
            console.error(e.message);
        }

    }

    /**
     * Get Product Regular Price
     * 
     * @since 2.1.0
     * @param Number id     - ID of the element in which where going to extract the price.
     * @returns {currency}  - Regular price
     */
    function getProductRegularPrice (id){
        var el_product_reg_price = $('#' + id);
        return el_product_reg_price.val();
    }

    /**
     * Calculate Percentage
     * 
     * This will calculate the percentage to be use to deduct to the regular price.
     * 
     * @since 2.1.0
     * @param float percent  - The percent value to be use to get the computation of the percentage value from the current price
     * @param float total    - The regular price to be used to calculate the percentage value
     * @returns {float} percentage
     */
    function calculatePercentage (percent, total) {

        // If the decimal separator is not a period "." replace it with a period "." so we can calculate the percentage 
        percent = $options.decimal_sep !== '.' ? percent.toString().replace($options.decimal_sep,'.') : percent;

        var percentage = (percent / 100) * total;

        return percentage;
    }

    /**
     * Calculate Percentage Wholesale Price
     * 
     * @since 2.1.0
     * @param Float price    - Price to calculate
     * @param Float percent  - Percentage to calculate
     * @returns {currency}   - The calculated discounted price
     */
    function calculateDiscountedPrice (price, percent){

        try {
            price = $options.decimal_sep !== '.' ? price.toString().replace($options.decimal_sep,".") : price;
        }catch (e) {
            console.error(e.message);
        }

        var wholesale_price = price - calculatePercentage(percent, price);

        /**
         * This will check if the wholesale_price value is less than or equal to 0 or wholesale_price is NaN(Not a Number) if true, it will make the wholesale_price to null. Otherwise it will format the wholesale_price base on the parameters given using the currency.js
         */
        wholesale_price =  wholesale_price <= 0 || isNaN(wholesale_price) ? null : currency(wholesale_price, {precision: $options.calculation_decimal_places, decimal: $options.decimal_sep, separator:'', pattern: '#'}).format();

        // We return null if the wholesale_price is null, otherwise return wholesale_price raw value
        return (wholesale_price === null) ? null : wholesale_price;
    }

    /**
     * This function listens to the changes in the discount type dropdown and then calls the appropriate function to process
     * the product variations or simple products
     * 
     * @since 2.1
     * 
     */
    function listenToDiscountTypeDropDownChanges() {

        if($variable_product_variations.length > 0 ){
            processProductVariations();
        }else{
            processSimpleProducts();
        }

    }


    /**=================================================================================================================
     * Events
     =================================================================================================================*/
     listenToDiscountTypeDropDownChanges();

});