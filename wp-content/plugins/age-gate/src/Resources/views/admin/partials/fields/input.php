<?php $this->layout('partials/fields/wrapper', ['field' => $field, 'lang' => $lang ?? false]) ?>

<?php $this->start('input') ?>

    <input type="<?php echo esc_attr($field['type']) ?>" name="<?php echo esc_attr($this->form_key($field_prefix . '.' . $name)) ?>" id="<?php echo esc_attr($this->form_id($field_prefix . '.' . $name)) ?>" <?php echo html_build_attributes($field['attributes'] ?? []) ?> value="<?php echo esc_attr(stripslashes($data[$name] ?? (!empty($lang) ? '' : $field['default'] ?? ''))) ?>" />

<?php $this->stop() ?>
