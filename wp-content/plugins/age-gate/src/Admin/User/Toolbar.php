<?php

namespace AgeGate\Admin\User;

use AgeGate\Utility\Cookie;
use AgeGate\Common\Settings;
use AgeGate\Common\Admin\AbstractController;

/**
 * Determine placement of toolbar link
 *
 * @package     AgeGate
 * @copyright   2022 Phil Baker
 */
class Toolbar extends AbstractController
{
    protected const INIT = false;

    public function register(): void
    {
        add_action('admin_bar_menu', [$this, 'toolbarLink'], 1000);
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
    }

    public function required(): bool
    {
        $settings = Settings::getInstance();

        return !is_admin() && $settings->toolbar;
    }

    public function data(): array
    {
        return [];
    }

    public function fields(): array
    {
        return [];
    }

    public function enqueue(): void
    {
        if (is_admin_bar_showing()) {
            wp_enqueue_script('age-gate-toolbar', AGE_GATE_URL . 'dist/toolbar.js', [], AGE_GATE_VERSION, true);
            wp_enqueue_style('age-gate-toolbar', AGE_GATE_URL . 'dist/toolbar.css', [], AGE_GATE_VERSION);
        }
    }

    public function toolbarLink($adminBar)
    {
        if (!is_admin_bar_showing()) {
            return;
        }


        $settings = Settings::getInstance();
        echo sprintf('<script>var ag_cookie_domain = "%s"; var ag_cookie_name = "%s"</script>', Cookie::getDomain(), $settings->getCookieName());

        $parent = [
            'id' => 'age-gate',
            'title' => '<span class="screen-reader-text">' . __('Age Gate', 'age-gate') . '</span>',
            'href' => esc_url(
                add_query_arg(
                    ['page' => 'age-gate'],
                    admin_url('admin.php')
                )
            )
        ];

        $adminBar->add_node($parent);


        $child = [
            'id' => 'age-gate-toggle',
            'title' => 'Toggle',
            'parent' => 'age-gate',
            'href' => esc_url(
                add_query_arg(
                    [
                        'page' => 'age-gate',
                        'ag_switch' => '1',
                        '_wpnonce' => wp_create_nonce('age-gate-toggle')
                    ],
                    admin_url('admin.php')
                )
            ),
            'meta' => [
                'title' => $settings->cookieDomain
            ]

        ];

        $adminBar->add_node($child);

    }
}
