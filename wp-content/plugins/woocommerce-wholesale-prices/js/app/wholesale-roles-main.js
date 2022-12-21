jQuery(document).ready(function ($) {

    vex.defaultOptions.className = 'vex-theme-plain wwp-wholesale-roles';

    // Variable Declarations
    var $wholesaleRolesPage = $("#wwpp-wholesale-roles-page"),
        $wholesaleTable = $wholesaleRolesPage.find(".wp-list-table"),
        $wholesaleForm = $wholesaleRolesPage.find("#wholesale-form"),
        errorToastrShowDuration = "12000",
        successToastrShowDuration = "5000";

    /*
     |--------------------------------------------------------------------------
     | Initialize Tooltips
     |--------------------------------------------------------------------------
     */

    $('.tooltip').tipTip({
        'attribute': 'data-tip',
        'fadeIn': 50,
        'fadeOut': 50,
        'delay': 200
    });

    /*
     |--------------------------------------------------------------------------
     | Events
     |--------------------------------------------------------------------------
     */

    // Only allow letters, numbers and underscores in rolekey
    $wholesaleForm.find("#role-key").keyup(function () {

        var raw_text = jQuery(this).val();
        var return_text = raw_text.replace(/[^a-zA-Z0-9_]/g, '');
        jQuery(this).val(return_text);

    });

    $wholesaleForm.find("#edit-wholesale-role-submit").click(function () {

        wwpWholesaleRolesFormActions.setSubmitButtonToProcessingState();

        var roleName = $.trim($wholesaleForm.find("#role-name").val()),
            roleKey = $.trim($wholesaleForm.find("#role-key").val()),
            roleDesc = $.trim($wholesaleForm.find("#role-desc").val()),
            onlyAllowWholesalePurchases = $wholesaleForm.find("#only-allow-wholesale-purchase").is(":checked") ? 'yes' : 'no',
            checkPoint = true;

        if (roleName == '') {
            toastr.error(wwp_wholesale_roles_main_params.i18n_enter_role_name, wwp_wholesale_roles_main_params.i18n_error_wholesale_form, { "closeButton": true, "showDuration": errorToastrShowDuration });
            checkPoint = false;
            wwpWholesaleRolesFormActions.setSubmitButtonToNormalState();
        }

        if (roleKey == '') {
            toastr.error(wwp_wholesale_roles_main_params.i18n_enter_role_key, wwp_wholesale_roles_main_params.i18n_error_wholesale_form, { "closeButton": true, "showDuration": errorToastrShowDuration });
            checkPoint = false;
            wwpWholesaleRolesFormActions.setSubmitButtonToNormalState();
        }

        if (checkPoint) {

            var role = {
                'roleKey': roleKey,
                'roleName': roleName,
                'roleDesc': roleDesc,
                'onlyAllowWholesalePurchases': onlyAllowWholesalePurchases
            };

            wwpBackendAjaxServices.editWholesaleRole(role)
                .done(function (data, textStatus, jqXHR) {
                    console.log(data);
                    if (data.status == 'success') {

                        wwpWholesaleRolesListingActions.editRole(role);
                        toastr.success(role.roleName + ' ' + wwp_wholesale_roles_main_params.i18n_role_successfully_edited, wwp_wholesale_roles_main_params.i18n_successfully_edited_role, { "closeButton": true, "showDuration": successToastrShowDuration });

                    } else {

                        toastr.error(data.error_message, wwp_wholesale_roles_main_params.i18n_failed_edit_role, { "closeButton": true, "showDuration": errorToastrShowDuration });
                        console.log(data);

                    }

                    wwpWholesaleRolesListingActions.setRowsToNormalMode();
                    wwpWholesaleRolesFormActions.initialForm();
                    wwpWholesaleRolesFormActions.setSubmitButtonToNormalState();

                })
                .fail(function (jqXHR, textStatus, errorThrown) {

                    toastr.error(jqXHR.responseText, wwp_wholesale_roles_main_params.i18n_failed_edit_role, { "closeButton": true, "showDuration": errorToastrShowDuration });

                    console.log(wwp_wholesale_roles_main_params.i18n_failed_edit_role);
                    console.log(jqXHR);
                    console.log('----------');

                    wwpWholesaleRolesFormActions.setSubmitButtonToNormalState();

                });

        }

        return false;

    });

    $wholesaleForm.find("#cancel-edit-wholesale-role-submit").click(function () {

        wwpWholesaleRolesListingActions.setRowsToNormalMode();
        wwpWholesaleRolesFormActions.initialForm();

    });

    $wholesaleTable.delegate(".edit-role", "click", function () {

        wwpWholesaleRolesListingActions.setRowsToNormalMode();

        var $currentRow = $(this).closest("tr"),
            role = {
                'roleName': $.trim($currentRow.find(".column-role-name > strong > a").text()),
                'roleKey': $.trim($currentRow.find(".column-role-key").text()),
                'roleDesc': $.trim($currentRow.find(".column-role-desc").text()),
                'onlyAllowWholesalePurchases': $.trim($currentRow.find(".column-only-allow-wholesale-purchases").attr("data-attr-raw-data"))
            };

        wwpWholesaleRolesFormActions.setFormToEditMode(role);
        wwpWholesaleRolesListingActions.setRowToEditMode($currentRow);

        return false;

    });


    $wholesaleRolesPage.find('a.page-title-action').on('click', function () {
        vex.dialog.open({
            showCloseButton: true,
            unsafeMessage: wwp_wholesale_roles_main_params.i18n_upsell_message,
            buttons: []
        });
    });


    // Init on load
    wwpWholesaleRolesFormActions.initialForm();

});