<?php

use AgeGate\Common\Form\Validation;

if (!class_exists('Age_Gate_Validation')) {
    class Age_Gate_Validation extends Validation
    {
        public static function add_validator($rule, $callback, $error = 'Unknown error')
        {
            parent::add_validator($rule, $callback, $error);
        }
    }
}
