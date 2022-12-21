<?php

namespace AgeGate\Legacy;

use AgeGate\Common\Settings;
use AgeGate\Common\Form\Submit;


class Check
{
    private $settings;

    public function __construct()
    {
        add_action('init', function() {
            $this->settings = Settings::getInstance();

            if (!$this->settings->disableAjaxFallback) {
                add_action('wp_ajax_ag_check', [$this, 'check']);
                add_action('wp_ajax_nopriv_ag_check', [$this, 'check']);
            }
        });
    }

    public function check()
    {
        wp_send_json((new Submit($_POST, $this->settings))->validate());
        wp_die();
    }
}
