<label <?php echo $this->attr('age-gate-' . $key . '-label') ?>><?php echo esc_html($label) ?></label>
<input <?php echo $this->attr('age-gate-' . $key . '-input') ?> placeholder="<?php echo esc_attr($placeholder) ?>" required value="<?php echo esc_attr($value) ?>" />
