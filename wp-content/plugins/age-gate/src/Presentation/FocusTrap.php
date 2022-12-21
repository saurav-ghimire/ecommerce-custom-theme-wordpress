<?php

namespace AgeGate\Presentation;

use AgeGate\Common\Settings;

class FocusTrap
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'assets'], 1);
    }

    public function assets()
    {
        $settings = Settings::getInstance();
        wp_enqueue_script('age-gate-focus', AGE_GATE_URL . 'dist/focus.js', [], AGE_GATE_VERSION, !$settings->inHeader);

        $elements = [
            '.age-gate',
        ];

        if (is_admin_bar_showing()) {
            $elements[] = '#wpadminbar';
        }

        if ($settings->inputType === 'buttons') {
            $focused = null;
        } elseif ($settings->stepped) {
            $focused = 'age_gate[y]';
        } else {
            $focused = sprintf('age_gate[%s]', strtolower($settings->dateFormat[0] ?? 'd'));
        }

        wp_localize_script('age-gate-focus', 'agfocus', [
            'focus' => $focused,
            'elements' => array_merge(
                apply_filters('age_gate/trap_focus/elements', []),
                $elements
            )
        ]);
    }
}
