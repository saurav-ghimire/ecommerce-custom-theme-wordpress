jQuery(document).ready(function ($) {

    var $acfwf_cross_sell = $(".acfwf-cross-sell");

    $acfwf_cross_sell.find('a.acfwf-notice-dismiss').click(function (e) {

        $acfwf_cross_sell.fadeOut("fast", function () {
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: "wwp_hide_acfwf_install_notice",
                    nonce: wwp_acfwf_install_notice_js_params.nonce
                },
                dataType: "json"
            })
                .done(function (data, textStatus, jqXHR) {
                    // notice is now hidden
                })

        });

    });

});