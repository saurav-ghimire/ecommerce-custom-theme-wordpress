<?php

namespace AgeGate\Common\Helpers;

class Woocommerce
{
    public static function isShop()
    {
        $page = get_option('woocommerce_shop_page_id', false);
        if ($page && function_exists('wc_get_page_id')) {
            if (is_post_type_archive('product') || is_page($page)) {
                return (int) $page;
            }
        }

        return false;
    }
}
