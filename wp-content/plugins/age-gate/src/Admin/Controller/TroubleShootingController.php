<?php

namespace AgeGate\Admin\Controller;

use AgeGate\Common\Immutable\Constants;
use AgeGate\Common\Admin\AbstractController;


class TroubleShootingController extends AbstractController
{
    protected $template = 'troubleshooting';

    public function register(): void
    {
        $this->menu(__('Troubleshooting', 'age-gate'), Constants::ADVANCED);
    }

    protected function required(): bool
    {
        return current_user_can(Constants::ADVANCED);
    }

    protected function data() : array
    {
        return [];
    }

    protected function fields() : array
    {
        return [];
    }
}
