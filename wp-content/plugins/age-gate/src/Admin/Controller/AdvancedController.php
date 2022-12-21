<?php

namespace AgeGate\Admin\Controller;

use Asylum\Utility\Storage;
use AgeGate\Admin\Settings\Advanced;
use AgeGate\Common\Admin\AbstractController;
use AgeGate\Common\Immutable\Constants as Immutable;

class AdvancedController extends AbstractController
{
    use Advanced;
    public const PERMISSION = Immutable::ADVANCED;
    public const OPTION = Immutable::OPTION_ADVANCED;

    public function register(): void
    {
        $this->menu(__('Advanced', 'age-gate'), self::PERMISSION);
    }

    protected function required(): bool
    {
        return current_user_can(self::PERMISSION);
    }

    protected function data(): array
    {
        return get_option(Immutable::OPTION_ADVANCED, []) ?: [];
    }

    protected function fields(): array
    {
        return $this->getAdvancedFields();
    }

    protected function rules() : array
    {
        return [
            'method' => 'alpha',
            'munge' => 'boolean',
            'in_header' => 'boolean',
            'preload' => 'boolean',
            'focus' => 'boolean',
            'dev_tools' => 'boolean',
            'rta' => 'boolean',
            'toolbar' => 'boolean',
            'anonymous' => 'boolean',
            'cookie_name' => 'ag_alpha_underscore',
            'css_type' => "alpha_numeric",
        ];
    }
}
