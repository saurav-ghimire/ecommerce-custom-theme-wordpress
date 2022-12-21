<?php

namespace AgeGate\Admin\Controller;

use Asylum\Utility\Arr;
use AgeGate\Admin\Settings\Message;
use AgeGate\Common\Admin\AbstractController;
use AgeGate\Common\Immutable\Constants as Immutable;

class MessageController extends AbstractController
{
    use Message;
    // const PERMISSION = Constants::RESTRICTIONS;
    public const PERMISSION = Immutable::MESSAGES;
    public const OPTION = Immutable::OPTION_MESSAGE;

    protected function required(): bool
    {
        return current_user_can(self::PERMISSION);
    }

    public function register(): void
    {
        $this->menu(__('Messages', 'age-gate'), self::PERMISSION);
    }

    protected function data(): array
    {
        return Arr::dot(get_option(self::OPTION, []) ?: []);
    }

    protected function fields(): array
    {
        return $this->getMessageFields();
    }

    public function enqueue(): void
    {
    }

    protected function rules() : array
    {
        return [
            // 'headline' => 'ag_message',
            // 'subheadline' => 'ag_message',
            // 'label_remember' => 'alpha_numeric_space',
            // 'label_aria' => 'ag_message',
            // 'label_buttons' => 'ag_message',
            // 'label_yes' => 'alpha_numeric_space',
            // 'label_no' => 'alpha_numeric_space',
            // 'label_day' => 'alpha_numeric_space',
            // 'label_month' => 'alpha_numeric_space',
            // 'label_year' => 'alpha_numeric_space',
            // 'placeholder_day' => 'alpha_numeric_space',
            // 'placeholder_month' => 'alpha_numeric_space',
            // 'placeholder_year' => 'alpha_numeric_space',
            // 'label_submit' => 'alpha_numeric_space',
            // 'label_no_cookies' => 'ag_message',
            // 'error_invalid' => 'ag_message_md',
            // 'error_failed' => 'ag_message_md',
            // 'error_generic' => 'ag_message_md',
        ];
    }
}
