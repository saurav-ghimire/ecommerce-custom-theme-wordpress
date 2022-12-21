<?php

namespace AgeGate\Admin\Controller;

use Asylum\Utility\Arr;
use AgeGate\Common\Immutable\Constants as Immutable;
use AgeGate\Admin\Settings\Restriction;
use AgeGate\Common\Admin\AbstractController;

class RestrictionController extends AbstractController
{
    use Restriction;

    public const PERMISSION = Immutable::RESTRICTIONS;
    protected const OPTION = Immutable::OPTION_RESTRICTION;

    protected const DATA_FILTERS = [
        'redirect' => 'full_url',
    ];


    protected function required(): bool
    {
        return current_user_can(self::PERMISSION);
    }

    public function register(): void
    {
        add_action('admin_print_footer_scripts', function () {
            if (isset($_REQUEST['page']) && strpos($_REQUEST['page'], 'age-gate') !== false) {
                if (! class_exists('_WP_Editors') && (! defined('DOING_AJAX') or ! DOING_AJAX)) {
                    require_once ABSPATH.WPINC.'/class-wp-editor.php';
                    wp_print_styles('editor-buttons');
                    \_WP_Editors::wp_link_dialog();
                }
            }
        }, 1);

        $this->menu(__('Restrictions', 'age-gate'), self::PERMISSION);
    }

    protected function data(): array
    {
        return get_option(static::OPTION, []);
    }

    protected function fields(): array
    {
        return $this->getRestrictionFields();
    }

    public function enqueue(): void
    {
        wp_enqueue_script('wplink');
    }

    protected function rules() : array
    {
        return [
            'default_age' => 'numeric',
            'type' => 'alpha',
            'multi_age' => 'boolean',
            'input_type' => 'alpha',
            'date_format' => 'alpha_space',
            'button_order' => 'alpha_dash',
            'stepped' => 'boolean',
            'remember' => 'boolean',
            'remember_length.remember_length' => 'numeric',
            'remember_length.remember_time' => 'alpha',
            'remember_auto_check' => 'boolean',
            'ignore_logged' => 'boolean',
            'rechallenge' => 'boolean',
            'redirect' => 'valid_url',

        ];
    }
}
