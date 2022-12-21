<?php

namespace AgeGate\Common;

use AgeGate\Utility\Cookie;
use AgeGate\Common\Settings;
use AgeGate\Utility\Crawler;

class User
{
    public static function getUser()
    {
        $settings = Settings::getInstance();

        return (object) [
            'age' => Cookie::get($settings->getCookieName()),
            'loggedIn' => is_user_logged_in(),
            'roles' => self::getRoles(),
            'bot' => Crawler::isBot(),
        ];
    }

    private static function getRoles()
    {
        return get_userdata(get_current_user_id())->roles ?? false;
    }
}
