<?php

namespace AgeGate\Presentation;

use AgeGate\Utility\Facades\View;

class Template
{
    public function __construct()
    {
        add_action('age_gate/form', [$this, 'form'], 10);
        add_action('age_gate/form/open', [$this, 'open'], 0);
        add_action('age_gate/form/close', [$this, 'close'], PHP_INT_MAX);
        add_action('age_gate/fields', [$this, 'hidden'], 11);

        if (has_action('age_gate/custom/after')) {
            add_action('age_gate/custom/after', [$this, 'extraWrapperOpen'], 0);
            add_action('age_gate/custom/after', [$this, 'extraWrapperClose'], PHP_INT_MAX);
        }

        if (has_action('age_gate/custom/before')) {
            add_action('age_gate/custom/before', [$this, 'extraWrapperOpen'], 0);
            add_action('age_gate/custom/before', [$this, 'extraWrapperClose'], PHP_INT_MAX);
        }

        // template functions
        add_action('age_gate/logo', 'age_gate_template_logo');
        add_action('age_gate/headline', 'age_gate_template_headline');
        add_action('age_gate/subheadline', 'age_gate_template_subheadline');
        add_action('age_gate/fields', 'age_gate_template_fields');
        add_action('age_gate/errors', 'age_gate_template_errors', 20);
        add_action('age_gate/submit', 'age_gate_template_submit');
        add_action('age_gate/additional', 'age_gate_template_additional');

        add_action('age_gate/remember', 'age_gate_template_remember', 15);
        add_action('age_gate/fields/age_field', 'age_gate_template_age_field');
        add_action('age_gate/form/background', [$this, 'renderVideo']);
    }

    public function open()
    {
        View::render('partials/form/open');
    }

    public function close()
    {
        View::render('partials/form/close');
    }

    public function form()
    {
        View::render('theme::partials/age-gate-form');
    }

    public function hidden()
    {
        View::render('partials/form/fields/hidden');
    }

    public static function logo()
    {
        View::render('theme::partials/decoration/logo');
    }

    public static function headline()
    {
        View::render('theme::partials/decoration/headline');
    }

    public static function subheadline()
    {
        View::render('theme::partials/decoration/subheadline');
    }

    public static function submit()
    {
        View::render('theme::partials/form/submit');
    }

    public static function additional()
    {
        View::render('theme::partials/decoration/content');
    }

    public static function fields($fields)
    {
        View::render('theme::partials/form/sections/' . $fields);
    }

    public static function remember()
    {
        View::render('theme::partials/form/sections/remember');
    }

    public static function age()
    {
        View::render('theme::partials/form/fields/age');
    }

    public function rta()
    {
        View::render('partials/meta/rta');
    }

    public static function errors()
    {
        View::render('partials/form/errors');
    }

    public function extraWrapperOpen()
    {
        View::render('partials/form/open-extra');
    }

    public function extraWrapperClose()
    {
        echo '</div>';
    }

    public function renderVideo($file)
    {
        $type = wp_check_filetype($file);
        if (strpos(($type['type'] ?? ''), 'video') !== false) {
            echo sprintf('<video src="%s" loop muted autoplay></video>', $file);
        }
    }
}
