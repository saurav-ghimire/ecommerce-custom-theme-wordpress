<?php

namespace AgeGate\Utility\Facades;

use AgeGate\App\AgeGate;
use AgeGate\Common\Settings;
use AgeGate\Controller\ViewController;
use AgeGate\Presentation\Attribute;
use Asylum\Utility\View as BaseView;

class View
{
    private static $instance = null;
    private static $viewPath = AGE_GATE_PATH . 'src/Resources/views/public';
    private static $empty = AGE_GATE_PATH . 'src/Resources/views/empty';

    public static function __callStatic($method, $arguments)
    {
        if (self::$instance) {
            return call_user_func_array([self::$instance, $method], $arguments);
        }

        // $theme = is_dir(get_template_directory() . '/age-gate') ? get_template_directory() . '/age-gate' : self::$empty;

        // self::$instance = new BaseView(self::$viewPath);
        self::$instance = (new ViewController)->getView();


        // self::$instance
        //     ->addData(
        //         [
        //             'settings' => Settings::getInstance(),
        //             'content' => AgeGate::getContent(),
        //         ]
        //     )
        //     ->addFunction('attr', [Attribute::class, 'attr']);

        // if (is_dir($theme)) {
        //     self::$instance->addFolder('theme', $theme);
        // }

        return call_user_func_array([self::$instance, $method], $arguments);
    }
}
