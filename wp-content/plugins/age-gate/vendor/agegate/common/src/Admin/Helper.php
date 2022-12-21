<?php

namespace AgeGate\Common\Admin;

trait Helper
{
    protected function redirect($referrer, $status = 1, $name = false, $additional = [])
    {
        $arr = [];
        parse_str(parse_url($referrer)['query'] ?? '', $arr);

        $redirects = [
            'age-gate',
            'age-gate-' . ($name ?: $this->getName()),
        ];

        if (in_array($arr['page'] ?? false, $redirects)) {
            $return = add_query_arg(['page' => $arr['page'], 'm' => $status], admin_url('/admin.php'));
        } else {
            $return = add_query_arg(['page' => 'age-gate', 'm' => $status], admin_url('/admin.php'));
        }

        foreach ($additional as $item) {
            $return = add_query_arg(['fail[]' => sanitize_text_field($item)], $return);
        }

        wp_safe_redirect(esc_url_raw($return));
        exit;
    }
}
