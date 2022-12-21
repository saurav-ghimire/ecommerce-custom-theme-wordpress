<?php

namespace AgeGate\Admin\Settings;

trait Tools
{
    protected function getToolsFields()
    {
        return [
            [
                'title' => 'Developer options',
                'subtitle' => '',
                'fields' => [
                    'dev_warning' => [
                        'type' => 'checkbox',
                        'label' => __('Development warning', 'age-gate'),
                        'default' => true,
                        'subtext' => __('Show warnings if using development version', 'age-gate'),
                    ],
                    'dev_endpoint' => [
                        'type' => 'checkbox',
                        'label' => __('Developer endpoint', 'age-gate'),
                        'default' => false,
                        'subtext' => __('Enable a developer API endpoint containing Age Gate information', 'age-gate'),
                    ],
                    'feedback' => [
                        'type' => 'checkbox',
                        'label' => __('Send feedback', 'age-gate'),
                        'default' => false,
                        'subtext' => __('Occasionally send settings information to the developers', 'age-gate'),
                    ],
                ]
            ],
        ];
    }
}
