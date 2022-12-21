<?php

namespace AgeGate\Admin\Controller;

use Asylum\Utility\Arr;
use AgeGate\Common\Settings;
use AgeGate\Admin\Settings\Content;
use AgeGate\Common\Content as ContentData;
use AgeGate\Common\Admin\AbstractController;
use AgeGate\Common\Immutable\Constants as Immutable;

class ContentController extends AbstractController
{
    use Content;
    public const PERMISSION = Immutable::CONTENT;
    public const OPTION = Immutable::OPTION_CONTENT;

    // protected $template = 'content';

    public function register(): void
    {
        $this->view->addData([
            'noOptions' => __('No terms', 'age-gate')
        ]);
        $this->menu(__('Content', 'age-gate'), self::PERMISSION);
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
        return $this->getContentFields();
    }

    protected function optionStored($data): void
    {
        $data = Arr::undot($data);

        foreach ($data['user'] ?? [] as $id => $options) {
            $content = (new ContentData($id, 'user'));
            $settings = Settings::getInstance();
            // mutli ages?
            if ($settings->multiAge && current_user_can(Immutable::SET_CUSTOM_AGE)) {
                $default = $settings->{$content->getLanguage()}['defaultAge'] ?? $settings->defaultAge;

                if ($options['age'] ?? false) {
                    $age = (int) $options['age'];

                    if ($age === $default) {
                        // remove the meta as we don't need it
                        delete_user_meta($id, Immutable::META_AGE);
                    } else {
                        // add new meta key
                        update_user_meta($id, Immutable::META_AGE, $age);
                    }
                }
            }

            // bypass ?
            if ($settings->type === 'all' && current_user_can(Immutable::SET_CONTENT)) {
                if ($options['bypass'] ?? false) {
                    // add new meta key
                    update_user_meta($id, Immutable::META_BYPASS, 1);
                } else {
                    // remove the meta as we don't need it
                    delete_user_meta($id, Immutable::META_BYPASS);
                }
            }

            // restrict
            if ($settings->type === 'selected' && current_user_can(Immutable::SET_CONTENT)) {
                if ($options['restrict'] ?? false) {
                    // add new meta key
                    update_user_meta($id, Immutable::META_RESTRICT, 1);
                } else {
                    // remove the meta as we don't need it
                    delete_user_meta($id, Immutable::META_RESTRICT);
                }
            }
        }
    }

    protected function rules() : array
    {
        $rules = [];

        foreach ($this->getContentFields() as $set) {
            foreach ($set['fields'] as $name => $field) {
                if ($field['type'] === 'group') {
                    foreach ($field['fields'] as $n => $f) {

                        if ($f['type'] !== 'hidden') {
                            $rules[$name . '.' . $n] = $f['type'] === 'number' ? 'numeric' : 'boolean';
                        }
                    }
                } else {
                    if ($field['type'] !== 'hidden') {
                        $rules[$name] = $field['type'] === 'checkbox' ? 'boolean' : 'numeric';
                    }
                }
            }
        }

        return $rules;
    }
}
