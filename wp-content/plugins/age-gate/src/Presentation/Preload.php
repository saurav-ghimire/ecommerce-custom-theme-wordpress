<?php

namespace AgeGate\Presentation;

use AgeGate\Common\Settings;

class Preload
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        add_action('wp_head', [$this, 'assets'], 3);
    }

    public function assets()
    {
        if ($this->settings->logo) {
            echo sprintf('<link rel="preload" href="%s" as="image" />', $this->settings->logo);
            echo "\r\n";
        }

        if ($this->settings->backgroundImage) {
            $file = wp_get_attachment_url($this->settings->backgroundImage);
            $type = wp_check_filetype($file);

            if (strpos($type['type'], 'image') !== false && $this->settings->logo !== $this->settings->backgroundImage) {
                echo sprintf('<link rel="preload" href="%s" as="image" />', $file);
                echo "\r\n";
            }
        }
    }
}
