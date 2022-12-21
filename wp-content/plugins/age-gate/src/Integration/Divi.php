<?php

namespace AgeGate\Integration;

use AgeGate\Common\Settings;
use AgeGate\Common\Integration;

class Divi extends Integration
{
    public function exists()
    {
        return function_exists('et_fb_is_enabled');
    }

    public function init()
    {
        if ($this->exists()) {
            // add_action('init', function () {
            if (isset($_GET['et_fb']) && current_user_can('edit_posts')) {
                Settings::getInstance()->isBuilder = true;
            }

            add_action('age_gate/before_render', [$this, 'pauseDiviCssCache']);

            // });
        }
    }

    public function pauseDiviCssCache()
    {
        $class = 'ET_Core_PageResource';

        // Output Location: head-early, right after theme styles have been enqueued.
        remove_action('wp_enqueue_scripts', [$class, 'head_early_output_cb'], 11);

        // Output Location: head, right BEFORE the theme and wp's custom css.
        remove_action('wp_head', [$class, 'head_output_cb'], 99);

        // Output Location: head-late, right AFTER the theme and wp's custom css.
        remove_action('wp_head', [$class, 'head_late_output_cb'], 103);

        // Output Location: footer
        remove_action('wp_footer', [$class, 'footer_output_cb'], 20);

        // Always delete cached resources for a post upon saving.
        remove_action('save_post', [$class, 'save_post_cb'], 10, 3);

        // Always delete cached resources for theme customizer upon saving.
        remove_action('customize_save_after', [$class, 'customize_save_after_cb']);

        // Add fallback callbacks (lol) to link/script tags
        remove_filter('style_loader_tag', [$class, 'link_and_script_tags_filter_cb'], 999, 2);

        remove_filter('wp_get_custom_css', 'et_epanel_handle_custom_css_output', 999, 2);

        add_action('wp_head', 'wp_custom_css_cb', 101);
    }
}
