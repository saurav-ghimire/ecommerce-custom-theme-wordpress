/* global jQuery */

jQuery(document).ready(function ($) {

    vex.defaultOptions.className = 'vex-theme-plain wwp-review';

    var ajax_args = {
        url: ajaxurl,
        type: "POST",
        data: { action: "wwp_request_review_response", review_request_response: "", nonce: review_request_args.nonce },
        dataType: "json"
    };

    vex.dialog.open({
        overlayClosesOnClick: false,
        escapeButtonCloses: false,
        unsafeMessage: "<style>.vex-dialog-message > p { font-size: 14px; }</style><div style='text-align: center; padding: 4px 0;'><a style='display: inline-block; outline: none;' href='https://wholesalesuiteplugin.com/' target='_blank'><img src='" + review_request_args.js_url + "admin-review-notice-logo.png'></a></div>" + review_request_args.msg,
        buttons: [
            $.extend({}, vex.dialog.buttons.YES, {
                className: "vex-dialog-button-primary", text: "Review", click: function ($vexContent, event) {

                    ajax_args.data.review_request_response = "review";

                    $.ajax(ajax_args);

                    window.open(review_request_args.review_link);

                    vex.closeAll();

                }
            }),
            $.extend({}, vex.dialog.buttons.NO, {
                className: "vex-dialog-button-review-later", text: "Review Later", click: function ($vexContent, event) {

                    ajax_args.data.review_request_response = "review-later";

                    $.ajax(ajax_args);

                    vex.closeAll();

                }
            }),
            $.extend({}, vex.dialog.buttons.NO, {
                className: "vex-dialog-button-never-show", text: "Don't show again", click: function ($vexContent, event) {

                    ajax_args.data.review_request_response = "never-show";

                    $.ajax(ajax_args);

                    vex.closeAll();

                }
            })
        ]
    });

});
