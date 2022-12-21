<?php

use AgeGate\Presentation\Template;

if (!function_exists('age_gate_template_logo')) {
    function age_gate_template_logo()
    {
        Template::logo();
    }
}

if (!function_exists('age_gate_template_headline')) {
    function age_gate_template_headline()
    {
        Template::headline();
    }
}

if (!function_exists('age_gate_template_subheadline')) {
    function age_gate_template_subheadline()
    {
        Template::subheadline();
    }
}

if (!function_exists('age_gate_template_fields')) {
    function age_gate_template_fields($fields)
    {
        Template::fields($fields);
    }
}

if (!function_exists('age_gate_template_submit')) {
    function age_gate_template_submit()
    {
        Template::submit();
    }
}

if (!function_exists('age_gate_template_additional')) {
    function age_gate_template_additional()
    {
        Template::additional();
    }
}

if (!function_exists('age_gate_template_remember')) {
    function age_gate_template_remember()
    {
        Template::remember();
    }
}

if (!function_exists('age_gate_template_age_field')) {
    function age_gate_template_age_field()
    {
        Template::age();
    }
}

if (!function_exists('age_gate_template_errors')) {
    function age_gate_template_errors()
    {
        Template::errors();
    }
}

if (!function_exists('age_gate_add_attribute')) {
    function age_gate_add_attribute($element, $attribute, $value)
    {
        \AgeGate\Presentation\Attribute::addAttribute($element, $attribute, $value);
    }
}
