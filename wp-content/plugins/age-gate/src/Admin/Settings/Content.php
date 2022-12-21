<?php

namespace AgeGate\Admin\Settings;

use AgeGate\Common\Settings;

trait Content
{
    protected function getContentFields()
    {
        $postTypes = get_post_types(['public' => true], 'objects');

        return [
            [
                'title' => __('Post Types', 'age-gate'),
                'subtitle' => __('Do not show Age Gate publish actions for the following post types', 'age-gate'),
                'fields' => collect($postTypes)->mapWithKeys(function ($postType) {
                    if ($postType->name === 'attachment') {
                        return [];
                    }
                    return [
                        'disable.' . $postType->name => [
                            'default' => false,
                            'type' => 'checkbox',
                            'attributes' => [

                            ],
                            'label' => $postType->label,
                        ]
                    ];
                })->toArray(),
            ],
            [
                'title' => __('Taxonomy Inheritance', 'age-gate'),
                'model' => ['inherit'],
                'subtitle' => __('Content can inherit restrictions from its taxonomy. Note the most restrictive will be selected. See docs for more information', 'age-gate'),
                'fields' => [
                    'inherit' => [
                        'label' => __('Inherit taxonomies', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => false,
                        'attributes' => [
                            'x-on:change' => 'inherit = ! inherit'
                        ]
                    ],
                ],
            ],
            [
                'title' => __('Taxonomies', 'age-gate'),
                'fields' => $this->getTaxonomyFields(),
                'condition' => [
                    'x-show' => 'inherit != \'\'',
                ]
            ],
            [
                'title' => __('Errors', 'age-gate'),
                'subtitle' => __('How Age Gate should handle certain error pages', 'age-gate'),
                'fields' => [
                    'error_404' => [
                        'label' => __('404 error'),
                        'subtext' => 'Show Age Gate on 404 error',
                        'type' => 'checkbox',
                        'default' => false,
                    ],
                ],
            ],
            [
                'title' => __('Archives', 'age-gate'),
                'subtitle' => __('How Age Gate should handle certain archive pages', 'age-gate'),
                'fields' => $this->getArchiveFields($postTypes),
            ],
            [
                'title' => __('Author archives', 'age-gate'),
                'subtitle' => __('How Age Gate should handle certain author pages', 'age-gate'),
                'fields' => $this->getAuthorFields($postTypes),
            ],
        ];
    }

    private function getTaxonomyFields(): array
    {
        $terms = collect(get_taxonomies(['public' => true, 'show_ui' => true], 'objects'))->mapWithKeys(function ($taxonomy, $slug) {
            return [
                $slug => [
                    'type' => 'group',
                    'label' => $taxonomy->label,
                    'fields' => $this->getTermFields(get_terms(['taxonomy' => $taxonomy->name]), $taxonomy),
                    'parent' => 'terms',
                ]
            ];
        })->toArray();

        return $terms;
    }

    private function getTermFields($terms, $taxonomy): array
    {
        $fields = [];

        foreach ($terms as $term) {
            foreach ($taxonomy->object_type ?? [] as $postType) {
                $fields[$term->term_id . '.' . $postType] = [
                    'type' => 'checkbox',
                    'label' => 'Post',
                    'subtext' => sprintf('%s (%s)', esc_html($term->name), esc_html($postType)),
                ];
            }
        }

        return $fields;
    }

    private function getAuthorFields(): array
    {
        $roles = [];
        $fields = [];
        $archives = [];

        foreach (wp_roles()->roles as $role_name => $role_obj) {
            if (! empty($role_obj['capabilities']['edit_posts'])) {
                $roles[] = $role_name;
            }
        }

        $users = get_users(
            [
                'role__in' => $roles
            ]
        );

        foreach ($users as $user) {
            $archives['user.' . $user->ID] = $user->display_name;
        }


        $settings = Settings::getInstance();

        // TODO:: Apply paermissions?
        foreach ($archives as $archive => $title) {
            $fields[$archive] = [
                'type' => 'group',
                'label' => $title,
                'fields' => [
                    'bypass' => [
                        'type' => $settings->type === 'all' ? 'checkbox' : 'hidden',
                        'label' => __('Bypass', 'age-gate'),
                        'subtext' => __('Bypass', 'age-gate'),
                    ],
                    'restrict' => [
                        'type' => $settings->type !== 'all' ? 'checkbox' : 'hidden',
                        'label' => __('Restrict', 'age-gate'),
                        'subtext' => __('Restrict', 'age-gate'),
                    ],
                    'age' => [
                        'type' => $settings->multiAge ? 'number' : 'hidden',
                        'label' => __('Age', 'age-gate'),
                        'subtext' => __('Age', 'age-gate'),
                        'attributes' => [
                            'class' => 'small-text'
                        ]
                    ]
                ],
            ];
        }


        return $fields;
    }

    private function getArchiveFields($postTypes): array
    {
        $fields = [];


        $archives = [
            'home' => __('Posts archive', 'age-gate'),
            'year' =>  __('Year archives', 'age-gate'),
            'month' => __('Month archives', 'age-gate'),
            'day' => __('Day archives', 'age-gate'),
        ];


        if (get_option('page_for_posts')) {
            unset($archives['home']);
        }

        foreach ($postTypes as $postType) {
            if ($postType->has_archive !== false) {
                if ($postType->name === 'product' && get_option('woocommerce_shop_page_id')) {
                    continue;
                }
                $archives[$postType->name] = $postType->label;
            }
        }

        $settings = Settings::getInstance();

        // TODO:: Apply paermissions?
        foreach ($archives as $archive => $title) {
            $fields['archives.' . $archive] = [
                'type' => 'group',
                'label' => $title,
                'fields' => [
                    'bypass' => [
                        'type' => $settings->type === 'all' ? 'checkbox' : 'hidden',
                        'label' => __('Bypass', 'age-gate'),
                        'subtext' => __('Bypass', 'age-gate'),
                    ],
                    'restrict' => [
                        'type' => $settings->type !== 'all' ? 'checkbox' : 'hidden',
                        'label' => __('Restrict', 'age-gate'),
                        'subtext' => __('Restrict', 'age-gate'),
                    ],
                    'age' => [
                        'type' => $settings->multiAge ? 'number' : 'hidden',
                        'label' => __('Age', 'age-gate'),
                        'subtext' => __('Age', 'age-gate'),
                        'attributes' => [
                            'class' => 'small-text'
                        ]
                    ]
                ],
            ];
        }

        return $fields;
    }
}
