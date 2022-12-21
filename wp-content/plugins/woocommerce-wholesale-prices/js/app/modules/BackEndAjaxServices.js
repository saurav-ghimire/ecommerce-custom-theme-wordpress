/**
 * A function implementing the revealing module pattern to house all ajax request. It implements the ajax promise methodology
 * @return {Ajax Promise} promise it returns a promise, I promise that #lamejoke
 *
 * Info:
 * ajaxurl points to admin ajax url for ajax call purposes. Added by wp when script is wp enqueued
 */
var wwpBackendAjaxServices = function () {

    var editWholesaleRole = function (role) {

        return jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            data: { 
                action: "wwpEditWholesaleRole", 
                role: role,
                nonce: wwp_wholesale_roles_main_params.nonce },
            dataType: "json"
        });

    };

    return {
        editWholesaleRole: editWholesaleRole
    };

}();
