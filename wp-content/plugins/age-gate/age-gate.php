<?php

/**
 * Plugin Name:       	 Age Gate
 * Plugin URI:           https://agegate.io/
 * Description:       	 A customisable age gate to block content from younger people
 * Version:           	 3.0.11
 * Requires at least:    6.0.0
 * Requires PHP:         7.4
 * Author:            	 Phil Baker
 * Author URI:        	 https://agegate.io/
 * Text Domain:       	 age-gate
 * Domain Path:          /language
 */

if (!defined('WPINC')) {
    die;
}

define('AGE_GATE_PATH', plugin_dir_path(__FILE__));
define('AGE_GATE_URL', plugin_dir_url(__FILE__));
define('AGE_GATE_VERSION', '3.0.11');
define('AGE_GATE_SLUG', 'age-gate');

$autoload = AGE_GATE_PATH . 'vendor/autoload.php';

if (!file_exists($autoload) || !is_readable($autoload)) {
    add_action('admin_notices', function() {
        echo sprintf('<div class="notice notice-error"><p>%s</p></div>', esc_html__('Age Gate could not load its dependencies. Please check your installation.', 'age-gate'));
    });
    return;
}

require_once($autoload);
require_once(AGE_GATE_PATH . 'src/Bootstrap.php');

function age_gate_activate($networkwide)
{
    if (is_multisite() && $networkwide) {
        foreach (get_sites() as $site) {
            switch_to_blog($site->blog_id);
            \AgeGate\Update\Activate::activate();
            restore_current_blog();
        }
    } else {
        \AgeGate\Update\Activate::activate();
    }
}

function age_gate_deactivate()
{
    \AgeGate\Update\Deactivate::deactivate();
}

function age_gate_uninstall()
{
    \AgeGate\Update\Uninstall::uninstall();
}

register_activation_hook(__FILE__, 'age_gate_activate');
register_deactivation_hook(__FILE__, 'age_gate_deactivate');
register_uninstall_hook(__FILE__, 'age_gate_uninstall');
