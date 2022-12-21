<?php $this->layout('partials/fields/wrapper', ['field' => $field, 'lang' => $lang ?? false]) ?>

<?php $this->start('input') ?>
    <?php if (!count($field['fields'] ?? [])) : ?>
        <?php echo esc_html($noOptions ?? 'No options') ?>
    <?php endif; ?>
    <?php $parent = $field['parent'] ?? $name; ?>
    <?php foreach ($field['fields'] ?? [] as $name => $field) : ?>
    <?php
        try {
            $this->insert('partials/fields/'.$field['type'], ['name' => $parent . '.' . $name, 'field' => $field]);
        } catch (Exception $e) {
            $this->insert('partials/fields/input', ['name' => $parent . '.' . $name, 'field' => $field]);
        } ?>
    <?php endforeach; ?>
<?php $this->stop(); ?>
