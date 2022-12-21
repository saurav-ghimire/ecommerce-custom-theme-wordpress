<?php

namespace AgeGate\Presentation;

class Attribute
{
    use ClassNames;

    private static $instance = null;

    private function __construct()
    {
        self::formatClasses();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}
