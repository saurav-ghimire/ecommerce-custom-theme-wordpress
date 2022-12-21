 <input type="hidden" name="<?php echo esc_attr($this->form_key($field_prefix . '.' . $name)) ?>" value="<?php echo esc_attr($data[$name] ?? (!empty($lang) ? '' : $field['default'] ?? '')) ?>" />
