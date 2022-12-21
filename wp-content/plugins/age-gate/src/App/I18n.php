<?php

namespace AgeGate\App;

class I18n
{
    public function __construct()
    {
        add_action('plugins_loaded', [$this, 'registerDomain']);
    }

    public function registerDomain()
    {
        load_plugin_textdomain(
            'age-gate',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );

    }
}
