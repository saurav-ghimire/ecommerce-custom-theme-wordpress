<?php $this->layout('partials/fields/wrapper', ['field' => $field, 'lang' => $lang ?? false]) ?>

<?php $this->start('input') ?>

    <?php echo wpautop(esc_html($field['message'] ?? '')) ?>

    <?php if ($field['suboption'] ?? false) : ?>
        <p><?php echo esc_html(__('Your previous styles are below if anything is missing.', 'age-gate')) ?> <button class="button-link ag-remove-legacy-css" type="button" data-id="<?php echo esc_attr(wp_create_nonce('ag_clear_css')) ?>"><?php echo esc_html(__('Remove legacy styles', 'age-gate')) ?></button></p>
        <pre><code class="language-css"><?php echo esc_html(trim($field['suboption'])) ?></code></pre>

    <?php endif; ?>

<?php $this->stop() ?>
