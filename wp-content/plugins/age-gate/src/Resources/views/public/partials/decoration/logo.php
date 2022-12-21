<div <?php echo $this->attr('age-gate-heading') ?>>
    <?php if ($settings->logo) : ?>
        <img src="<?php echo esc_url($settings->logo) ?>" alt="<?php echo get_bloginfo('name') ?>"<?php echo $this->attr('age-gate-logo') ?> />
    <?php else : ?>
        <<?php echo esc_attr($settings->headingElement ?: 'h1') ?> <?php echo $this->attr('age-gate-heading-title') ?>><?php echo esc_html($settings->heading ?: bloginfo('title')) ?></<?php echo esc_attr($settings->headingElement ?: 'h1') ?>>
    <?php endif; ?>
</div>
