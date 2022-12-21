<?php do_action('age_gate/fields/age_field', $content->getAge()); ?>
<?php /*<input type="hidden" name="action" value="age_gate_submit" />*/ ?>
<?php echo str_replace('id="age_gate[nonce]"', '', wp_nonce_field('age_gate_form', 'age_gate[nonce]', true, false)); ?>
<input type="hidden" name="age_gate[lang]" value="<?php echo esc_attr($settings->language ?? 'en'); ?>" />
<?php if ($settings->method === 'js') : ?>
    <input type="hidden" name="age_gate[confirm]" />
<?php endif; ?>
