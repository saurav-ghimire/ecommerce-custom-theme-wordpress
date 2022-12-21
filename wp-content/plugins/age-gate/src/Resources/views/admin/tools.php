<?php $this->layout('default'); ?>



<?php $this->start('additional') ?>

<?php if (current_user_can($export_capability) || current_user_can($import_capability)) : ?>
<hr />
<h2><?php echo esc_html(__('Import/Export', 'age-gate')) ?></h2>
<div class="ag-row">
    <?php if (current_user_can($export_capability)) : ?>
        <div class="ag-col w-1/2">
            <h3><?php echo esc_html(__('Export', 'age-gate')) ?></h3>

            <form name="ag-import" action="<?php echo esc_url(admin_url('admin-post.php')) ?>" method="post">
                <input type="hidden" name="action" value="age_gate_export" />
                <table class="form-table">
                    <tr>
                        <th><?php echo esc_html(__('Select all', 'age-gate')) ?></th>
                        <td>
                            <fieldset class="ag-fields ag-fields--checkbox">
                                <?php $this->insert('partials/fields/checkbox', ['name' => 'all', 'field' => ['type' => 'checkbox']]); ?>
                            </fieldset>
                        </td>
                    </tr>
                    <?php foreach ($export_options as $label => $option) : ?>
                        <tr>
                            <th><?php echo esc_html(ucfirst($label)) ?></th>
                            <td>
                                <fieldset class="ag-fields ag-fields--checkbox">
                                    <?php $this->insert('partials/fields/checkbox', ['name' => $label, 'field' => ['type' => 'checkbox']]); ?>
                                </fieldset>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <?php wp_nonce_field('ag_export', 'ag_export', true, true) ?>
                <?php submit_button('Export', 'primary', 'submit_export') ?>
            </form>
        </div>
    <?php endif; ?>
    <?php if (current_user_can($import_capability)) : ?>
        <div class="ag-col w-1/2">
            <h3><?php echo esc_html(__('Import', 'age-gate')) ?></h3>

            <form name="ag-import" action="<?php echo esc_url(admin_url('admin-post.php')) ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="age_gate_import" />
                <label class="ag-file">
                    <input type="file" id="file" name="ag_settings_import" accept=".json" aria-label="File browser example">
                    <span class="ag-file-custom" data-text="Choose file..."></span>
                </label>
                <?php wp_nonce_field('ag_import', 'ag_import', true, true) ?>
                <?php submit_button('Import', 'primary', 'submit_import') ?>
            </form>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php if (current_user_can($reset_capability)) : ?>
    <hr />

    <h3><?php echo esc_html(__('Reset Age Gate', 'age-gate')) ?></h3>

    <div class="ag-row">
        <div class="ag-col w-1/2">
            <p><?php echo esc_html(__('Restore Age Gate to default settings.', 'age-gate')) ?></p>
            <p><b><?php echo esc_html(__('This cannot be undone, so export your settings first', 'age-gate')) ?></b></p>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')) ?>" class="ag-form__reset">
                <?php wp_nonce_field('ag_reset_settings', 'ag_reset_settings', true, true) ?>
                <input type="hidden" name="action" value="age_gate_reset">
                <input type="password" name="pwd" placeholder="<?php echo esc_attr(__('Enter your password', 'age-gate')) ?>" />
                <button type="submit" class="button button--remove"><?php echo esc_html(__('Reset', 'age-gate')) ?></button>
            </form>
        </div>
        <div class="ag-col w-1/2">
            <p><?php echo esc_html(__('Clear all meta data.', 'age-gate')) ?></p>
            <p><b><?php echo esc_html(__('Remove any custom ages, bypass and restrict settings. This cannot be undone.', 'age-gate')) ?></b></p>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')) ?>" class="ag-form__reset">
                <?php wp_nonce_field('ag_reset_post', 'ag_reset_post', true, true) ?>
                <input type="hidden" name="action" value="age_gate_reset_post">
                <input type="password" name="pwd" placeholder="<?php echo esc_attr(__('Enter your password', 'age-gate')) ?>" />
                <button type="submit" class="button button--remove"><?php echo esc_html(__('Reset', 'age-gate')) ?></button>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php $this->stop() ?>
