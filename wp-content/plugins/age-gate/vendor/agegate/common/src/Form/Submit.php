<?php

namespace AgeGate\Common\Form;

use Exception;
use Asylum\Utility\Arr;
use AgeGate\Utility\Age;
use AgeGate\Common\Content;
use AgeGate\Common\Settings;
use AgeGate\Utility\Encrypt;
use Asylum\Utility\StringTemplate;
use Asylum\Utility\Facades\Parsedown;
class Submit
{
    private $validation;

    private $data;
    private $raw;

    private $rules = [];

    private $messages = [];

    private $names = [];

    private $settings;

    private $shortcode = false;

    private $filters = [
        'd' => 'sanitize_numbers|pad,2,0',
        'm' => 'sanitize_numbers|pad,2,0',
        'y' => 'sanitize_numbers',
        'age' => 'decode_age',
    ];

    private $inputType = null;

    public function __construct($data, Settings $settings)
    {
        $this->raw = $data;

        $this->settings = $settings;
        $this->inputType = $this->settings->inputType;
        $data = apply_filters('age_gate/form/post_data', $data);

        try {
            $this->validation = new Validation($this->settings->language ?? 'en');
        } catch (Exception $e) {
            $this->validation = new Validation();
        }


        $this->data = $this->validation->sanitize($data);

        $this->shortcode = $this->data['age_gate']['shortcode'] ?? false;


        if ($this->shortcode) {
            $this->inputType = (new Encrypt())->decrypt($this->shortcode);
        }


        $this->data['age_gate'] = $this->validation->filter($this->data['age_gate'] ?? [], $this->filters);

        $this->setRules();
        $this->setMessages();
        $this->setFieldNames();
        $this->setValidators();
    }

    public function validate()
    {
        $this->validation->validation_rules($this->rules);
        $this->validation->set_field_names($this->names);
        $this->validation->set_error_messages($this->messages);


        if ($this->inputType !== 'buttons') {
            $this->data['age_gate']['dob'] = (new StringTemplate())->render('{y}-{m}-{d}', $this->data['age_gate']);
        }

        $data = $this->validation->run($this->data);

        if ($this->validation->errors()) {
            do_action(
                'age_gate/submission/failed',
                $this->hookData($this->data),
                $this->validation->get_errors_array(),
                $this->customData($this->data)
            );

            return [
                'errors' => $this->filterErrors($this->validation->get_errors_array()),
                'status' => false,
                'redirect' => esc_url_raw(apply_filters('age_gate/failed/redirect', $this->settings->redirect, $data)),
                'values' => $data,
                'set_cookie' => apply_filters('age_gate/cookie/set', true, isset($this->data['age_gate']['remember'])),
            ];
        }

        do_action(
            'age_gate/submission/success',
            $this->hookData($this->data),
            [],
            $this->customData($this->data)
        );

        global $wp;

        return [
            'data' => array_merge(
                [
                    'user_age' => $this->settings->anonymous ? 1 : ($this->inputType === 'buttons' ? $this->data['age_gate']['age'] : Age::calculateAge($data['age_gate']['dob'])),
                ],
                $data
            ),
            'status' => true,
            'redirect' => esc_url_raw(apply_filters('age_gate/success/redirect', ($this->settings->method !== 'js' ? home_url($wp->request) : ''), $data)),
            'cookieLength' => $this->getCookieLength($data['age_gate']['remember'] ?? false),
            'transition' => $this->settings->exitTransition,
            'user_data' => apply_filters('age_gate/response/user_data', [], $this->data, $this->raw),
            'set_cookie' => apply_filters('age_gate/cookie/set', true, isset($this->data['age_gate']['remember'])),
        ];

        // return (int) (new Encrypt)->decrypt($this->data['age_gate']['age']);
    }

    private function filterErrors($errors)
    {
        if ($this->settings->method === 'standard') {
            // errors are parsed and espaced in the view in standard
            return $errors;
        }

        foreach ($errors as $key => $error) {
            $errors[$key] = Parsedown::line($error);
        }

        return $errors;
    }

