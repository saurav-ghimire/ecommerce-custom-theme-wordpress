<?php

    /**
     * @hooked AgeGate\Template\Template::renderFormOpen - 10
     */
    do_action('age_gate/form/open');

    // logo
    do_action('age_gate/logo');

    // subheading
    do_action('age_gate/headline');

    // messages
    do_action('age_gate/subheadline');

    if (!$settings->lockout) {
        // BEFORE custom fields
        do_action('age_gate/custom/before');

        // INPUTS
        do_action('age_gate/fields', $settings->inputType);

        // AFTER CUSTOM FIELDS
        do_action('age_gate/custom/after');

        // remember me
        do_action('age_gate/remember');
    }

    do_action('age_gate/errors');

    // SUBMIT
    if ($settings->inputType !== 'buttons' && !$settings->lockout) {
        do_action('age_gate/submit');
    }

    // ADDITIONAL
    do_action('age_gate/additional');

    /**
     * @hooked AgeGate\Template\Template::renderFormClose - 10
     */
    do_action('age_gate/form/close');
