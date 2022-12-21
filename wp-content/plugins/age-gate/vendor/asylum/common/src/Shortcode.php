<?php

namespace Asylum\Common;

abstract class Shortcode
{
    public function __construct()
    {
        add_shortcode($this->shortcode, [$this, 'registerShortcode']);
    }

    abstract public function registerShortcode($atts, $content, $tag);
}
