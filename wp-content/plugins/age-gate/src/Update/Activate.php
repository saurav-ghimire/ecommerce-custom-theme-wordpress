<?php

namespace AgeGate\Update;

use Asylum\Utility\Arr;
use AgeGate\Common\Immutable\Constants;
use AgeGate\Admin\Settings\Tools;
use AgeGate\Admin\Settings\Access;
use AgeGate\Admin\Settings\Content;
use AgeGate\Admin\Settings\Message;
use AgeGate\Admin\Settings\Advanced;
use AgeGate\Admin\Settings\Appearance;
use AgeGate\Admin\Settings\Restriction;
use AgeGate\Update\Migration\Migrate;
use WP_Query;

class Activate
{
    use Restriction;
    use Message;
    use Access;
    use Appearance;
    use Content;
    use Tools;
    use Advanced;

    private static $instance = null;

    private $migrationMap = [

    ];

    private function __construct()
    {
    }

    public static function activate()
    {
        if (self::$instance === null) {
            self::$instance = new Activate();
        }

        // default settings
        $defaultSettings = self::$instance->getDefaultSettings();

        // user settings
        $userSettings = self::$instance->getUserSettings();

        $merge = array_merge(Arr::dot($defaultSettings), Arr::dot($userSettings));

        $filtered = Arr::undot((array_filter($merge, fn ($item) => $item !== null)));


        if ($fromVersion = get_option('wp_age_gate_version', false)) {
            $filtered = (new Migrate())->mapSimpleSettings($filtered);
            update_option('age_gate_updated_from', $fromVersion);
            delete_option('wp_age_gate_version');
        }

        self::$instance->setCapabilities(get_option('age_gate_version', false), $fromVersion);

        if ($filtered['advanced']['css'] ?? false) {
            update_option('age_gate_legacy_css', esc_textarea($filtered['advanced']['css']));

            $cssId = get_theme_mod('custom_css_post_id') ?: false;

            if ($cssId && $post = get_post($cssId)) {
                $content = $post->post_content ?? '';
                $content = $content . "\r\n" . wp_kses($filtered['advanced']['css'], []);
                update_option('age_gate_theme_css', $post->content);

                wp_update_post([
                    'ID' => $cssId,
                    'post_content' => $content,
                ]);
            } else {
                $id = wp_insert_post([
                    'post_type' => 'custom_css',
                    'post_title' => get_option('stylesheet'),
                    'post_status' => 'publish',
                    'post_content' => wp_kses($filtered['advanced']['css'], []),
                ]);

                set_theme_mod('custom_css_post_id', $id);
            }

            unset($filtered['advanced']['css']);
        }

        // always bin
        unset($filtered['advanced']['css_file']);

        foreach ($filtered as $key => $options) {
            // dump(Constants::AGE_GATE_OPTIONS[$key]);
            if (Constants::AGE_GATE_OPTIONS[$key] ?? false) {
                update_option(Constants::AGE_GATE_OPTIONS[$key], $options);
            }
        }

        if (wp_next_scheduled('age_gate/cron_options')) {
            wp_clear_scheduled_hook('age_gate/cron_options');
        }

        update_option('age_gate_version', AGE_GATE_VERSION);
    }

    private function getDefaultSettings()
    {
        $defaults = [];

        foreach (Constants::AGE_GATE_OPTIONS as $set => $option) {
            $method = 'get' . ucfirst($set) . 'Fields';
            $dot = Arr::dot(self::$instance->$method());


            $values = Arr::where($dot, function ($value, $key) {
                return substr($key, -8) === '.default';
            });

            foreach ($values as $key => $value) {
                $exp = '/(?:[0-9]).fields.([a-z_.]+).default/';
                $k = preg_replace($exp, '$1', $key);

                $k = str_replace('.fields', '', $k);
                $defaults[$set][trim($k, '.')] = $values[$key];
            }
        }

        return $defaults;
    }

    private function getUserSettings()
    {
        $user = [];

        foreach (Constants::AGE_GATE_OPTIONS as $set => $option) {
            $user[$set] = get_option($option, null);
        }

        return $user;
    }

    private function setCapabilities($version, $old)
    {
        if (!$version && !$old) {
            // editor / author

            $editor = get_role('editor');

            if ($editor) {
                $editor->add_cap(Constants::MESSAGES);
                $editor->add_cap(Constants::RESTRICTIONS);
                $editor->add_cap(Constants::SET_CONTENT);
                $editor->add_cap(Constants::SET_CUSTOM_AGE);
                $editor->add_cap(Constants::APPEARANCE);
            }


            $author = get_role('author');

            if ($author) {
                $author->add_cap(Constants::SET_CONTENT);
                $author->add_cap(Constants::SET_CUSTOM_AGE);
            }
        }

        // administrator
        $admin = get_role('administrator');

        foreach (Constants::AGE_GATE_PERMISSION_ARRAY as $cap) {
            $admin->add_cap($cap);
        }
    }
}
