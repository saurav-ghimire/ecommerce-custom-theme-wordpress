<?php if ($languages['available'] ?? []) : ?>

    <?php $flag = $lang ? $languages['available'][$lang]['flag'] : $languages['default']['flag']; ?>
    <img src="<?php echo esc_url($flag) ?>" alt="<?php echo esc_attr($name) ?>" class="ag-flag" />
<?php endif; ?>
