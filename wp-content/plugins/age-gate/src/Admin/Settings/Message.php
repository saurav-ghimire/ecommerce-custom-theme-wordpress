<?php

namespace AgeGate\Admin\Settings;

trait Message
{
    protected function getMessageFields()
    {
        return [
            [
                'title' => '',
                'subtitle' => __('Customise the various messages Age Gate uses', 'age-gate'),
                'fields' => [
                    'headline' => [
                        'label' => __('Headline', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text'
                        ],
                        'default' => '',
                        'translate' => true,
                    ],
                    'subheadline' => [
                        'label' => __('Sub headline', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text'
                        ],
                        'default' => '',
                        'translate' => true,
                    ],
                    'label_remember' => [
                        'label' => __('Remember me text', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('Remember me', 'age-gate'),
                        'translate' => true,
                    ],
                    'label_aria' => [
                        'label' => __('Aria label', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => '',
                        'translate' => true,
                    ],
                    'label_buttons' => [
                        'label' => __('Yes/No sub question', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('Are you over {age} years of age?', 'age-gate'),
                        'translate' => true,
                    ],
                    'label_yes' => [
                        'label' => __('Yes button text', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('Yes', 'age-gate'),
                        'translate' => true,
                    ],
                    'label_no' => [
                        'label' => __('No button text', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('No', 'age-gate'),
                        'translate' => true,
                    ],
                    'label_day' => [
                        'label' => __('Day label', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('Day', 'age-gate'),
                        'translate' => true,
                    ],
                    'label_month' => [
                        'label' => __('Month label', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('Month', 'age-gate'),
                        'translate' => true,
                    ],
                    'label_year' => [
                        'label' => __('Year label', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('Year', 'age-gate'),
                        'translate' => true,
                    ],
                    'placeholder_day' => [
                        'label' => __('Day placeholder', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('DD', 'age-gate'),
                        'translate' => true,
                    ],
                    'placeholder_month' => [
                        'label' => __('Month placeholder', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('MM', 'age-gate'),
                        'translate' => true,
                    ],
                    'placeholder_year' => [
                        'label' => __('Year placeholder', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('YYYY', 'age-gate'),
                        'translate' => true,
                    ],
                    'label_submit' => [
                        'label' => __('Submit button text', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('Submit', 'age-gate'),
                        'translate' => true,
                    ],
                    'label_no_cookies' => [
                        'label' => __('No cookies message', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('Your browser does not support cookies, you may experience problems entering this site', 'age-gate'),
                        'translate' => true,
                    ],
                    'content' => [
                        'label' => __('Additional content', 'age-gate'),
                        'type' => 'textarea',
                        'attributes' => [
                            'class' => 'ag-rte',
                        ],
                        'default' => '',
                        'translate' => true,
                    ],
                    'error_invalid' => [
                        'label' => __('Invalid inputs', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('Your input was invalid', 'age-gate'),
                        'translate' => true,
                    ],
                    'error_failed' => [
                        'label' => __('Failed error', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('You are not old enough to view this content', 'age-gate'),
                        'translate' => true,
                    ],
                    'error_generic' => [
                        'label' => __('Generic error', 'age-gate'),
                        'type' => 'text',
                        'attributes' => [
                            'class' => 'regular-text',
                        ],
                        'default' => __('An error occurred, please try again', 'age-gate'),
                        'translate' => true,
                    ]
                ]
            ]
        ];
    }
}
