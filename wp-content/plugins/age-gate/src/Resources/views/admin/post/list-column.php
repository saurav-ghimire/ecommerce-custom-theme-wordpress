
<?php // TODO: APPLY JS TOGGLES ?>
<?php if ($content->isRestricted()) : ?>
    <span data-ag-tooltip="<?php echo esc_html__('Restricted to', 'age-gate'); ?>: <?php echo $content->getAge(); ?>" data-quick-checked="">
        <i class="dashicons dashicons-lock"></i>
    </span>
    <span class="screen-reader-text"><?php echo esc_html__('Restricted to', 'age-gate'); ?>: <strong class="age-display"><?php echo $content->getAge(); ?></strong></span>
<?php else: ?>
    <span data-ag-tooltip="<?php echo esc_html__('Unrestricted', 'age-gate'); ?>" data-quick-checked="">
        <i class="dashicons dashicons-unlock"></i>
    </span>
    <span class="screen-reader-text"><?php echo esc_html__('Unrestricted', 'age-gate'); ?></span>
<?php endif; ?>
