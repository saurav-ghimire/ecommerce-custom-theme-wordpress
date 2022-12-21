<?php

namespace AgeGate\Admin\Settings;

trait Appearance
{
    protected function getAppearanceFields()
    {
        return [
            [
                'model' => ['switch_title'],
                'fields' => [
                    'logo' => [
                        'label' => __('Logo', 'age-gate'),
                        'type' => 'image',
                        'default' => '',
                    ],
                    'background_color' => [
                        'label' => __('Background colour', 'age-gate'),
                        'type' => 'color',
                        'default' => '',
                    ],
                    'background_opacity' => [
                        'label' => __('Background colour opacity', 'age-gate'),
                        'type' => 'range',
                        'default' => '1',
                        'attributes' => [
                            'min' => '0',
                            'max' => '1',
                            'step' => '0.1',
                        ]
                    ],
                    'blur' => [
                        'label' => __('Blur other elements by', 'age-gate'),
                        'type' => 'number',
                        'default' => '5',
                        'subtext' => 'px',
                        'attributes' => [
                            'class' => 'small-text',
                        ],
                    ],
                    'background_image' => [
                        'label' => __('Background image', 'age-gate'),
                        'type' => 'image',
                        'default' => '',
                    ],
                    'background_position' => [
                        'label' => __('Background position', 'age-gate'),
                        'type' => 'group',
                        'fields' => [
                            'x' => [
                                'label' => __('X axis', 'age-gate'),
                                'type' => 'select',
                                'options' => [
                                    'left' => __('Left', 'age-gate'),
                                    'right' => __('Right', 'age-gate'),
                                    'center' => __('Center', 'age-gate'),
                                ],
                                'default' => 'center',
                            ],
                            'y' => [
                                'label' => __('Y axis', 'age-gate'),
                                'type' => 'select',
                                'options' => [
                                    'top' => __('Top', 'age-gate'),
                                    'bottom' => __('Bottom', 'age-gate'),
                                    'center' => __('Center', 'age-gate'),
                                ],
                                'default' => 'center',
                            ],
                        ],
                        'wrapper' => [
                            'class' => 'ag-field--flex',
                        ]
                    ],
                    'background_image_opacity' => [
                        'label' => __('Background image opacity', 'age-gate'),
                        'type' => 'range',
                        'default' => '1',
                        'attributes' => [
                            'min' => '0',
                            'max' => '1',
                            'step' => '0.1',
                        ]
                    ],
                    'foreground_color' => [
                        'label' => __('Foreground colour', 'age-gate'),
                        'type' => 'color',
                        'default' => '#ffffff',
                    ],
                    'foreground_opacity' => [
                        'label' => __('Foreground colour opacity', 'age-gate'),
                        'type' => 'range',
                        'default' => '1',
                        'attributes' => [
                            'min' => '0',
                            'max' => '1',
                            'step' => '0.1',
                        ]
                    ],
                    'text_color' => [
                        'label' => __('Text colour', 'age-gate'),
                        'type' => 'color',
                        'default' => '#000000',
                    ],
                    'enqueue_css' => [
                        'label' => __('Layout', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => true,
                        'subtext' => __('Use plugin style on the front end', 'age-gate')
                    ],
                    'exit_transition' => [
                        'label' => __('Transition', 'age-gate'),
                        'type' => 'select',
                        'default' => '',
                        'subtext' => __('In JavaScript transition Age Gate out', 'age-gate'),
                        'options' => [
                            '' => 'None',
                            'fade' => __('Fade', 'age-gate'),
                            'slide-up' => __('Slide up', 'age-gate'),
                            'slide-down' => __('Slide down', 'age-gate'),
                            'slide-left' => __('Slide left', 'age-gate'),
                            'slide-right' => __('Slide right', 'age-gate'),
                        ]
                    ],
                    'viewport' => [
                        'label' => __('Viewport meta tag', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => false,
                        'subtext' => __('Add viewport meta to Age Gate page', 'age-gate') . "\r\n" . '(width=device-width, minimum-scale=1, maximum-scale=1)',

                    ],
                    'input_auto_tab' => [
                        'label' => __('Auto tab inputs', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => false,
                    ],
                    'switch_title' => [
                        'label' => __('Change the page title', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => true,
                        'attributes' => [
                            'x-on:change' => 'switch_title = ! switch_title'
                        ],
                    ],
                    'custom_title' => [
                        'label' => __('Custom page title', 'age-gate'),
                        'type' => 'text',
                        'default' => __('Age Verification', 'age-gate'),
                        'translate' => true,
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'condition' => [
                            'x-show' => 'switch_title != \'\'',
                        ],
                    ],
                    'simplebar' => [
                        'label' => __('Enable simplebar', 'age-gate'),
                        'type' => 'checkbox',
                        'default' => false,
                        'subtext' => __('Enables a stylable scrollbar if Age Gate overflows', 'age-gate'),
                        'translate' => false,
                    ],
                ]
            ],
        ];
    }
}
