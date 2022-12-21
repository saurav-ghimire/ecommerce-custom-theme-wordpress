<?php

namespace AgeGate\Common\Admin;

use Asylum\Utility\Arr;
use League\Plates\Engine;
use Asylum\Utility\Language;
use Asylum\Utility\Notice;
use AgeGate\Common\Admin\Helper;
use Asylum\Validation\Validator;
use League\CommonMark\CommonMarkConverter;
use Parsedown;

abstract class AbstractController
{
    use Helper;

    protected const OPTION = '';
    protected const PERMISSION = '';
    protected const INIT = true;
    protected const DATA_FILTERS = [];

    protected static $tabs = [];

    public function __construct()
    {
        if ($this->required()) {
            if (static::INIT) {
                $this->init();
            }

            $this->register();
        }
    }

    abstract public function register(): void;

    abstract protected function required(): bool;

    abstract protected function data(): array;

    abstract protected function fields(): array;

    private function init()
    {
        $this->view = new Engine(AGE_GATE_PATH . '/src/Resources/views/admin');

        $this->view->addData([
            'action' => $this->getName(),
            'permission' => static::PERMISSION,
        ]);

        $this->view->registerFunction('form_key', function ($string) {
            return Arr::dotToKey($string);
        });

        $this->view->registerFunction('form_id', function ($string) {
            $dot = Arr::dotToKey($string);
            return sanitize_title(str_replace(['.', '['], '_', $dot));
        });

        $markdown = new Parsedown();

        $this->view->registerFunction('md', function ($string) use ($markdown) {
            return $markdown->line($string);
        });


        foreach (apply_filters('age_gate/admin/views', []) ?: [] as $key => $dir) {
            $this->view->addFolder($key, $dir);
        }

        add_action('admin_post_age_gate_' . $this->getName(), fn () => $this->store($_POST));
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueDefault']);

        if (!has_action('in_admin_header', [self::class, 'adminHeader'])) {
            add_action('in_admin_header', [self::class, 'adminHeader']);
        }
    }

