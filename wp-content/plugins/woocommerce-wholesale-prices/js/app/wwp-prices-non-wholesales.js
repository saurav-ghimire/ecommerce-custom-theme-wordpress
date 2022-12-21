jQuery(document).ready(function ($) {
    /**-----------------------------------------------------------------------------------------------------------------
     * Variables
     -----------------------------------------------------------------------------------------------------------------*/
    var popover_title =
        '<h4 class="custom-title"><i class="fa fa-info-circle"></i> ' +
        options.popover_header_title +
        "</h4> <a href='#' class='close' data-dismiss='alert'>&times;</a>";
    /**-----------------------------------------------------------------------------------------------------------------
     * Events
     -----------------------------------------------------------------------------------------------------------------*/
    // Prevent all anchor tag with an attribute of "[data-toggle=popover]"  event to execute its default action, since we will not be needing it

    $("body").popover({
        title: popover_title,
        container: 'body',
        content: function() {
            var product_id = $(this).data('product_id'), session_key = "";

            // Set session key
            session_key = "wwp-product-id-"+product_id;

            // create a placeholder id
            var tmp_id = session_key;

            // create temporary id for popover holder
            var popover_tmp_id = 'popover-tmp-id-'+ product_id;

            /**
             * If wholesale_price_data is empty or null then we query data from api which will take sometime,
             * and if wholesale_price_data is not empty then we load data from local storage.
             */

            // add temporary id on popover holder
            $(this).attr('id',popover_tmp_id);

            var el = document.getElementById(popover_tmp_id);

            if(el.getAttribute('data-wholesale_data').length <= 0){

                // get data, when data loads remove the spinner
                get_wholesale_price(product_id).then(function(data) {

                    $(this).attr('data-wholesale_data', data);

                    $('#'+ tmp_id).removeClass('loading spinner').html(data);
                    prepareWholesalePriceDataTable(tmp_id, popover_tmp_id);

                });

            }else{

                // get wholesale_data attribute content
                var wholesale_data = JSON.parse(el.getAttribute('data-wholesale_data'));
                return $('<div>').attr('id', tmp_id).removeClass('loading spinner').html(wholesale_data.price_html);
            }

            // generate temporary content for the placeholder to show the user while we wait
            return $('<div>').attr('id', tmp_id).addClass('loading spinner');

        },
        html: true,
        selector: '[rel=popover]',
        trigger: 'click',   
    }).addClass('wwp-see-wholesale-prices-popover');

    /**
     * Hides and destroys an element’s popover. Popovers that use delegation (which are created using the selector option) cannot be individually destroyed on descendant trigger elements.
     */
    $('body').on('click', function (e) {
        $('[rel=popover]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('dispose');
            }
        });
    });


    $(document).on("click", "[rel=popover]", function (e) {
        e.preventDefault();
    });

    // popover close event
    $(document).on('click', '.popover .close', function(e){
        e.preventDefault();
        $(this).parents('.popover').popover('hide');
    });



    // Disable Anchor tag for registration link if wwlc is not active
    if (options.is_wwlc_active == false) {
        $(".register-link").prop("disable", true);
    }

    /**-----------------------------------------------------------------------------------------------------------------
     * Functions
     -----------------------------------------------------------------------------------------------------------------*/

    /**
     * Get wholesale price data
     * 
     * @since 1.16.1
     * @param {*} product_id 
     * @returns promise
     */
    async function get_wholesale_price(product_id) {
        try {
            return await getWholesalePriceDataByAPI(product_id);
        }catch(e){

        }
    }

    /**
     * Prepare wholesale price data table for wholesale price box and update our localStorage
     * 
     * @since 1.16.1
     * @param {*} tmp_id 
     * @param {*} product_id 
     */
    function prepareWholesalePriceDataTable(tmp_id, popover_tmp_id){
        var tbody = '', table='', registration = '',element = document.getElementById(tmp_id),wholesale_container_element = element.querySelectorAll('.wholesale_price_container'),container_count = wholesale_container_element.length,currencySymbol = get_currency_symbol(tmp_id),wholesale_price = '',i = 0;

        table = "<div class='wwp-see-wholesale-prices-popover popover-wholesale-price-table'><table class='table'><tbody>";

        // Loop in container elements first
        for(i=0;i<container_count;i++){
            $('#'+ tmp_id +' > del').remove();
            var arr = wholesale_container_element[i].innerText.split(currencySymbol);
            var wholesale_role = arr[0].trim();

            switch(arr.length){
                case 2:
                    wholesale_price = currencySymbol+arr[1];
                    break;
                case 3:
                    wholesale_price = currencySymbol+arr[1] + is_price_range(arr[1],currencySymbol+arr[2]);
                    break;
                case 4:

            }

            tbody += "<tr><td class='textalign-left' style='width:100%'>"+ wholesale_role +"</td><td class='textalign-right autowidth'>"+ wholesale_price +"</td></tr>";
        }

        table += tbody;
        table += "</tbody></table></div>";

        // Add registration link if wwlc is enabled
        if(options.is_wwlc_active){
            registration ="<div class='register-link'>"+$('.register-link')[0].innerHTML+"</div>";
        }
        $('#'+ tmp_id).html(table).append(registration);

        var html_price = $('#'+ tmp_id).html(),price_data = {price_html:html_price};

        // update wholesale_data attribute
        $('#'+popover_tmp_id).attr('data-wholesale_data', JSON.stringify(price_data));

    }

    /**
     * Get wholesale price data via api
     * 
     * @since 1.16.1
     * @param {*} product_id 
     * @returns wholesale price data
     */
    function getWholesalePriceDataByAPI(product_id){
        let result;

        // Make sure product_id is not empty, if empty dont execute ajax
        if(product_id){
            result = $.ajax({
                url: options.ajaxurl,
                type: 'POST',
                data : {
                    nonce: options.nonce,
                    action: 'get_product_wholesale_prices_ajax',
                    data: {
                        product_id: product_id,
                    }
                }
            });

            return result;
        }
    }

    /**
     * This will basically check if the first_price contains dash(-) to identify if the price is in range
     * if does not contain dash then make the second/third price a suffix
     * 
     * @param {string} first_price Needle
     * @param {string} price Price to be formated
     * @return string
     */
    const is_price_range =(first_price, price)=>{

        if(first_price.indexOf('-') <= 0){
            price = "<small>"+ price +"</small>";
        }

        return price;
    }

    /**
     * Get Currency Symbol
     * 
     * @param {string} id Element ID
     * @returns string Currency Symbol
     */
    const get_currency_symbol =(id)=>{
        var now = new Date();

        let currency = '';

        try {
            currency = $('#'+ id +' .woocommerce-Price-currencySymbol')[0].innerText;
        } catch (e) {
        
        }

        return currency;
    }

});
