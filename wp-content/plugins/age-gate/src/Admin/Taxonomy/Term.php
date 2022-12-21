<?php

namespace AgeGate\Admin\Taxonomy;

use AgeGate\Common\Content;
use AgeGate\Common\Settings;
use AgeGate\Common\Immutable\Constants;
use Jawira\CaseConverter\Convert;

class Term
{
    private $settings;
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
        $this->settings = Settings::getInstance();

        add_action('admin_init', [$this, 'registerFields']);
    }

    public function registerFields()
    {
        $args = [
            'public' => true,
            'show_ui' => true,
        ];

        foreach (get_taxonomies($args) as $taxonomy) {
            add_action($taxonomy . '_add_form_fields', [$this, 'index']);
            add_action($taxonomy . '_edit_form_fields', [$this, 'index']);

            add_action('create_' . $taxonomy, [$this, 'store'], 10, 2);
            add_action('edited_' . $taxonomy, [$this, 'store'], 10, 2);
        }
    }

    public function index()
    {
        if (current_user_can(Constants::SET_CONTENT) || current_user_can(Constants::SET_CUSTOM_AGE)) {
            global $typenow;


            $settings = Settings::getInstance();
            $disable = $this->settings->disable[$typenow] ?? false;

            if ($disable) {
                return;
            }

            $id = ($_GET['tag_ID'] ?? null) ? (int) $_GET['tag_ID'] : null;

            echo $this->view->addData([
                'content' => new Content($id, 'term'),
                'action' => strpos(current_action(), 'edit') !== false ? 'edit' : 'add',
                'settings' => $settings,
                'setRestriction' => current_user_can(Constants::SET_CONTENT),
                'setAge' => current_user_can(Constants::SET_CUSTOM_AGE),
                'contentOption' => $this->settings->type === 'selected' ? Constants::META_RESTRICT : Constants::META_BYPASS,
            ])->render('term/meta-options');
        }
    }

    public function store($id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // check nonce
        if (!wp_verify_nonce($_POST['_agn'] ?? '', 'age_gate_post_edit')) {
            return;
        }

        $content = new Content($id, 'term');

        // mutli ages?
        if ($this->settings->multiAge && current_user_can(Constants::SET_CUSTOM_AGE)) {
            $default = $this->settings->{$content->getLanguage()}['defaultAge'] ?? $this->settings->defaultAge;

            if ($_POST['ag_settings']['age'] ?? false) {
                $age = (int) $_POST['ag_settings']['age'];

                if ($age === $default) {
                    // remove the meta as we don't need it
                    delete_term_meta($id, Constants::META_AGE);
                } else {
                    // add new meta key
                    update_term_meta($id, Constants::META_AGE, $age);
                }
            }
        }

        // bypass ?
        if ($this->settings->type === 'all' && current_user_can(Constants::SET_CONTENT)) {
            if ($_POST['ag_settings']['bypass'] ?? false) {
                // add new meta key
                update_term_meta($id, Constants::META_BYPASS, 1);
            } else {
                // remove the meta as we don't need it
                delete_term_meta($id, Constants::META_BYPASS);
            }
        }

        // restrict
        if ($this->settings->type === 'selected' && current_user_can(Constants::SET_CONTENT)) {
            if ($_POST['ag_settings']['restrict'] ?? false) {
                // add new meta key
                update_term_meta($id, Constants::META_RESTRICT, 1);
            } else {
                // remove the meta as we don't need it
                delete_term_meta($id, Constants::META_RESTRICT);
            }
        }
    }
}
