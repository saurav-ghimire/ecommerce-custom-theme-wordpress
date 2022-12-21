<?php $this->layout('partials/fields/wrapper', ['field' => $field, 'lang' => $lang ?? false]) ?>

<?php $this->start('input') ?>
    <div class="ag-preview"><?php if ($data[$name] ?? false) : ?>
            <?php if (strpos(get_post_mime_type($data[$name]), 'video') !== false) : ?>
                <video controls src="<?php echo esc_url(wp_get_attachment_url($data[$name])); ?>"></video>
            <?php else: ?>
                <img src="<?php echo esc_url(wp_get_attachment_url($data[$name])); ?>" />
            <?php endif; ?>
        <?php endif; ?></div>
    <input type="hidden" name="<?php echo esc_attr($this->form_key($field_prefix . '.' . $name)) ?>" value="<?php echo esc_attr($data[$name] ?? (!empty($lang) ? '' : $field['default'] ?? '')) ?>" />
    <button type="button" class="button ag-media-clear"><?php echo esc_html__('Remove', 'age-gate') ?></button>
    <button type="button" class="button js-modal" data-modal="ag-media"><?php echo esc_html__('Select image', 'age-gate') ?></button>
<?php $this->stop(); ?>

