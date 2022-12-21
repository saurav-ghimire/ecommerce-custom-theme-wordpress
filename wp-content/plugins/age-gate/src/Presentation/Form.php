<?php

namespace AgeGate\Presentation;

use Asylum\Utility\Color;
use Asylum\Utility\Storage;
use AgeGate\Common\Settings;

class Form
{
    private $settings;
    private $custom = 'age-gate-custom';
    private $options = 'age-gate-options';

    public function __construct()
    {
        $this->settings = Settings::getInstance();
    }

    public function optionStyle()
    {
        wp_register_style($this->custom, false);
        wp_add_inline_style($this->custom, strip_tags(stripslashes(html_entity_decode($this->getStyleOptions(), ENT_QUOTES))));

        return $this;
    }

    public function customStyle()
    {
        if (!trim($this->settings->css)) {
            return $this;
        }

        if ($this->settings->cssFile && file_exists(Storage::storageDir('css', 'age-gate') . '/custom.css')) {
            $deps = [];

            if ($this->settings->enqueueCss) {
                $deps = ['age-gate'];
            }

            wp_register_style($this->custom, Storage::storageUrl('css', 'age-gate') . '/custom.css', $deps, AGE_GATE_VERSION);
        } else {
            wp_register_style($this->custom, false);

            wp_add_inline_style($this->custom, strip_tags(stripslashes(html_entity_decode($this->settings->css, ENT_QUOTES))));
        }

        return $this;
    }

    public function enqueue()
    {
        wp_enqueue_style($this->custom);
        wp_enqueue_style($this->options);
    }

    private function getStyleOptions()
    {
        $options = [
            'backgroundColor',
            'backgroundOpacity',
            'backgroundImage',
            'backgroundPosition',
            'backgroundImageOpacity',
            'blur',
            'foregroundColor',
            'foregroundOpacity',
            'textColor',
        ];

        $styles = '';

        if ($this->settings->backgroundColor) {
            $styles .= $this->variable('--ag-background-color', Color::hex2rgb($this->settings->backgroundColor,  $this->settings->backgroundOpacity !== false ? (float) $this->settings->backgroundOpacity : 1));
        }

        if ($this->settings->backgroundImage) {
            $styles .= $this->variable('--ag-background-image', 'url(' . $this->settings->backgroundImage . ')');
        }

        if ($this->settings->backgroundPosition) {
            $styles .= $this->variable('--ag-background-image-position', $this->settings->backgroundPosition['x'] . ' ' . $this->settings->backgroundPosition['y']);
        }

        if ($this->settings->backgroundImageOpacity) {
            $styles .= $this->variable('--ag-background-image-opacity', $this->settings->backgroundImageOpacity !== false ? (float) $this->settings->backgroundImageOpacity : 1);
        }


        if ($this->settings->foregroundColor) {
            $styles .= $this->variable('--ag-form-background', Color::hex2rgb($this->settings->foregroundColor, $this->settings->foregroundOpacity !== false ? (float) $this->settings->foregroundOpacity : 1));
        }

        if ($this->settings->textColor) {
            $styles .= $this->variable('--ag-text-color', $this->settings->textColor);
        }


        if ($this->settings->blur) {
            $styles .= $this->variable('--ag-blur', $this->settings->blur . 'px');
        }

        $styles = trim($styles);
        wp_register_style($this->options, false, ['age-gate']);

        if ($styles) {
            $styles = sprintf(':root{%s}', $styles);

            wp_add_inline_style($this->options, strip_tags(stripslashes(html_entity_decode($styles, ENT_QUOTES))));
        }

        if ($this->settings->blur) {
            if (is_admin_bar_showing()) {
                $not = apply_filters('age_gate/presentation/blur/ignore', ['#wpadminbar']);
            } else {
                $not = apply_filters('age_gate/presentation/blur/ignore', []);
            }

            if (is_array($not)) {
                $not = implode(',', $not);
            }

            if (!$not) {
                wp_add_inline_style($this->options, '.age-gate-wrapper ~ *,.age-gate__wrapper ~ * {filter: blur(var(--ag-blur));}');
            } else {
                wp_add_inline_style($this->options, sprintf('.age-gate-wrapper ~ *:not('. $not .'),.age-gate__wrapper ~ *:not('. $not .') {filter: blur(var(--ag-blur));}', $not));
            }
        }

        return $styles;


        // dd($this->settings);
    }

    private function variable($key, $value)
    {
        return sprintf('%s: %s;', $key, $value);
    }
}
