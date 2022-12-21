<?php
    $model = collect($fields)->pluck('model');

    $model = $model->merge($model->toArray())->flatten()->unique()->filter();

    $model = $model->mapWithKeys(function($item) use ($data) {
        return [$item => $data[$item] ?? null];
    })->toArray();

    if ($model) {
        $model = collect($model)->map(fn($item, $key) => "$key:'$item'")->values()->implode(',');
    }
?>

<div class="wrap" <?php echo ($model ? 'x-data="{' . esc_attr($model) . '}"' : '') ?>>
    <h2><?php echo esc_html(get_admin_page_title()) ?></h2>

    <div class="ag-errors"></div>

    <?php if (current_user_can($permission)) : ?>
        <form method="post" action="<?php echo esc_url(admin_url('/admin-post.php')) ?>" id="ag-settings" class="ag-settings-form" data-form="<?php echo esc_attr($action) ?>">
            <input type="hidden" name="action" value="age_gate_<?php echo esc_attr($action) ?>">
            <?php wp_nonce_field('age_gate_' . $action) ?>
            <?php echo $this->section('content'); ?>

            <?php submit_button(__('Save settings', 'age-gate')) ?>
        </form>
    <?php endif; ?>
    <?php echo $this->section('after') ?>
</div>
