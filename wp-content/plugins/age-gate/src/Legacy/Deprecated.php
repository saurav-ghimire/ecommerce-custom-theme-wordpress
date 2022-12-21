<?php

namespace AgeGate\Legacy;

use Exception;
use AgeGate\Common\Settings;

require_once dirname(__FILE__) . '/Validation.php';

class Deprecated
{
    public function __construct()
    {
        add_action('age_gate/validation/validators', [$this, 'addLegacyValidators']);
        add_filter('age_gate/validation/rules', [$this, 'addLegacyRules']);

        if (has_filter('post_age_gate_custom_fields')) {
            add_action('age_gate/custom/after', [$this, 'addLegacyAfter']);
        }

        if (has_filter('pre_age_gate_custom_fields')) {
            add_action('age_gate/custom/before', [$this, 'addLegacyBefore']);
        }
        add_filter('age_gate/validation/messages', [$this, 'addLegacyMessages']);
        add_filter('age_gate/validation/names', [$this, 'addLegacyNames']);
        add_filter('age_gate/cookie/set', [$this, 'filterLegacyCookie'], 10, 2);

        // restricted filters...
        add_filter('age_gate/unrestricted/logged', [$this, 'addLegacyLogged'], 1, 3);
        add_filter('age_gate/restricted', [$this, 'addLegacyRestricted'], 1, 3);
        add_filter('age_gate/unrestricted', [$this, 'addLegacyUnrestricted'], 1, 3);

        // logo
        add_filter('age_gate/logo/src', [$this, 'addLegacyLogo'], 1, 2);

    }

    public function addLegacyLogo($logo)
    {
        $legacyLogo = apply_filters('age_gate_logo', $logo, $logo);

        preg_match('/src=(?:"|\')(.*?)(?:"|\')/', $legacyLogo, $matches);

        return $matches[1] ?? $logo;

    }

    public function addLegacyValidators($validation)
    {
        try {
            do_action('age_gate_add_validators');
        } catch(Exception $e) {
            // dd($e->getMessage());
        }
    }

    function addLegacyRules($rules)
    {
        return apply_filters('age_gate_validation', $rules);
    }

    public function addLegacyBefore()
    {
        echo apply_filters('pre_age_gate_custom_fields', '');
    }

    public function addLegacyAfter()
    {
        echo apply_filters('post_age_gate_custom_fields', '');
    }

    public function addLegacyMessages($messages)
    {
        return apply_filters('age_gate_validation_messages', $messages);
    }

    public function addLegacyNames($names)
    {
        return apply_filters('age_gate_field_names', $names);
    }

    public function filterLegacyCookie($set, $remember)
    {
        return apply_filters('age_gate_set_cookie', $set, $remember);
    }

    public function addLegacyLogged($status, $age, $content) {
        $meta = $this->transformMeta($age, $content);
        return apply_filters('age_gate_unrestricted_logged_in', $status, $meta);
    }

    public function addLegacyRestricted($status, $age, $content) {
        $meta = $this->transformMeta($age, $content);
        return apply_filters('age_gate_restricted', $status, $meta);
    }

    public function addLegacyUnrestricted($status, $age, $content) {
        $meta = $this->transformMeta($age, $content);
        return apply_filters('age_gate_unrestricted_unrestricted', $status, $meta);
    }

    private function transformMeta($age, $content)
    {
        $settings = Settings::getInstance();
        $data = (object) [
            'age' => $content->getAge(),
            'type' => $content->getType(),
            'bypass' => $content->getBypass(),
            'restrict' => $content->getRestricted(),
            'restrictions' => (object) [
                'min_age' => $settings->defaultAge,
                'restriction_type' => $settings->type,
                'multi_age' => $settings->multiAge,
                'restrict_register' => false,
                'input_type' => $settings->inputType,
                'stepped' => $settings->stepped,
                'button_order' => $settings->buttonOrder,
                'inherit_category' => $settings->inherit,
                'remember' => $settings->remember,
                'remember_days' => $settings->rememberLength,
                'remember_timescale' => $settings->rememberTime,
                'remember_auto_check' => $settings->rememberAutoCheck,
                'date_format' => strtolower(str_replace(' ', '', $settings->dateFormat)), //'ddmmyyyy',
                'ignore_logged' => $settings->ignoreLogged,
                'rechallenge' => $settings->rechallenge,
                'fail_link_title' => '',
                'fail_link' => $settings->redirect,
            ]
        ];

        return $data;
    }
}
