<?php

namespace Asylum\Utility\Facades;

use Asylum\Utility\StringTemplate as FacadeClass;

class StringTemplate
{
    private static $instance = null;

    public static function __callStatic($method, $arguments)
    {
        if (self::$instance === null) {
            self::$instance = new FacadeClass;
        }

        return call_user_func_array([self::$instance, $method], $arguments);
    }
}
