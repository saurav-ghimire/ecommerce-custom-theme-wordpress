var wwpWholesaleRolesListingActions = function () {

    var $wholesaleRolesTable = jQuery("#wwpp-wholesale-roles-page").find(".wp-list-table"),
        $wholesaleRolesList = $wholesaleRolesTable.find("#the-list"),
        removeNewlyAddedRowClasses = function () {

            setTimeout(function () {
                $wholesaleRolesList
                    .find('.newlyAdded')
                    .removeClass('newlyAdded');
            }, 3000);

        },
        setRowToEditMode = function ($row) {

            $row.addClass("editing");

        },
        setRowsToNormalMode = function () {

            $wholesaleRolesList.find("tr").removeClass("editing");

        },
        incrementRolesCount = function () {

            $wholesaleRolesTable.siblings(".tablenav").find(".wholesale-roles-count").each(function () {

                jQuery(this).text(parseInt(jQuery(this).text(), 10) + 1);

            });

        },
        decrementRolesCount = function () {

            $wholesaleRolesTable.siblings(".tablenav").find(".wholesale-roles-count").each(function () {

                if (parseInt(jQuery(this).text(), 10) > 0)
                    jQuery(this).text(parseInt(jQuery(this).text(), 10) - 1);

            });

        },
        editRole = function (role) {

            var i18n_wholesale_pruchases_only = role['onlyAllowWholesalePurchases'] === 'yes' ? wwp_wholesaleRolesListingActions_params.i18n_yes : wwp_wholesaleRolesListingActions_params.i18n_no;
            var currentRow = $wholesaleRolesList.find('.column-role-key').filter(function () {

                return jQuery(this).text() === role['roleKey'];

            }).closest('tr');

            currentRow.find('.column-role-name').find('strong').find('a').text(role['roleName']);
            currentRow.find('.column-only-allow-wholesale-purchases').attr('data-attr-raw-data', role['onlyAllowWholesalePurchases']).text(i18n_wholesale_pruchases_only);
            currentRow.find('.column-role-desc').text(role['roleDesc']);
            currentRow.addClass('newlyAdded');

            removeNewlyAddedRowClasses();

        };

    return {
        editRole: editRole,
        setRowToEditMode: setRowToEditMode,
        setRowsToNormalMode: setRowsToNormalMode
    };

}();