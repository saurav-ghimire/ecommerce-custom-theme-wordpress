<?php

namespace AgeGate\Integration;

use AgeGate\Common\Settings;
use AgeGate\Common\Integration;

class Elementor extends Integration
{
    public function exists()
    {
        return class_exists('\\Elementor\\Plugin');
    }

    public function init()
    {
        if ($this->exists()) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
                Settings::getInstance()->isBuilder = true;
            }
            // add_filter('age_gate/restricted', '__return_false');
        }
    }
}
