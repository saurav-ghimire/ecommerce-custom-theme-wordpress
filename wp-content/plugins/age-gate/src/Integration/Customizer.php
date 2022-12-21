<?php

namespace AgeGate\Integration;

use AgeGate\Common\Settings;
use AgeGate\Common\Integration;

class Customizer extends Integration
{
    public function exists()
    {
        return is_customize_preview();
    }

    public function init()
    {
        if ($this->exists()) {
            $settings = Settings::getInstance();
            $settings->isBuilder = true;
        }
    }
}
