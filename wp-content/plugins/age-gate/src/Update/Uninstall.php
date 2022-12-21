<?php

namespace AgeGate\Update;

use Asylum\Utility\Storage;
use AgeGate\Common\Immutable\Constants;

class Uninstall
{
    public static function uninstall()
    {
        // delete options
        foreach (Constants::AGE_GATE_OPTIONS as $option) {
            delete_option($option);
        }

        // delete legacy options
        delete_option('age_gate_updated_from');
        delete_option('wp_age_gate_restrictions');
        delete_option('wp_age_gate_messages');
        delete_option('wp_age_gate_validation_messages');
        delete_option('wp_age_gate_appearance');
        delete_option('wp_age_gate_advanced');
        delete_option('wp_age_gate_access');
        delete_option('age_gate_dev');

        // delete custom css file
        // no longer required
        // wp_delete_file( Storage::storageDir('css', 'age-gate') . '/custom.css' );

        // remove caps
        foreach (wp_roles()->roles ?? [] as $role => $data) {
            $r = get_role($role);

            foreach (Constants::AGE_GATE_PERMISSION_ARRAY as $cap) {
                $r->remove_cap($cap);
            }
        }

        // delete version
        delete_option('age_gate_version');
    }
}
