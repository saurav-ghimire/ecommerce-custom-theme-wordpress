<?php

namespace AgeGate\Shortcode;

use AgeGate\Common\Content;

class ShortcodeContent extends Content
{
    public function __construct()
    {
        $this->setType('shortcode');
        $this->setDefaultAge(21);
        $this->setAge(21);
    }
}
