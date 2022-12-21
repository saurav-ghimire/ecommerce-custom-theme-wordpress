<?php

namespace AgeGate\Admin;

use Asylum\Utility\Notice;
use AgeGate\Common\Immutable\Constants;

class Ajax
{
    public function __construct()
    {
        add_action('wp_ajax_ag_clear_legacy_css', [$this, 'removeLegacyCss']);
    }

    public function removeLegacyCss()
    {
        $data = [];
        if (!current_user_can(Constants::ADVANCED) || !wp_verify_nonce($_POST['nonce'], 'ag_clear_css' )) {
            $data['status'] = 'Not allowed';
            $code = 401;
        } else {
            delete_option('age_gate_legacy_css');
            Notice::add(__('Legacy CSS removed'), 'success');
            $data['status'] = 'ok';
            $code = 200;
        }

        wp_send_json($data, $code);
        wp_die();
    }
}
