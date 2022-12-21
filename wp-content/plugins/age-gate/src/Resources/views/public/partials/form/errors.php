<?php if ($errors) : ?>

    <div <?php echo $this->attr('age-gate-errors') ?>>

        <?php
            $key = array_key_last($errors);
            $error = end($errors);
        ?>
        <p <?php echo $this->attr('age-gate-error') ?>>
            <?php echo $this->mdLine(apply_filters('age_gate/error/' . $key, $error)) ?>
        </p>

    </div>
<?php endif; ?>
<?php if (is_customize_preview()) : ?>
    <div <?php echo $this->attr('age-gate-errors') ?>>
        <p <?php echo $this->attr('age-gate-error') ?>><?php echo esc_html(apply_filters('age_gate/error/demo', __('Demonstration error message'))) ?></p>
    </div>
<?php endif; ?>
<?php if ($settings->method === 'js') : ?>
    <div <?php echo $this->attr('age-gate-errors') ?>></div>
<?php endif ?>
