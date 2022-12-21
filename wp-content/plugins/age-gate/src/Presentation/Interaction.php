<?php

namespace AgeGate\Presentation;

use AgeGate\Common\Settings;

class Interaction
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'assets'], 1);
    }

    public function assets()
    {
        $settings = Settings::getInstance();
        wp_enqueue_script('age-gate-interaction', AGE_GATE_URL . 'dist/interaction.js', [], AGE_GATE_VERSION, !$settings->inHeader);
    }
}
