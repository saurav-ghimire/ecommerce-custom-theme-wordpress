<?php

namespace AgeGate\App;

use AgeGate\Common\Content;
use AgeGate\Common\Settings;
use AgeGate\Integration\Discover;
use AgeGate\Controller\JsController;
use AgeGate\Controller\StandardController;

class AgeGate
{
    protected static $content;

    public function __construct()
    {
        add_action('wp', [$this, 'init'], PHP_INT_MAX);
    }

    public function init()
    {
        if (is_admin()) {
            return;
        }

        new Discover();

        $settings = Settings::getInstance();

        do_action('age_gate/settings', $settings);

        self::$content = new Content();

        if ($settings->method === 'js') {
            new JsController(self::$content);
        } else {
            new StandardController(self::$content);
        }
    }

    public static function getContent()
    {
        return self::$content;
    }
}
