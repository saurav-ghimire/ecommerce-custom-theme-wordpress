<?php

namespace AgeGate\Update\Migration;

use Asylum\Utility\Arr;
use Asylum\Utility\Storage;
use Asylum\Utility\HtmlToMarkdown;
use League\HTMLToMarkdown\HtmlConverter;

class Migrate
{
    private $mapTwo = [
        "restrictions.min_age" => "restriction.default_age",
        "restrictions.restriction_type" => "restriction.type",
        "restrictions.multi_age" => "restriction.multi_age",
        "restrictions.stepped" => "restriction.stepped",
        "restrictions.input_type" => "restriction.input_type",

        "restrictions.remember" => "restriction.remember",
        "restrictions.remember_days" => "restriction.remember_length.remember_length",
        "restrictions.remember_timescale" => "restriction.remember_length.remember_time",
        "restrictions.remember_auto_check" => "restriction.remember_auto_check",
        "restrictions.ignore_logged" => "restriction.ignore_logged",
        "restrictions.rechallenge" => "restriction.rechallenge",
        "restrictions.fail_link_title" => null,
        "restrictions.fail_link" => "restriction.redirect",
        "restrictions.button_order" => "restriction.button_order",

        //
        "messages.instruction" => 'message.headline',
        "messages.messaging" => 'message.subheadline',
        "messages.invalid_input_msg" => 'message.error_invalid',
        "messages.under_age_msg" => 'message.error_failed',
        "messages.generic_error_msg" => "message.error_generic",
        "messages.remember_me_text" => "message.label_remember",
        "messages.yes_no_message" => "message.label_buttons",
        "messages.yes_text" => "message.label_yes",
        "messages.no_text" => "message.label_no",
        "messages.button_text" => "message.label_submit",
        "messages.cookie_message" => "message.label_no_cookies",
        "messages.text_day" => "message.label_day",
        "messages.text_month" => "message.label_month",
        "messages.text_year" => "message.label_year",
        "messages.aria_label" => "message.label_aria",

        //
        "validation_messages.validate_required" => null,
        "validation_messages.validate_numeric" => null,
        "validation_messages.validate_max_len" => null,
        "validation_messages.validate_min_len" => null,
        "validation_messages.validate_min_numeric" => null,
        "validation_messages.validate_max_numeric" => null,
        "validation_messages.validate_min_age" => null,
        "validation_messages.validate_date" => null,

        //
        "appearance.logo" => "appearance.logo",
        "appearance.background_colour" => "appearance.background_color",
        "appearance.background_opacity" => "appearance.background_opacity",
        "appearance.background_image" => "appearance.background_image",
        "appearance.background_image_opacity" => "appearance.background_image_opacity",
        "appearance.foreground_colour" => "appearance.foreground_color",
        "appearance.foreground_opacity" => "appearance.foreground_opacity",
        "appearance.text_colour" => "appearance.text_color",
        "appearance.styling" => "appearance.enqueue_css",
        "appearance.device_width" => "appearance.viewport",
        "appearance.switch_title" => "appearance.switch_title",
        "appearance.custom_title" => "appearance.custom_title",
        "appearance.auto_tab" => "appearance.input_auto_tab",
        "appearance.title_separator" => null,
        "appearance.title_format" => null,
        "appearance.transition" => "appearance.exit_transition", // check values
        "appearance.background_pos_x" => "appearance.background_position.x",
        "appearance.background_pos_y" => "appearance.background_position.y",
        "appearance.blur_amount" => "appearance.blur",

        //

        "advanced.save_to_file" => "advanced.css_file", // if this and that are true, move to new location
        "advanced.custom_css" => "advanced.css",
        "advanced.dev_notify" => "tools.dev_versions",
        "advanced.dev_hide_warning" => "tools.dev_warning",
        "advanced.anonymous_age_gate" => "advanced.anonymous",
        "advanced.endpoint" => null, // only REST
        "advanced.use_default_lang" => null, // default
        "advanced.use_meta_box" => null, //
        "advanced.inherit_taxonomies.category" => null, // need updating
        // "advanced.custom_bots" => "advanced.user_agents",
        "advanced.enable_quicktags" => null,
        "advanced.full_nav" => "advanced.toolbar",
        "advanced.enable_import_export" => null,
        "advanced.filter_qs" => null,
        "advanced.post_to_self" => null,
        "advanced.js_hooks" => null,
        "advanced.cookie_name" => "advanced.cookie_name",
        "advanced.rta_tag" => "advanced.rta",
        "advanced.munge_options" => "advanced.munge",
        "advanced.trap_focus" => "advanced.focus",
        "advanced.disable_right" => "advanced.dev_tools",
        "advanced.preload_images" => "advanced.preload",
    ];

    private $mapTwoSpecial = [
        "advanced.use_js" => ["key" => "advanced.method", "callback" => "getMethod"], // set, js, not set, standard
        // // will need a loop to content.disable.<posttype>
        // "access.post",
        // "access.page",
        // "access.attachment",
        // "access.revision",
        // "access.nav_menu_item",
        // "access.custom_css",
        // "access.customize_changeset",
        // "access.oembed_cache",
        // "access.user_request",
        // "access.wp_block",
        // "access.wp_template",
        // "access.wp_template_part",
        // "access.wp_global_styles",
        // "access.wp_navigation",
        // "access.acf-field-group",
        // "access.acf-field",

        "restrictions.date_format" => [
            'key' => "restriction.date_format",
            'callback' => 'getDateFormat',
        ],

        "restrictions.inherit_category" => [
            'key' => '',
            'callback' =>  'getInherited', // Work out what the hell is happening!
        ],
        "messages.additional" => [
            'key' => "message.content",
            'callback' => 'getMarkdown', // convert to markdown!
        ],

        // // Languages :???
        // "restrictions.lang.fr.min_age",
        // "restrictions.lang.fr.date_format",
        // "restrictions.lang.fr.fail_link_title",
        // "restrictions.lang.fr.fail_link",
    ];

