<?php

namespace AgeGate\Admin\Post;

use AgeGate\Common\Content;
use AgeGate\Common\Settings;
use AgeGate\Common\Immutable\Constants;
use Jawira\CaseConverter\Convert;
use AgeGate\Admin\Post\PostTrait as Post;

class ListTable
{
    use Post;

    private $settings;
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
        $this->settings = Settings::getInstance();

        add_filter('manage_posts_columns', [$this, 'column'], 10000);
        add_filter('manage_pages_columns', [$this, 'column'], 10000);

        add_action('manage_posts_custom_column', [$this, 'data'], 10, 2);
        add_action('manage_pages_custom_column', [$this, 'data'], 10, 2);

        if (current_user_can(Constants::SET_CONTENT)) {
            add_action('init', [$this, 'actions']);
        }
    }

    /**
     * Register column
     *
     * @param array $columns
     * @return array
     */
    public function column($columns)
    {
        global $typenow;

        $disable = array_key_exists($typenow, $this->settings->disable ?: []);


        if (!$disable) {
            $columns['age_gate'] = '<span data-ag-tooltip="'.__('Age Gate').'"><i class="wp-menu-image dashicons-before dashicons-lock"></i></span> <span class="screen-reader-text">Age Gate</span>';
        }

        return $columns;
    }

    /**
     * Output custom column
     *
     * @param string $column
     * @param int $postId
     * @return void
     */
    public function data($column, $postId)
    {
        if ($column !== 'age_gate') {
            return;
        }


        echo $this->view->addData([
            'content' => new Content($postId),
        ])->render('post/list-column');
    }

    /**
     * Register bulk actions on post types
     *
     * @return void
     */
    public function actions()
    {

        $postTypes = get_post_types(
            [
                'public' => true
            ]
        );


        foreach ($postTypes as $postType) {
            $settings = Settings::getInstance();
            $disable = array_key_exists($postType, $settings->disable ?: []);


            if (!$disable) {
                add_filter('bulk_actions-edit-' . $postType, function ($actions) {
                    $actions['age_gate_restrict'] = esc_html__('Age Gate: Restrict', 'age-gate');
                    $actions['age_gate_unrestrict'] = esc_html__('Age Gate: Unrestrict', 'age-gate');


                    return $actions;
                });

                add_filter('handle_bulk_actions-edit-' . $postType, [$this, 'actionHandler'], 10, 3);
            }
        }
    }

    /**
     * Handle bulk assigning
     *
     * @param string $redirect
     * @param string $action
     * @param array $posts
     * @return string
     */
    public function actionHandler($redirect, $action, $posts)
    {
        if (!in_array($action, ['age_gate_restrict', 'age_gate_unrestrict'] )) {
            return $redirect;
        }

        if (!current_user_can(Constants::SET_CONTENT)) {
            return $redirect;
        }

        switch ($action) {
            case 'age_gate_restrict':
                $this->bulk('restrict', $posts);
                break;
            case 'age_gate_unrestrict':
                $this->bulk('bypass', $posts);
                break;
        }

        return $redirect;
    }

    /**
     * Apply the bulk update
     *
     * @param string $action
     * @param array $posts
     * @return void
     */
    private function bulk(string $action, array $posts) : void
    {
        $settings = Settings::getInstance();

        switch ($action) {
            case 'restrict':

                foreach ($posts as $postId) {
                    if ($settings->type === 'selected') {
                        update_post_meta($postId, Constants::META_RESTRICT, 1);
                    } else {
                        delete_post_meta($postId, Constants::META_BYPASS);
                    }
                }

                break;
            case 'bypass':
                foreach ($posts as $postId) {
                    if ($settings->type === 'selected') {
                        delete_post_meta($postId, Constants::META_RESTRICT);
                    } else {
                        update_post_meta($postId, Constants::META_BYPASS, 1);
                    }
                }

                break;
        }
    }
}
