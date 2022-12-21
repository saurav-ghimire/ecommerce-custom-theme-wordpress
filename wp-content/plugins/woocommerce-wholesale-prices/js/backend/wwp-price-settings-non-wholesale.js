jQuery(document).ready(function ($) {
    /**=================================================================================================================
     * Variables
     =================================================================================================================*/
    var $wwp_show_wholesale_price_chkbox = $(
            "#wwp_prices_settings_show_wholesale_prices_to_non_wholesale"
        ),
        $fieldset = $wwp_show_wholesale_price_chkbox.closest("fieldset"),
        show_in_shop_check = Options.show_in_shop == "yes" ? "checked" : "", // Check if it will show "Click to See Wholesale Prices" in Shops Archives
        show_in_product_check = Options.show_in_products == "yes" ? "checked" : "", // Check if it will show "Click to See Wholesale Prices" in single products page
        show_in_wwof_check = Options.show_in_wwof == "yes" ? "checked" : ""; // Check if it will show "Click to See Wholesale Prices" in Wholesale Order Form.  Precondition: WWOF Plugin should be activated

    /**=================================================================================================================
     * Functions
     =================================================================================================================*/

    /**
     * This function is responsible for rendering controls in the Price options of the Wholesale Price settings
     * @since 1.15.0
     * @returns string containing html tags and fields
     */
    function render_show_wholesale_price_to_non_wholesales() {
        var wholesale_roles = "",
            wholesale_role_selection = "",
            selected = "",
            register_text_notice = "",
            register_text_label_inactive = "",
            wwof_notice_inactive_text = "";

        wholesale_roles = Options.wholesale_roles;
        wholesale_roles_options = Options.wholesale_role_options;

        // Check if wwlc is active/installed, if not warn store owner
        if (!Options.is_wwlc_active) {
            register_text_notice = Options.i18n_register_text_notice + Options.wwlc_admin_notice + Options.i18n_bonus_text;
            register_text_label_inactive =
                "style='color: rgba(44,51,56,.5) !important;'";
        }

        if (!Options.is_wwof_active) {
            wwof_notice_inactive_text = Options.i18n_wwof_inactive_notice;
        }

        // Get wholesale roles
        if (Options.is_wwpp_active) {
            for (var key in wholesale_roles) {
                if (wholesale_roles_options.indexOf(key) >= 0) {
                    selected = "selected";
                } else {
                    selected = "";
                }

                wholesale_role_selection +=
                    '<option value="' +
                    key +
                    '" ' +
                    selected +
                    ">" +
                    wholesale_roles[key]["roleName"] +
                    "</option>";
            }
        } else {
            selected = "selected";

            for (var key in wholesale_roles) {
                wholesale_role_selection +=
                    '<option value="' +
                    key +
                    '" ' +
                    selected +
                    ">" +
                    wholesale_roles[key]["roleName"] +
                    "</option>";
            }
        }

        // Control container
        var control_container =
            "<!--Begin: #wwp-non-wholesale-settings-->" +
            "<div id='wwp-non-wholesale-settings' style='max-width: 680px !important;'>" +
            //----------------------------------------------------------------------------------------------------------
            // WWP API Credentials
            //----------------------------------------------------------------------------------------------------------
            "<h3>"+ Options.i18n_woocommerce_api_title +"</h3>" +
            "<p>"+ Options.i18n_woocommerce_api_help_desc +" <a href='"+ Options.base_url +"/wp-admin/admin.php?page=wc-settings&tab=advanced&section=keys' target='_blank'><strong>"+ Options.i18n_generate_api_key +"</strong></a></p>"+
            "<table class='form-table'>" +
            "<tbody>" +
            // Consumer Key
            "<tr valign='top'>" +
            "<th class='titledesc' scope='row'>"+ Options.i18n_consumer_key +"</th>" +
            "<td class='forminp forminp-text' style='padding-right:0;'>" +
            "<input id='wwp_woocommerce_api_consumer_key' name='wwp_woocommerce_api_consumer_key' type='text' style='width:100%' placeholder='' value='"+ Options.api_consumer_key +"' />" +
            "</td>" +
            "</tr>" +
            // Consumer Secret Key
            "<tr valign='top'>" +
            "<th class='titledesc' scope='row'>"+ Options.i18n_consumer_secret +"</th>" +
            "<td class='forminp forminp-password' style='padding-right:0;'>" +
            "<input id='wwp_woocommerce_api_consumer_secret' name='wwp_woocommerce_api_consumer_secret' type='password' style='width:100%' placeholder='' value='"+ Options.api_consumer_secret +"' />" +
            "</td>" +
            "</tr>" +
            "<tr valign='top'>" +
            "<th class='titledesc' scope='row'>&nbsp;</th>" +
            "<td class='forminp forminp-password' style='padding-right:0;'>" +
            "<a id='wwp_auto_generate_api_key' name='wwp_auto_generate_api_key' class='button button-secondary'>"+ Options.i18n_auto_generate_key +"</a>" +
            "<span class='spinner' style='margin-top: 3px; float: none; display: none;'></span>" +
            "<span class='status valid' style='padding-left: 0px; display: none;'>" + Options.i18n_api_key_is_valid_msg + "<span class='dashicons dashicons-yes-alt' style='color:green;'></span></span>" +
            "<span class='status invalid' style='padding: 5px 15px; display: none;'>" + Options.i18n_api_key_is_invalid_msg +"<span class='dashicons dashicons-dismiss' style='color:red;'></span></span>" +
            "</td>" +
            "</tr>" +
            "</tbody>" +
            "</table>" +
            "<hr>" +
            "<!--Begin: .wwp-non-wholesale-setting-controls -->" +
            "<div class='wwp-non-wholesale-setting-controls'>" +
            "<h3>"+ Options.i18n_show_wholesale_price_settings_title +"</h3>" +
            "<table class='form-table'>" +
            "<tbody>" +
            //----------------------------------------------------------------------------------------------------------
            // Show Wholesale Prices in pages
            //----------------------------------------------------------------------------------------------------------
            "<tr valign='top'><th class='titledesc' scope='row'>"+ Options.i18n_locations_title +"</th><td>" +
            "<label for='wwp_non_wholesale_show_in_shop' style='padding-right: 20px;'><input id='wwp_non_wholesale_show_in_shop' name='wwp_non_wholesale_show_in_shop' class='wwp_non_wholesale_show_in_shop' type='checkbox' value='yes'" +
            show_in_shop_check +
            "> "+ Options.i18n_locations_shop +"</label>" +
            "</td></tr>" +
            "<tr valign='top'><th class='titledesc' scope='row'></th><td>" +
            "<label for='wwp_non_wholesale_show_in_products'><input id='wwp_non_wholesale_show_in_products' name='wwp_non_wholesale_show_in_products' class='wwp_non_wholesale_show_in_products' type='checkbox' value='yes' " +
            show_in_product_check +
            "> "+ Options.i18n_locations_single_product +"</label>" +
            "</td></tr>" +
            "<tr valign='top'><th class='titledesc' scope='row'></th><td>" +
            "<label for='wwp_non_wholesale_show_in_wwof'><input id='wwp_non_wholesale_show_in_wwof' name='wwp_non_wholesale_show_in_wwof' class='wwp_non_wholesale_show_in_wwof' type='checkbox' value='yes' " +
            show_in_wwof_check +
            "> <span id='wwof_label_text_span'>"+ Options.i18n_locations_order_form +"</span> <p class='description'>" +
            wwof_notice_inactive_text +
            "</p></label>" +
            "</td></tr>" +
            //----------------------------------------------------------------------------------------------------------
            // Wholesale Prices replacement text
            //----------------------------------------------------------------------------------------------------------
            "<tr valign='top'>" +
            "<th class='titledesc' scope='row'>" +
            "<label for='wwp_see_wholesale_prices_replacement_text'>" +
            Options.i18n_click_to_see_wholesale_price_title +
            Options.wwp_see_wholesale_prices_replacement_text_tooltip +
            "</label>" +
            "</th>" +
            "<td class='forminp forminp-text' style='padding-right:0;'>" +
            "<input id='wwp_see_wholesale_prices_replacement_text' class='wwp_see_wholesale_prices_replacement_text' name='wwp_see_wholesale_prices_replacement_text' type='text' style='width:100%' placeholder='' value='" +
            Options.wwp_see_wholesale_prices_replacement_text +
            "'>" +
            "</td>" +
            "</tr>" +
            //----------------------------------------------------------------------------------------------------------
            // Wholesale Role Selection
            //----------------------------------------------------------------------------------------------------------
            "<tr valign='top'>" +
            "<th class='titledesc' scope='row'>" +
            "<label for='wwp_non_wholesale_wholesale_role_select2'>" +
            Options.i18n_wholesale_role_title +
            Options.wwp_wholesale_role_select_select2_tooltip +
            "</label>" +
            "</th>" +
            "<td class='forminp forminp-multiselect' style='padding-right:0;'>" +
            "<select id='wwp_non_wholesale_wholesale_role_select2' name='wwp_non_wholesale_wholesale_role_select2[]' data-placeholder='" +
            Options.wholesale_role_data_placeholder_txt +
            "' class='wwp_non_wholesale_wholesale_role_select2' style='width:100%' multiple>" +
            wholesale_role_selection +   
            "</select>" +
            "</td>" +
            "</tr>" +
            //----------------------------------------------------------------------------------------------------------
            // Register text
            //----------------------------------------------------------------------------------------------------------
            "<tr valign='top'>" +
            "<th class='titledesc' scope='row'>" +
            "<label for='wwp_price_settings_register_text' " +
            register_text_label_inactive +
            ">" +
            Options.i18n_register_title +
            Options.wwp_price_settings_register_text_tooltip +
            "</label>" +
            "</th>" +
            "<td class='forminp forminp-text' style='padding-right:0;'>" +
            "<input id='wwp_price_settings_register_text' class='wwp_price_settings_register_text' name='wwp_price_settings_register_text' type='text' style='width:100%' placeholder='' value='" +
            Options.wwp_price_settings_register_text +
            "'></td></tr></tbody></table>" +
            "</div>" +
            //----------------------------------------------------------------------------------------------------------
            // Register text - Warning notice will be shown if wwlc is inactive/not installed.
            //----------------------------------------------------------------------------------------------------------
            register_text_notice +
            "</div>" +
            "<!--End: #wwp-non-wholesale-settings -->";

        // Append Control container
        $fieldset.append(control_container);
    }

    /**=================================================================================================================
     * Events
     =================================================================================================================*/

    $(document).on("click", "input[type='checkbox']", function () {
        if (this.checked) {
            this.setAttribute("checked", "checked");
        } else {
            this.removeAttribute("checked");
        }
    });

    function run_events() {
        // Initialize Select2
        $(".wwp_non_wholesale_wholesale_role_select2").select2();
        $(".wwp_non_wholesale_wholesale_role_select2").prop('disabled', Options.is_wwpp_active ? false : true);
        
        // Initialize tooltip
        $("body").find(".woocommerce-help-tip").tipTip({
            attribute: "data-tip",
            fadeIn: 50,
            fadeOut: 50,
            delay: 200,
        });

        // Trigger Checkbox change function to show wholesale price to non-wholesale users
        $wwp_show_wholesale_price_chkbox.trigger("change");

        // Check if WWLC is active/installed, if not disable registration text
        $("#wwp_price_settings_register_text").prop("disabled", Options.is_wwlc_active ? false : true);

        // Check if WWOF is active/installed, if not disable option to show wholesale prices for WWOF
        if (Options.is_wwof_active) {
            $("#wwp_non_wholesale_show_in_wwof").prop("disabled", false);
            $("#wwof_label_text_span").prop("disabled", false);
        } else {
            $("#wwp_non_wholesale_show_in_wwof").prop("disabled", true);
            $("#wwof_label_text_span").prop("disabled", true);
            $("#wwof_label_text_span").css("color", "rgba(44,51,56,.5)");
        }

        // Hide Auto Generate Button if api keys are valid
        if(Options.is_api_key_valid){
            $("body").find("span.status.valid").css("display", "inline-block");
            $("body").find("span.status.invalid").hide();
            $("body").find("a#wwp_auto_generate_api_key").hide();
        }else {
            $("body").find("span.status.valid").hide();
            $("body").find("span.status.invalid").css("display", "inline-block");
        } 

    }

    $wwp_show_wholesale_price_chkbox.change(function () {
        if ($(this).is(":checked")) {
            $("#wwp-non-wholesale-settings").slideDown();
        } else {
            $("#wwp-non-wholesale-settings").slideUp();
        }
    });

    /**=================================================================================================================
     * Page Load
     =================================================================================================================*/

    // Render controls
    render_show_wholesale_price_to_non_wholesales();

    // Run Events
    run_events();
});
