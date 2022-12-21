
<input type="hidden" name="age_gate[age]" value="<?php echo esc_attr($encrypt->encrypt($content->getAge(true) ?: $content->getAge())) ?>" />
