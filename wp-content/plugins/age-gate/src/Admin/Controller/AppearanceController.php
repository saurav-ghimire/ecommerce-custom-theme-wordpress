<?php

namespace AgeGate\Admin\Controller;

use AgeGate\Common\Immutable\Constants as Immutable;
use AgeGate\Admin\Settings\Appearance;
use AgeGate\Common\Admin\AbstractController;

class AppearanceController extends AbstractController
{
    use Appearance;

    // const PERMISSION = Constants::RESTRICTIONS;
    public const PERMISSION = Immutable::APPEARANCE;
    public const OPTION = Immutable::OPTION_APPEARANCE;

    public function register(): void
    {
        $this->menu(__('Appearance', 'age-gate'), self::PERMISSION);
    }

    protected function required(): bool
    {
        return current_user_can(self::PERMISSION);
    }

    protected function data(): array
    {
        return get_option(self::OPTION, []) ?: [];
    }

    protected function fields(): array
    {
        return $this->getAppearanceFields();
    }

    protected function rules() : array
    {
        return [
            'logo' => 'numeric',
            'background_color' => 'ag_hex',
            'background_opacity' => 'float',
            'blur' => 'numeric',
            'background_image' => 'numeric',
            'background_position.y' => 'alpha',
            'background_position.x' => 'alpha',
            'background_image_opacity' => 'float',
            'foreground_color' => 'ag_hex',
            'foreground_opacity' => 'float',
            'text_color' => 'ag_hex',
            'enqueue_css' => 'boolean',
            'exit_transition' => 'alpha_dash',
            'viewport' => 'boolean',
            'input_auto_tab' => 'boolean',
            'switch_title' => 'boolean',
            'simplebar' => 'boolean',
            // 'custom_title' => 'ag_message',
        ];
    }
}
