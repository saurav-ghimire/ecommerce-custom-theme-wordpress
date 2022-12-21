<?php

namespace AgeGate\Admin\Controller;

use Asylum\Utility\Arr;
use Asylum\Utility\Notice;
use Asylum\Validation\Validator;
use AgeGate\Common\Admin\AbstractController;
use AgeGate\Common\Immutable\Constants as Immutable;

class AccessController extends AbstractController
{
    // const PERMISSION = Constants::RESTRICTIONS;
    public const PERMISSION = Immutable::ACCESS;
    public const OPTION = Immutable::OPTION_ACCESS;

    protected $template = 'access';

    public function register(): void
    {
        $this->menu(__('Access', 'age-gate'), self::PERMISSION);
    }

    protected function required(): bool
    {
        return current_user_can(self::PERMISSION);
    }

    protected function data(): array
    {
        global $wp_roles;

        return [
            'roles' => $wp_roles->roles ?? [],
            'sections' => Immutable::AGE_GATE_PERMISSION_ARRAY,
        ];
    }

    public function store()
    {
        global $wp_roles;

        $validation = new Validator;
        $data = $validation->sanitize($_POST);

        if (Validator::is_valid($data['ag_settings'], $this->rules()) !== true) {
            Notice::add(__('Invalid form data', 'age-gate'));
            $this->redirect($data['_wp_http_referer'], 0);
            exit;
        }

        $set = array_fill_keys(array_keys(Immutable::AGE_GATE_PERMISSION_ARRAY), []);



        $distribution = array_merge($set, $data['ag_settings'] ?? []);


        foreach ($distribution as $cap => $roles) {
            foreach ($wp_roles->roles as $key => $role) {
                if ($key === 'administrator') {
                    continue;
                }

                $role = get_role($key);

                $capability = Immutable::AGE_GATE_PERMISSION_ARRAY[$cap];

                if (array_key_exists($key, $roles)) {
                    if (!$role->has_cap($capability)) {
                        $role->add_cap($capability);
                    }
                } else {
                    if ($role->has_cap($capability)) {
                        $role->remove_cap($capability);
                    }
                }
            }
        }

        // set all on admin anyway
        $admin = get_role('administrator');

        foreach (Immutable::AGE_GATE_PERMISSION_ARRAY as $cap) {
            if (!$admin->has_cap($cap)) {
                $admin->add_cap($cap);
            }
        }

        $this->redirect($data['_wp_http_referer']);
    }

    protected function fields(): array
    {
        return [];
    }

    protected function rules() : array
    {
        $validation = new Validator;
        $data = $validation->sanitize($_POST);
        return array_fill_keys(array_keys(Arr::dot($data['ag_settings'])), 'boolean');
    }
}
