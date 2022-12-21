<?php

namespace Asylum\Utility\Facades;

use Parsedown as FacadeClass;

class Parsedown
{
    private static $instance = null;

    public static function __callStatic($method, $arguments)
    {
        if (self::$instance === null) {
            self::$instance = new FacadeClass();
            self::$instance->setSafeMode(true);

        }

        return call_user_func_array([self::$instance, $method], $arguments);
    }
}
