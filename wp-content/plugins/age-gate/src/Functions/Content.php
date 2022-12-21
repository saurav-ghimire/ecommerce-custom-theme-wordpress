<?php

use AgeGate\Common\Content;

if (!function_exists('age_gate_is_restricted')) {
    function age_gate_is_restricted($id = null, $type = 'post')
    {
        return (new Content($id, $type))->isRestricted();
    }
}

if (!function_exists('age_gate_has_restriction')) {
    function age_gate_has_restriction($id = null, $type = 'post')
    {

        return (new Content($id, $type))->getRestricted();
    }
}

if (!function_exists('age_gate_content')) {
    function age_gate_content($id = null, $type = 'post')
    {

        return new Content($id, $type);
    }
}

if (!function_exists('age_gate_error')) {
    /**
     * @deprecated 3.0.0
     */
    function age_gate_error($key)
    {
        return null;
    }
}

if (!function_exists('age_gate_set_value')) {
    /**
     * @deprecated 3.0.0
     */
    function age_gate_set_value($key)
    {
        return null;
    }
}

if (!function_exists('age_gate_status')) {
    /**
     * @deprecated 3.0.0
     */
    function age_gate_status()
    {
        return null;
    }
}
