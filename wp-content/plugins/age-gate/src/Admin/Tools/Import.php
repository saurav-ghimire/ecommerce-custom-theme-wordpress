<?php

namespace AgeGate\Admin\Tools;

use AgeGate\Common\Admin\Helper;
use AgeGate\Common\Form\Validation;
use AgeGate\Common\Immutable\Constants as Immutable;

class Import
{
    use Helper;

    public const PERMISSION = Immutable::IMPORT;

    public function __construct()
    {
        add_action('admin_post_age_gate_import', [$this, 'action']);
    }

    public function action()
    {
        if (!current_user_can(self::PERMISSION) || !wp_verify_nonce($_POST['ag_import'] ?? '', 'ag_import')) {
            wp_die('Disallowed action');
        }

        $validator = new Validation;
        $validator->validation_rules([
            'ag_settings_import' => [
                'required_file',
                'extension' => [
                    'json'
                ],
            ],
            'ag_settings_import.type' => 'equals,application/json',
            'data' => 'valid_json_string',

        ]);

        $valid_data = $validator->run(array_merge($_POST, $_FILES, ['data' => $_FILES['ag_settings_import']['tmp_name'] ? file_get_contents($_FILES['ag_settings_import']['tmp_name']) : 's']));

        if ($validator->errors()) {

            $this->redirect($_POST['_wp_http_referer'], 0, 'tools');
        }

        $data = $validator->sanitize(json_decode($valid_data['data'], true));

        $failed = [];

        foreach ($data['options'] ?? [] as $option => $values) {
            if ($option === 'access') {
                global $wp_roles;

                foreach ($values as $capability => $roles) {
                    foreach ($wp_roles->roles as $slug => $role) {
                        if (Immutable::AGE_GATE_ADMIN_PERMISSION[$capability] ?? false) {
                            $cap = Immutable::AGE_GATE_ADMIN_PERMISSION[$capability];
                            $role = get_role($slug);

                            if ($slug === 'administrator') {
                                continue;
                            }

                            if (array_key_exists($slug, $roles)) {
                                if (!$role->has_cap($cap)) {
                                    $role->add_cap($cap);
                                }
                            } else {

                                if ($role->has_cap($cap)) {
                                    $role->remove_cap($cap);
                                }

                            }
                        }
                    }
                }

            } elseif (Immutable::AGE_GATE_OPTIONS[$option] ?? false) {
                update_option(Immutable::AGE_GATE_OPTIONS[$option], $values);
            } else {
                // TODO : Handle this
                $failed[] = wp_generate_uuid4( );
            }
        }


        $this->redirect($_POST['_wp_http_referer'], 1, 'tools');

    }
}