    protected function store()
    {
        $validator = new Validator();

        // TODO:: Move validators somewhere better
        Validator::add_filter("keys_to_value", function ($value, array $params = []) {
            return array_keys($value);
        });

        Validator::add_filter("full_url", function ($value, array $params = []) {

            if (empty(trim($value))) {
                return '';
            }

            $parsed = parse_url($value);

            if (empty($parsed['host'])) {
                return esc_url_raw(site_url($value));
            }

            return $value;
        });

        Validator::add_validator('ag_message', function($field, $input, $params = [], $value = null) {
            return preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝŸÑàáâãäåçèéêëìíîïðòóôõöùúûüýÿñ\s0-9{}%-.,?!])+$/i', $value) > 0;
        }, "{field} may only contain letters, numbers, spaces hyphens and placeholders such as {age} or %s");

        Validator::add_validator('ag_message_md', function($field, $input, $params = [], $value = null) {
            return preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝŸÑàáâãäåçèéêëìíîïðòóôõöùúûüýÿñ\s0-9{}%-.,?!\[\]\(\)\/\:])+$/i', $value) > 0;
        }, "{field} may only contain letters, numbers, spaces hyphens and placeholders such as {age} or %s");

        Validator::add_validator('ag_hex', function($field, $input, $params = [], $value = null) {
            return preg_match('/#([a-f0-9]{3}){1,2}\b/i', $value) > 0;
        }, "{field} must be a hexidecimal colour");

        Validator::add_validator('ag_alpha_underscore', function($field, $input, $params = [], $value = null) {
            return preg_match('/([a-z_])+$/i', $value) > 0;
        }, "{field} may only contain letters and underscores");


        // check nonce & permission
        if (!wp_verify_nonce($_POST['_wpnonce'], $_POST['action']) || !current_user_can(static::PERMISSION)) {
            wp_die('Disallowed action');
        }

        $method = sprintf('get%sFields', ucfirst($this->getName()));
        $dot = Arr::dot($this->$method());

        $default = [];

        $values = Arr::where($dot, function ($value, $key) {
            return substr($key, -8) === '.default';
        });

        foreach ($values as $key => $value) {
            $exp = '/(?:[0-9]).fields.([a-z_.]+).default/';
            $k = preg_replace($exp, '$1', $key);

            $k = str_replace('.fields', '', $k);
            $default[trim($k, '.')] = null;
        }


        $merge = Arr::undot(array_merge($default, Arr::dot($_POST['ag_settings'] ?? [])));

        $language = Language::getInstance();
        $filters = static::DATA_FILTERS;

        foreach ($language->getLanguages() as $code => $lang) {
            foreach (static::DATA_FILTERS as $key => $filter) {
                $filters[$code . '.' . $key] = $filter;
            }
        }

        $merge = Arr::undot($validator->filter(Arr::dot($merge), $filters));

        if ($rules = $this->rules()) {

            foreach ($language->getLanguages() as $code => $language) {
                foreach ($rules as $key => $rule) {
                    $rules[$code . '.' . $key] = $rule;
                }
            }

            $validator->validation_rules($rules);
            $validator->run($merge);

            if ($validator->errors()) {
                $previous = Arr::dot(get_option(static::OPTION, []));
                $merge = Arr::dot($merge);

                foreach ($validator->get_errors_array() as $field => $error) {
                    // reset the invalid field to previous value
                    $merge[$field] = $previous[$field] ?? '';
                    Notice::add($error);
                }

                $merge = Arr::undot($merge);
            }
        }

        // dd($merge);
        update_option(static::OPTION, $validator->sanitize($merge));

        // allow controllers to do more than the base save
        $this->optionStored($merge);

        // get return URL
        $this->redirect($_POST['_wp_http_referer']);
    }

    public function parent()
    {
        // if (!$this->exists('age-gate')) {
        add_menu_page(
            __('Age Gate', 'age-gate'),
            __('Age Gate', 'age-gate'),
            static::PERMISSION,
            'age-gate',
            '__return_false',
            'dashicons-lock',
            59
        );
        // }
    }

    private function exists($name)
    {
        global $menu;
        $menuItems = collect($menu)->flatten()->toArray();
        return in_array($name, $menuItems);
    }

    protected function getSlug()
    {
        if (!$this->exists('age-gate')) {
            $this->parent();
            return 'age-gate';
        }

        return 'age-gate-' . $this->getName();
    }

    public function render()
    {
        $language = Language::getInstance();

        $this->view->addData([
            'data' => Arr::dot($this->data()),
            'fields' => $this->fields(),
            'field_prefix' => 'ag_settings',
            'languages' => [
                'current' => $language->getLanguages('current'),
                'available' => $language->getLanguages(),
                'default' => $language->getLanguages('default'),
            ]
        ]);
        $tmpl = $this->template ?? 'default'; //$this->getName();

        echo $this->view->render($tmpl);
    }

    protected function menu($name, $permission, $menu = '')
    {
        add_action('admin_menu', function () use ($name, $permission, $menu) {
            $slug = $this->getSlug();

            self::$tabs[$slug] = $menu ?: $name;

            add_submenu_page(
                'age-gate',
                $name,
                $menu ?: $name,
                $permission,
                $slug,
                [$this, 'render']
            );
        });
    }

    protected function getName()
    {
        return strtolower(str_replace('Controller', '', substr(get_called_class(), strrpos(get_called_class(), '\\') + 1)));
    }

    public function enqueueDefault()
    {
        global $pagenow;
        if ($pagenow === 'admin.php' && substr($_GET['page'] ?? '', 0, 8) === 'age-gate') {
            // No longer required
            // wp_enqueue_code_editor([
            //     'type' => 'text/css',
            // ]);

            wp_enqueue_script('age-gate-admin', AGE_GATE_URL . 'dist/admin.js', [], AGE_GATE_VERSION, true);
            wp_localize_script('age-gate-admin', 'ag_admin', [
                'nonce' => wp_create_nonce('wp_rest'),
                'rest_url' => rest_url('/age-gate/v3/media'),
                'labels' => [
                    'search' => __('Search', 'age-gate'),
                    'more' => __('More', 'age-gate'),
                    'loading' => __('Loading', 'age-gate'),
                ]
            ]);
        }
    }

    public static function adminHeader()
    {
        if (($_GET['page'] ?? false) && strpos($_GET['page'], 'age-gate') !== false) {
            $view = new Engine(AGE_GATE_PATH . '/src/Resources/views/admin');
            $view->addData([
                'tabs' => self::$tabs,
            ]);
            echo $view->render('partials/global/admin-toolbar');
        }
    }

    public function enqueue(): void
    {
    }

    protected function optionStored($data): void
    {
    }

    protected function rules() : array
    {
        return [];
    }
}
