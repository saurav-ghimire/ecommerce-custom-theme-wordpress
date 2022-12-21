<?php $this->layout('partials/fields/wrapper', ['field' => $field, 'lang' => $lang ?? false]) ?>

<?php $this->start('input') ?>
    <textarea name="<?php echo esc_attr($this->form_key($field_prefix . '.' . $name)) ?>" <?php echo html_build_attributes($field['attributes'] ?? []) ?>><?php echo esc_html(stripslashes($data[$name] ?? (!empty($lang) ? '' : $field['default'] ?? ''))) ?></textarea>

<?php $this->stop() ?>
