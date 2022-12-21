<?php

namespace AgeGate\Utility;

use AgeGate\Common\Settings;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class Crawler
{
    public static function isBot()
    {
        $settings = Settings::getInstance();
        $bots = apply_filters('age_gate/settings/bots', preg_split('/\r\n|\r|\n/', $settings->userAgents));

        if (!is_array($bots)) {
            $bots = [];
        }

        foreach ($bots as $crawler) {
            if (!empty($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] === $crawler) {
                return true;
            }
        }

        $crawlerDetect = new CrawlerDetect;
        return $crawlerDetect->isCrawler();
    }
}
