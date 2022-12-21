<?php

namespace AgeGate\Common;

use AgeGate\Common\User;
use AgeGate\Common\Content;
use AgeGate\Common\Settings;

class Status
{
    private $content;
    private $user;
    private $settings;
    private $restricted = true;
    private $passed = false;

    public function __construct(Content $content)
    {
        $this->content = $content;
        $this->user = User::getUser();
        $this->settings = Settings::getInstance();
        $this->setRestricted();
        $this->setPassed();
    }

    public function setRestricted()
    {
        if ($this->settings->ignoreLogged && $this->user->loggedIn && $this->settings->method !== 'js') {
            $this->restricted = apply_filters(
                'age_gate/unrestricted/logged',
                false,
                $this->user->age,
                $this->content
            );
            return $this;
        }

        if ($this->user->bot && $this->settings->method !== 'js') {
            $this->restricted = false;
            return $this;
        }

        if ($this->content->getType() === 'error' && !$this->settings->error404) {
            $this->restricted = false;
            return $this;
        }

        switch ($this->settings->type) {
            case 'all':
                $this->restricted = !$this->content->getBypass();
                break;
            case 'selected':
                $this->restricted = $this->content->getRestrict();
                break;
        }


        $filter = $this->restricted ? 'restricted' : 'unrestricted';
        $this->restricted = apply_filters('age_gate/' . $filter, $this->restricted, $this->user->age, $this->content);

        return $this;
    }

    public function setPassed()
    {
        $this->passed = $this->user->age >= $this->content->getAge();
        return $this;
    }

    /**
     * Get the value of restricted
     */
    public function getRestricted()
    {
        return $this->restricted;
    }

    /**
     * Get the value of passed
     */
    public function getPassed()
    {
        return $this->passed;
    }
}
