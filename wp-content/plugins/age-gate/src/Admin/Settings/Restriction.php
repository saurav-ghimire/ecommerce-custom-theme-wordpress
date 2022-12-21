<?php

namespace AgeGate\Admin\Settings;

trait Restriction
{
    protected function getRestrictionFields()
    {
        return [
            [
                // 'title' => 'gfddf',
                'subtitle' => __('Below are the core settings to Age Gate your content.', 'age-gate'),
                'model' => ['input_type', 'remember'],
                'fields' => [
                    'default_age' => [
                        'default' => 18,
                        'type' => 'number',
                        'subtext' => '',
                        'attributes' => [
                            'class' => 'small-text',
                            'required' => 'required',
                            'min' => 1
                        ],
                        'label' => __('Default age', 'age-gate'),
                        'translate' => true,
                    ],
                    'type' => [
                        'default' => 'all',
                        'type' => 'select',
                        'options' => [
                            'all' => __('All content', 'age-gate'),
                            'selected' => __('Selected content'),
                        ],
                        'arrtibutes' => [],
                        'label' => __('Restrict', 'age-gate'),
                    ],
                    'multi_age' => [
                        'default' => false,
                        'type' => 'checkbox',
                        'attributes' => [

                        ],
                        'label' => __('Varied ages', 'age-gate'),
                    ],
                    'input_type' => [
                        'default' => 'inputs',
                        'type' => 'select',
                        'options' => [
                            'inputs' => __('Input fields', 'age-gate'),
                            'selects' => __('Dropdown fields', 'age-gate'),
                            'buttons' => __('Yes/No buttons', 'age-gate'),
                        ],
                        'label' => __('Validate age using', 'age-gate'),
                        'attributes' => [
                            'x-model' => 'input_type',
                        ]
                    ],
                    'date_format' => [
                        'type' => 'select',
                        'options' => [
                            'DD MM YYYY' => 'DD MM YYYY',
                            'MM DD YYYY' => 'MM DD YYYY',
                            'YYYY MM DD' => 'YYYY MM DD',
                        ],
                        'default' => 'DD MM YYYY',
                        'label' => __('Date format', 'age-gate'),
                        'condition' => [
                            'x-show' => "input_type != 'buttons'"
                        ],
                        'translate' => true,
                    ],
                    'button_order' => [
                        'default' => 'no-yes',
                        'type' => 'select',
                        'options' => [
                            'no-yes' => __('No then Yes', 'age-gate'),
                            'yes-no' => __('Yes then No', 'age-gate'),
                        ],
                        'label' => __('Button order', 'age-gate'),
                        'condition' => [
                            'x-show' => "input_type == 'buttons'"
                        ],
                        'translate' => true,
                    ],
                    'year_order' => [
                        'type' => 'select',
                        'default' => 'low-high',
                        'label' => __('Year order', 'age-gate'),
                        'condition' => [
                            'x-show' => "input_type == 'selects'"
                        ],
                        'options' => [
                            'low-high' => __('Low to high', 'age-gate'),
                            'high-low' => __('High to low', 'age-gate'),
                        ],
                    ],
                    'stepped' => [
                        'type' => 'checkbox',
                        'default' => false,
                        'label' => __('Stepped Inputs', 'age-gate'),
                        'subtext' => __('Not available on all devices', 'age-gate'),
                        'sublabel' => __('BETA'),
                        'docs' => [
                            'link' => 'https://agegate.io/docs/v3/cms-settings/restrictions/stepped-inputs-beta',
                            'label' => __('Documentation', 'age-gate'),
                            'class' => 'button-text',
                        ],
                        'condition' => [
                            'x-show' => "input_type == 'inputs'"
                        ],
                    ],
                    'remember' => [
                        'default' => false,
                        'type' => 'checkbox',
                        'attributes' => [
                            'x-on:change' => 'remember = ! remember'
                        ],
                        'label' => __('Remember', 'age-gate'),
                    ],
                    'remember_length' => [
                        'type' => 'group',
                        'label' => __('Remember length', 'age-gate'),
                        'wrapper' => [
                            'class' => 'ag-field--flex',
                        ],
                        'condition' => [
                            'x-show' => 'remember != \'\'',
                        ],
                        'fields' => [
                            'remember_length' => [
                                'type' => 'number',
                                'default' => 365,
                                'attributes' => [
                                    'class' => 'small-text',
                                ],
                                'label' => __('Remember length', 'age-gate'),
                            ],
                            'remember_time' => [
                                'type' => 'select',
                                'default' => 'days',
                                'options' => [
                                    'days' => __('Days', 'age-gate'),
                                    'hours' => __('Hours', 'age-gate'),
                                    'minutes' => __('Minutes', 'age-gate'),
                                ],
                                'attributes' => [],
                                'label' => __('Remember time', 'age-gate'),
                            ],
                        ]
                    ],
                    'remember_auto_check' => [
                        'default' => false,
                        'type' => 'checkbox',
                        'attributes' => [],
                        'label' => __('Auto check remember me', 'age-gate'),
                        'condition' => [
                            'x-show' => 'remember != \'\'',
                        ],
                    ],
                    'ignore_logged' => [
                        'default' => false,
                        'type' => 'checkbox',
                        'attributes' => [],
                        'label' => __('Ignore logged in', 'age-gate'),
                    ],
                    'rechallenge' => [
                        'default' => true,
                        'type' => 'checkbox',
                        'attributes' => [],
                        'label' => __('Rechallenge', 'age-gate'),
                    ],
                    'redirect' => [
                        'type' => 'link',
                        'default' => null,
                        'attributes' => [],
                        'label' => __('Redirect failures ', 'age-gate'),
                        'translate' => true,
                    ],
                ]
            ],
        ];
    }
}
