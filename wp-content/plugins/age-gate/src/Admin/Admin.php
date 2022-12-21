<?php

namespace AgeGate\Admin;

use WP_REST_Request;
use Asylum\Utility\Notice;
use AgeGate\Common\Settings;
use Asylum\Utility\Language;
use AgeGate\Admin\User\Toolbar;
use AgeGate\Common\Immutable\Constants;
use AgeGate\Admin\Controller\PostController;
use AgeGate\Admin\Controller\ToolsController;
use AgeGate\Admin\Controller\AccessController;
use AgeGate\Admin\Controller\ContentController;
use AgeGate\Admin\Controller\MessageController;
use AgeGate\Admin\Controller\AdvancedController;
use AgeGate\Admin\Controller\AppearanceController;
use AgeGate\Admin\Controller\RestrictionController;
use AgeGate\Admin\Controller\TroubleShootingController;

class Admin
{
    public function __construct()
    {
        // add_action('init', fn() => dd(Language::getInstance()->getLanguages()));
        add_action('plugins_loaded', function () {
            new RestrictionController();
            new MessageController();
            new AppearanceController();
            new AdvancedController();
            new AccessController();
            new ContentController();
            new ToolsController();
            new PostController();
            new Toolbar();
            new TroubleShootingController();
        });

        add_action('admin_notices', [$this, 'notices']);

        $basename = plugin_basename(AGE_GATE_PATH . 'age-gate.php');
        add_filter("plugin_action_links_" . $basename, [$this, 'actionLinks']);
        add_filter('plugin_row_meta', [$this, 'websiteLink'], 10, 2);
    }

    public function notices()
    {

        // $req = wp_remote_get(rest_url('/age-gate/v3/check'), [
        //     'sslverify' => false
        // ]);

        if (!is_php_version_compatible('7.4') && strpos(sanitize_text_field($_GET['page'] ?? ''), 'age-gate') !== false) {
             echo '<div id="message" class="notice notice-error"><p>' . esc_html__('Age Gate requires a minimum PHP version of 7.4 which your system does not have. You may experience some issues.', 'age-gate') . '</p></div>';
        }

        // if (wp_remote_retrieve_response_code($req) !== 200 && current_user_can(Constants::RESTRICTIONS) && strpos(sanitize_text_field($_GET['page'] ?? ''), 'age-gate-advanced') !== false) {
        //     echo '<div id="ag-api-error" class="notice notice-error is-dismissible"><p>' . esc_html__('Age Gate is having trouble contacting the Wordpress REST API. Is something blocking it?', 'age-gate') . '</p></div>';
        // }

        if (isset($_GET['m'])) {
            switch ((int) $_GET['m']) {
                case 1:
                    $status = 'success';
                    $message = esc_html__('Settings saved', 'age-gate');
                    break;
                case 0:
                    $status = 'error';
                    $message = esc_html__('Something went wrong', 'age-gate');
                    break;
            }
            echo sprintf('<div class="notice notice-%s"><p>%s</p></div>', esc_attr($status), esc_html($message));
        }

        if (Settings::getInstance()->devWarning && current_user_can(Constants::TOOLS)) {

            $data = get_plugin_data(AGE_GATE_PATH . 'age-gate.php');

            $dev = preg_split('/\-|\+/', $data['Version']);

            if ($dev[1] ?? false) {
                $pattern = sprintf('/(%s|%s)/', $dev[1], AGE_GATE_VERSION);
                /* translators: %1$s: Sub version  number. %2$s: Full version number. */
                $messageText = sprintf(__('You are using the %1$s version of Age Gate %2$s. This may not be suitable for production websites.', 'age-gate'), $dev[1], AGE_GATE_VERSION);
                echo '<div id="message" class="notice notice-error"><p>' . preg_replace($pattern, '<b>$1</b>', esc_html($messageText)) . '</p></div>';
            }
        }

        if (Settings::getInstance()->devEndpoint && current_user_can(Constants::TOOLS)) {
            echo '<div id="message" class="notice notice-warning"><p>' . esc_html__('The developer endpoint is enabled. You should disable this unless you have an open support topic', 'age-gate') . '</p></div>';
        }

        foreach (Notice::get() ?? [] as $notice) {
            echo '<div id="message" class="notice notice-' . esc_attr($notice['type'] ?? 'notice') . '"><p>' . esc_html($notice['message']) . '</p></div>';
        }

    }

    public function actionLinks($links)
    {
        $settings = sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=age-gate'), esc_html__('Settings', 'age-gate'));
        $donate = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donate%40wordpressagegate%2ecom&lc=GB&item_name=Age%20Gate&item_number=Age%20Gate%20Donation&no_note=0&currency_code=GBP&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest" target="_blank" rel="noopener noreferrer">' . esc_html__('Donate', 'age-gate') . '</a>';

        array_unshift($links, $settings);
        array_push($links, $donate);

        return $links;
    }

    public function websiteLink($meta, $file)
    {
        $basename = plugin_basename(AGE_GATE_PATH) . '/age-gate.php';

        if ($basename === $file) {
            $meta[] = sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
                'https://agegate.io/docs',
                esc_html__('Documentation', 'age-gate')
            );

            $meta[] = sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
                'https://agegate.io/release-notes',
                esc_html__('What&rsquo;s new?', 'age-gate')
            );
        }

        return $meta;
    }
}
