<?php

namespace AgeGate\Admin\Tools;

use AgeGate\Update\Activate;
use AgeGate\Common\Admin\Helper;
use AgeGate\Common\Immutable\Constants as Immutable;

class Reset
{
    use Helper;

    public const PERMISSION = Immutable::HARD_RESET;

    public function __construct()
    {
        add_action('admin_post_age_gate_reset', [$this, 'resetSettings']);
        add_action('admin_post_age_gate_reset_post', [$this, 'resetPosts']);
    }

    private function auth($password)
    {
        $user = get_user_by('ID', get_current_user_id(  ));

        return wp_check_password( $password, $user->data->user_pass, get_current_user_id());
    }

    public function resetSettings()
    {
        if (!current_user_can(self::PERMISSION) || !wp_verify_nonce($_POST['ag_reset_settings'] ?? '', 'ag_reset_settings')) {
            wp_die('Disallowed action');
        }


        if (!$this->auth($_POST['pwd'])) {
            $this->redirect($_POST['_wp_http_referer'], 0, 'tools');
        }

        foreach (Immutable::AGE_GATE_OPTIONS as $option) {
            delete_option($option);
        }

        delete_option('age_gate_version');

        Activate::activate();

        $this->redirect($_POST['_wp_http_referer'], 1, 'tools');
    }

    public function resetPosts()
    {
        if (!current_user_can(self::PERMISSION) || !wp_verify_nonce($_POST['ag_reset_post'] ?? '', 'ag_reset_post')) {
            wp_die('Disallowed action');
        }

        if (!$this->auth($_POST['pwd'])) {
            $this->redirect($_POST['_wp_http_referer'], 0, 'tools');
        }


        global $wpdb;

        $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_age_gate-%'");
        $wpdb->query("DELETE FROM {$wpdb->termmeta} WHERE meta_key LIKE '_age_gate-%'");
        $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '_age_gate-%'");

        $this->redirect($_POST['_wp_http_referer'], 1, 'tools');
    }
}
