<?php if ($settings->remember) : ?>
    <div <?php echo $this->attr('age-gate-remember-wrapper') ?>>
        <label <?php echo $this->attr('age-gate-remember') ?>>
            <input <?php echo $this->attr('age-gate-remember-checkbox') ?> <?php echo $settings->rememberAutoCheck ? 'checked' : '' ?> /> <span <?php echo $this->attr('age-gate-remember-text') ?>><?php echo esc_html($settings->labelRemember) ?></span>
        </label>
    </div>
<?php endif; ?>
