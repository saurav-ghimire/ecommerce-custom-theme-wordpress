<?php $this->layout('layouts/default'); ?>

<?php $data = \Asylum\Utility\Arr::undot($data); ?>
<div class="ag-row">
    <?php foreach ($data['sections'] ?? [] as $key => $section) : ?>
        <div class="ag-col">
            <?php
                switch ($key) {
                    case 'set_content':
                        $label = __('Restrict/Bypass indiviual content', 'age-gate');
                        break;
                    case 'set_age':
                        $label = __('Change age for indiviual content', 'age-gate');
                        break;
                    case 'reset':
                    case 'export':
                    case 'import':
                        $label = sprintf(__('User can %s all settings', 'age-gate'), $key);
                        break;
                    default:
                        $label = sprintf(__('Manage %s settings', 'age-gate'), $key);
                        break;
                }
            ?>

            <p><?php echo esc_html($label) ?></p>
            <?php foreach ($data['roles'] as $slug => $role) : ?>
                <?php if ($slug !== 'administrator') : ?>
                    <div>
                        <label class="ag-switch">
                            <input type="checkbox" name="ag_settings[<?php echo esc_attr($key); ?>][<?php echo esc_attr($slug) ?>]" value="1" <?php echo(($role['capabilities'][$section] ?? false) ? 'checked' : '') ?> />
                            <span class="ag-switch__slider"></span>
                        </label>
                        <?php echo esc_html($role['name']) ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
