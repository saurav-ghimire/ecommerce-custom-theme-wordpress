<?php

namespace AgeGate\Admin\Controller;

use AgeGate\Admin\Tools\Reset;
use AgeGate\Admin\Tools\Export;
use AgeGate\Admin\Tools\Import;
use AgeGate\Admin\Settings\Tools;
use AgeGate\Common\Admin\AbstractController;
use AgeGate\Common\Immutable\Constants as Immutable;

class ToolsController extends AbstractController
{
    use Tools;

    // const PERMISSION = Constants::RESTRICTIONS;
    public const PERMISSION = Immutable::TOOLS;
    protected const OPTION = Immutable::OPTION_TOOLS;

    protected $template = 'tools';

    public function register(): void
    {
        $user = wp_get_current_user();

        $possibleCaps = array_values(
            array_intersect(
                array_keys($user->allcaps),
                [
                    self::PERMISSION,
                    Immutable::HARD_RESET,
                    Immutable::EXPORT,
                    Immutable::IMPORT,
                ]
            )
        );

        $cap = $possibleCaps[0] ?? self::PERMISSION;

        $this->menu(__('Tools', 'age-gate'), $cap);

        if (in_array(Immutable::EXPORT, $possibleCaps)) {
            new Export();
        }

        if (in_array(Immutable::IMPORT, $possibleCaps)) {
            new Import();
        }

        if (in_array(Immutable::HARD_RESET, $possibleCaps)) {
            new Reset();
        }
    }

    protected function required(): bool
    {
        return current_user_can(self::PERMISSION) || current_user_can(Immutable::HARD_RESET) || current_user_can(Immutable::EXPORT) || current_user_can(Immutable::IMPORT);
    }

    protected function data(): array
    {
        $this->view->addData([
            'export_options' => Immutable::AGE_GATE_OPTIONS,
            'export_capability' => Immutable::EXPORT,
            'import_capability' => Immutable::IMPORT,
            'reset_capability' => Immutable::HARD_RESET,
        ]);
        return get_option(self::OPTION, []) ?: [];
    }

    protected function fields(): array
    {
        return $this->getToolsFields();
    }

    protected function rules() : array
    {
        return [
            'dev_warning' => 'boolean',
            'dev_endpoint' => 'boolean',
            'feedback' => 'boolean',
        ];
    }
}
