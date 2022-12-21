<div class="ag-field ag-field--<?php echo esc_attr($field['type']) ?> <?php echo esc_attr($field['wrapper']['class'] ?? '') ?>">
    <?php if ($field['translate'] ?? false) : ?>
        <?php $this->insert('partials/global/flag', [
            'lang' => $lang ?? false,
            'name' => '',
        ]) ?>
    <?php endif; ?>

    <?php echo $this->section('input') ?>

    <?php if ($field['subtext'] ?? false) : ?>
        <p class="ag-small"><?php echo $this->md(esc_html($field['subtext'])) ?></p>
    <?php endif; ?>
</div>
