import { listView, megaphone } from "@wordpress/icons";

wp.hooks.addFilter(
    'woocommerce_admin_homescreen_quicklinks',
    'woocommerce-wholesale-prices',
    function(quickLinks) {

        let wwpStoreLinks = [{
            title: 'View Wholesale Orders',
            href: 'edit.php?s&post_status=all&post_type=shop_order&action=-1&m=0&wwpp_fbwr=all_wholesale_orders&_customer_user&filter_action=Filter&paged=1&action2=-1',
            icon: listView,
        }];

        // If not all premium plugins are not activated (WWPP, WWLC, WWOF) Show the Upgrade link.
        if(options.has_all_premiums == 'false'){

            wwpStoreLinks.push({
                title: 'Upgrade Wholesale Suite',
                href: 'admin.php?page=wchome-wws-upgrade',
                icon: megaphone,
            });

        }

        return [
            ...quickLinks,
            ...wwpStoreLinks,
        ]
    }
);
