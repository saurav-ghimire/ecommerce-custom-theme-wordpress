<?php

namespace AgeGate\Admin\Settings;

trait Advanced
{
    protected function getAdvancedFields()
    {
        return [
            [
                'title' => __('Caching / Performance', 'age-gate'),
                'model' => ['method'],
                'subtitle' => __('If you have a caching solution, it is best to use a JavaScript triggered version of the age gate as this won\'t be adversely affected by the cache. If you don\'t have caching, the standard method is recommended.', 'age-gate'),
                'fields' => [
                    'method' => [
                        'label' => __('Method', 'age-gate'),
                        'type' => 'select',
                        'options' => [
                            'standard' => __('Standard (PHP)', 'age-gate'),
                            'js' => __('No caching (JavaScript)', 'age-gate'),
                        ],
                        'default' => 'js',
                        'attributes' => [
                            'x-model' => 'method',
                        ]

                    ],
                    'disable_ajax_fallback' => [
                        'label' => __('Disable AJAX fallback', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => false,
                        'subtext' => __('By default, if Age Gate fails to get a response from the REST API it will then attempt an admin_ajax request', 'age-gate'),
                        'condition' => [
                            'x-show' => "method == 'js'"
                        ],
                    ],
                    'munge' => [
                        'label' => __('Munge options', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => false,
                        'subtext' => __('Settings in JavaScript mode are not outputted as a script tag, useful for some performance plugins like Lightspeed', 'age-gate'),
                        'condition' => [
                            'x-show' => "method == 'js'"
                        ],
                    ],
                    'in_header' => [
                        'label' => __('In header', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => false,
                        'subtext' => __('Add Age Gate early in the load process. This may give a visual speed improvement but harm pagespeed scores (blocking)', 'age-gate'),
                        'condition' => [
                            'x-show' => "method == 'js'"
                        ],
                    ],
                    'js_hooks' => [
                        'label' => __('Hooks', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => false,
                        'subtext' => __('Enable certain hooks in JavaScript', 'age-gate'),
                        'condition' => [
                            'x-show' => "method == 'js'"
                        ],
                    ],
                    'loader_img' => [
                        'label' => __('Loading image', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => false,
                        'subtext' => __('Include loader as image tag (default inline SVG)', 'age-gate'),
                        'condition' => [
                            'x-show' => "method == 'js'"
                        ],
                    ],
                    'preload' => [
                        'label' => __('Preload images', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => true,
                        'subtext' => __('Logo and/or background images are preloaded', 'age-gate'),
                    ],
                ],
            ],
            [
                'title' => __('Interactions', 'age-gate'),
                'subtitle' => __('Block certain actions from a user.', 'age-gate'),
                'fields' => [
                    'focus' => [
                        'label' => __('Trap focus', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => false,
                        'subtext' => sprintf(
                            __('Focus trapping can help accessibility, however may conflict with other popups like cookie banners. %s', 'age-gate'),
                            sprintf(
                                '[%s](%s)',
                                __('See documentation', 'age-gate'),
                                '#'
                            )
                        ),
                    ],
                    'dev_tools' => [
                        'label' => __('Disable right click and F12', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => false,
                        'subtext' => __('Removes users ability to open context menu or dev tools with F12. Other routes may stil be available for developers', 'age-gate'),
                    ],
                ],
            ],
            [
                'title' => __('Additional tags', 'age-gate'),
                'fields' => [
                    'rta' => [
                        'label' => __('Enable RTA tag', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => false,
                    ],
                ],
            ],
            [
                'title' => __('Toolbar', 'age-gate'),
                'fields' => [
                    'toolbar' => [
                        'label' => __('Show in front end toolbar', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => true,
                    ],
                ],
            ],
            [
                'title' => __('Custom bots', 'age-gate'),
                'subtitle' => __('You can add the user agent string of any bots that are not automatically picked up. Add 1 per line, can be a part or full user agent string'),
                'fields' => [
                    'user_agents' => [
                        'type' => 'textarea',
                        'label' => __('UA Strings', 'age-gate'),
                        'default' => '',
                        'attributes' => [
                            'class' => 'mono'
                        ]
                    ]
                ],
            ],
            [
                'fields' => [
                    'anonymous' => [
                        'label' => __('Anonymous Age Gate', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => false,
                    ],
                    'cookie_name' => [
                        'label' => __('Cookie name', 'age-gate'),
                        'type' => 'text',
                        'default' => 'age_gate',
                    ],

                ],
            ],
            [
                'title' => __('Styling', 'age-gate'),
                'subtitle' => __('You can add custom CSS for the Age Gate in customizer.', 'age-gate'),
                'fields' => [
                    'css' => [
                        'label' => __('Custom CSS', 'age-gate'),
                        'type' => 'message',
                        'message' => __('Custom CSS can now be found/added in the customiser', 'age-gate'),
                        'suboption' => get_option('age_gate_legacy_css', ''),
                        'default' => false,
                        'attributes' => [
                            'class' => 'ag-css',
                        ],
                        'docs' => [
                            'link' => 'https://agegate.io/docs/v3/styling/css-reference',
                            'label' => 'CSS Reference',
                            'class' => 'button',
                        ]
                    ],
                    'css_type' => [
                        'label' => __('CSS style', 'age-gate'),
                        'type' => 'select',
                        'options' => [
                            'v3' => 'BEM style',
                            'v2' => 'V2 style'
                        ],
                        'default' => 'v3',

                    ],
                ],
            ],

        ];
    }
}
