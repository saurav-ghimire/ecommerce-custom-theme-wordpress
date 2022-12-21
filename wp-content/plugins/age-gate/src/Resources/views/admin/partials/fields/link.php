<?php $this->layout('partials/fields/wrapper', ['field' => $field, 'lang' => $lang ?? false]) ?>

<?php $this->start('input') ?>

<button type="button" class="button button--link">Select link</button>
<?php if ($data[$name] ?? false) : ?>
    <button type="button" class="button button--remove">Remove link</button>
<?php endif; ?>
<input type="hidden" name="<?php echo esc_attr($this->form_key($field_prefix . '.' . $name)) ?>" value="<?php echo esc_attr($data[$name] ?? $field['default'] ?? '') ?>" />

<?php if ($data[$name] ?? false) : ?>
    <span class="link-display"><?php echo esc_html($data[$name]) ?></span>
<?php endif; ?>

<?php $this->stop(); ?>
