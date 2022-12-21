<?php

namespace AgeGate\Common\Form;

use AgeGate\Utility\Encrypt;
use Asylum\Validation\Validator;

class Validation extends Validator
{
    protected function filter_decode_age($value, array $params = [])
    {
        return (int) (new Encrypt)->decrypt($value);
    }

    protected function filter_pad($value, array $params)
    {
        if (empty($value)) {
            return $value;
        }
        $length = $params[0] ?? 2;
        $char = $params[1] ?? 0;
        return str_pad($value, $length, $char, STR_PAD_LEFT);
    }

    protected function filter_min_max($value, array $params)
    {
        return min($value, $params[0]);
    }

    protected function validate_nonce($field, array $input, array $param = [], $value = null)
    {
        return wp_verify_nonce($value, 'age_gate_form');
    }

    protected function validate_equals($field, array $input, array $params, $value = null)
    {
        // wp_die($params[0]);
        return $value === $params[0];
    }
}
