<?php

namespace AgeGate\Admin\Content;

trait DisabledTypes
{
    private $defaultDisabledTypes = [
        'shop_order',
        'attachment',
        'acf-field-group',
    ];

    protected function getDefaultDisabledTypes()
    {
        $types = apply_filters('age_gate/disabled_content/defaults', $this->defaultDisabledTypes);

        if (!is_array($types)) {
            return $this->defaultDisabledTypes;
        }

        return $types;
    }
}
