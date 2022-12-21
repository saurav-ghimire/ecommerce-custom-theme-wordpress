<?php

namespace AgeGate\Common\Controller;

use AgeGate\Common\Content;
use AgeGate\Common\Settings;
use AgeGate\Presentation\Title;
use AgeGate\Presentation\Preload;
use AgeGate\Presentation\FocusTrap;
use AgeGate\Presentation\Interaction;

abstract class AbstractController
{
    protected $content;

    protected $settings;

    protected $title;

    public function __construct(Content $content)
    {
        $this->settings = Settings::getInstance();
        $this->content = $content;
        $this->title = new Title($this->content, $this->settings);
        add_action('wp_enqueue_scripts', [$this, 'assets']);

        if ($this->content->isRestricted()) {
            if ($this->settings->rta) {
                add_action('wp_head', [$this, 'rta']);
            }

            if ($this->settings->preload) {
                new Preload($this->settings);
            }

            if ($this->settings->devTools) {
                new Interaction;
            }

            if ($this->settings->focus) {
                new FocusTrap;
            }
        }

        $this->init();
    }

    public function rta()
    {
        // if ($this->settings->rta && $this->content->isRestricted()) {
            echo '<meta name="rating" content="RTA-5042-1996-1400-1577-RTA" />';
        // }
    }

    abstract public function init();

    abstract public function assets();
}