    public function mapSimpleSettings($settings)
    {
        $legacy = [
            'restrictions' => get_option('wp_age_gate_restrictions', []),
            'messages' => get_option('wp_age_gate_messages', []),
            'validation_messages' => get_option('wp_age_gate_validation_messages', []),
            'appearance' => get_option('wp_age_gate_appearance', []),
            'advanced' => get_option('wp_age_gate_advanced', []),
            'access' => get_option('wp_age_gate_access', []),
        ];

        $v3 = Arr::dot($settings);
        $v2 = Arr::dot($legacy);


        // map all the 1-to-1 settings
        foreach ($v2 as $key => $option) {
            if (array_key_exists($key, $this->mapTwo)) {
                if ($this->mapTwo[$key]) {
                    $v3[$this->mapTwo[$key]] = $option;
                };
            }


            if (array_key_exists($key, $this->mapTwoSpecial)) {
                if ($this->mapTwoSpecial[$key]) {
                    $mapped = $this->{$this->mapTwoSpecial[$key]['callback']}($option, $v2);

                    if (!is_array($mapped)) {
                        $v3[$this->mapTwoSpecial[$key]['key']] = $mapped;
                    } else {
                        $v3 = array_merge($v3, $mapped);
                    }
                };
            }

        }

        foreach ($legacy['access'] ?? [] as $type => $value) {
            $v3['content.disable.' . $type] = $value;
        }

        // Move custom CSS to customizer

        // languages...
        foreach ($legacy as $key => $options) {
            foreach($options['lang'] ?? [] as $code => $options) {
                foreach ($options as $option => $value) {
                    if (array_key_exists($key . '.' . $option, $this->mapTwo)) {
                        if ($this->mapTwo[$key . '.' . $option]) {

                            $parts = explode('.', $this->mapTwo[$key . '.' . $option] ?? '');
                            array_splice($parts, 1, 0, [$code]);
                            $v3Key = implode('.', $parts);

                            $v3[$v3Key] = $value;
                        };
                    }

                    if (array_key_exists($key . '.' . $option, $this->mapTwoSpecial)) {
                        if ($this->mapTwoSpecial[$key . '.' . $option]) {
                            $mapped = $this->{$this->mapTwoSpecial[$key . '.' . $option]['callback']}($option, $v2);

                            $parts = explode('.', $this->mapTwoSpecial[$key . '.' . $option]['key'] ?? '');
                            array_splice($parts, 1, 0, [$code]);
                            $v3Key = implode('.', $parts);

                            if (!is_array($mapped)) {
                                $v3[$v3Key] = $mapped;
                            } else {
                                $v3 = array_merge($v3, $mapped);
                            }
                        };
                    }



                    // dump($key . '.' . $option);

                    // $parts = explode('.', $this->mapTwo[$key . '.' . $option] ?? '');

                    // array_splice($parts, 1, 0, [$code]);
                    // dump(implode('.', $parts));
                    // dump($option, $value);
                }
            }
        }

        // default updates to V@ styles?
        $v3['advanced.css_type'] = 'v2';

        $v3['advanced.user_agents'] = implode("\r\n", $legacy['advanced']['custom_bots'] ?? []);

        return Arr::undot($v3);
    }

    /**
     * Determine the method
     *
     * @param string|int $value
     * @return strong
     */
    private function getMethod($value)
    {
        return $value == 1 ? 'js' : 'standard';
    }

    /**
     * Convert old html to markdown
     *
     * @param string $value
     * @return string
     */
    private function getMarkdown($value)
    {
        if (class_exists('DomDocument')) {
            $converter = new HtmlConverter();
        } else {
            $converter = new HtmlToMarkdown;
        }

        return preg_replace('/(\[.*\])\(\"(.*)\"\)/', '$1($2)', stripslashes(strip_tags($converter->convert(html_entity_decode(html_entity_decode($value))))));
    }

    /**
     * Map old bad term inheritance to new, better version
     *
     * @param mixed $value
     * @param array $all
     * @return void
     */
    private function getInherited($value, $all)
    {
        if (!$value) {
            return false;
        }

        $data = [
            'content.inherit' => 1,
        ];

        $all = Arr::undot($all);
        foreach ($all['advanced']['inherit_taxonomies'] as $tax) {
            $taxonomy = get_taxonomy($tax);
            $terms = get_terms([
                'taxonomy' => $tax,
            ]);

            foreach ($terms as $term) {
                foreach ($taxonomy->object_type as $type) {
                    $data['content.terms.' . $term->term_id . '.' . $type] = 1;
                }
            }
        }

        return $data;
    }

    /**
     * Determine the new date formate
     *
     * @param string $value
     * @return string
     */
    private function getDateFormat($value)
    {
        switch ($value) {
            case 'ddmmyyyy':
                return 'DD MM YYYY';
            case 'mmddyyyy':
                return 'MM DD YYYY';
        }

        return 'DD MM YYYY';
    }
}
