<?php

namespace AgeGate\Enqueue;

use AgeGate\Common\Settings;
use AgeGate\Presentation\Form;

class Enqueue
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'admin'], 99);
        add_action('wp_enqueue_scripts', [$this, 'assets']);
    }

    public function admin()
    {
        if (($GLOBALS['pagenow'] ?? false) === 'plugins.php') {
            wp_enqueue_script('age-gate-update', AGE_GATE_URL . 'dist/update.js', [], AGE_GATE_VERSION);
        }

        $pages = [
            'edit-tags.php',
            'edit.php',
            'post-new.php',
            'post.php',
            'term.php',
            'edit-tags.php',
        ];
        if (
            ($GLOBALS['pagenow'] ?? false) === 'admin.php' && strpos(($_GET['page'] ?? ''), 'age-gate') !== false
            || in_array(($GLOBALS['pagenow'] ?? false), $pages)) {
                wp_enqueue_media();
                wp_enqueue_style('age-gate-admin', AGE_GATE_URL . 'dist/admin.css', [], AGE_GATE_VERSION);
            // wp_enqueue_script('age-gate-admin', AGE_GATE_URL . 'dist/admin.js', [], AGE_GATE_VERSION, true);
        }
    }

    public function assets()
    {
        $settings = Settings::getInstance();

        wp_enqueue_script('age-gate-all', AGE_GATE_URL . 'dist/all.js', [], AGE_GATE_VERSION, true);

        wp_localize_script( 'age-gate-all', 'age_gate_common', [
            'cookies' => $settings->labelNoCookies,
            'simple' => $settings->simplebar,
        ]);

        wp_register_script('age-gate-shortcode', AGE_GATE_URL . 'dist/shortcode.js', [], AGE_GATE_VERSION, true);

        if ($settings->enqueueCss) {
            wp_register_style('age-gate', AGE_GATE_URL . 'dist/main.css', [], AGE_GATE_VERSION);
        }

        if ($settings->inputAutoTab && !$settings->stepped) {
            wp_enqueue_script('age-gate-autotab', AGE_GATE_URL . 'dist/autotab.js', [], AGE_GATE_VERSION, !$settings->inHeader);
        }


        if ($settings->stepped && $settings->inputType !== 'buttons') {
            wp_enqueue_style('age-gate-stepped', AGE_GATE_URL . 'dist/stepped.css', [], AGE_GATE_VERSION);
            wp_enqueue_script('age-gate-stepped');
        }

        (new Form())
            ->optionStyle()
            ->enqueue();
    }
}