    private function setRules()
    {
        $customRules = apply_filters('age_gate/validation/rules', []);
        $minYear = apply_filters('age_gate/form/min_year', 1900);
        $maxYear = apply_filters('age_gate/form/max_year', date('Y'));

        if (!is_array($customRules)) {
            _doing_it_wrong('age_gate/validation/rules', 'This filter expects an <code>Array</code> to be returned', '3.0.0');
            $customRules = [];
        }

        // $customRules = collect($customRules)->mapWithKeys(function ($rule, $key) {
        //     if (substr($key, 0, 10) !== 'ag_custom_') {
        //         return ['ag_custom_' . $key => $rule];
        //     }

        //     return [$key => $rule];
        // })->all();

        if ($this->inputType === 'buttons') {
            $rules = [
                'age_gate.confirm' => 'required|equals,1',
            ];
        } else {
            $rules = [
                'age_gate.d' => 'required|numeric|min_len,1|max_len,2|max_numeric,31',
                'age_gate.m' => 'required|numeric|min_len,1|max_len,2|max_numeric,12',
                'age_gate.y' => 'required|numeric|min_len,4|max_len,4|min_numeric,'. $minYear .'|max_numeric,' . $maxYear,
                'age_gate.dob' => 'required|date|min_age,' . ($this->data['age_gate']['age'] ?? 0)
            ];
        }

        $rules['age_gate.age'] = 'required';

        if ($this->shortcode) {
            $this->rules = $rules;
            return;
        }

        $this->rules = array_merge($customRules, $rules);
    }

    private function setMessages()
    {
        $custom = apply_filters('age_gate/validation/messages', []);

        if (!is_array($custom)) {
            $custom = [];
        }

        $this->messages = array_merge(
            $custom,
            [
                'equals' => $this->settings->errorFailed,
                'min_age' => $this->settings->errorFailed,
            ]
        );
    }

    private function setFieldNames()
    {
        $custom = apply_filters('age_gate/validation/names', []);

        if (!is_array($custom)) {
            $custom = [];
        }

        $this->names = array_merge(
            [
                'age_gate.age' => 'age',
                'age_gate.d' => 'day',
                'age_gate.m' => 'month',
                'age_gate.y' => 'year',
                'age_gate.dob' => 'date of birth',
            ],
            $custom
        );
    }

    private function setValidators()
    {
        do_action('age_gate/validation/validators', Validation::class);
    }

    private function hookData($data)
    {
        $hook = [
            'age_gate_age' => $data['age_gate']['age'] ?? $this->settings->defaultAge,
            'age_gate_content' => site_url($data['_wp_http_referer'] ?? '/'),

        ];

        if ($this->settings->inputType !== 'buttons') {
            $hook['age_gate_d'] = $data['age_gate']['d'] ?? false;
            $hook['age_gate_m'] = $data['age_gate']['m'] ?? false;
            $hook['age_gate_y'] = $data['age_gate']['y'] ?? false;
        } else {
            $hook['age_gate_confirm'] = $data['age_gate']['confirm'] ?? false;
        }

        return $hook;
    }

    private function customData($data)
    {
        unset(
            $data['age_gate']['age'],
            $data['age_gate']['confirm'],
            $data['_wp_http_referer'],
            $data['age_gate']['d'],
            $data['age_gate']['m'],
            $data['age_gate']['y'],
            $data['age_gate']['nonce'],
        );


        return Arr::dot($data);
    }

    protected function getCookieLength($checked = false)
    {
        if (!$checked) {
            $length = apply_filters('age_gate/cookie/length', 0);
            $time = apply_filters('age_gate/cookie/time', 'days');
            return $this->compileCookieLength($length, $time);
        }

        return $this->compileCookieLength($this->settings->rememberLength, $this->settings->rememberTime);
    }

    protected function compileCookieLength($length, $time = 'days')
    {
        if (!$length || !is_numeric($length)) {
            return 0;
        }

        if ($this->settings->method === 'js') {
            switch ($time) {
                case 'hours':
                    return (int) $length / 24;
                case 'minutes':
                    return (int) $length / 24 / 60;
                default:
                    return (int) $length;
            }
        }

        return strtotime("+{$length} {$time}");
    }
}
