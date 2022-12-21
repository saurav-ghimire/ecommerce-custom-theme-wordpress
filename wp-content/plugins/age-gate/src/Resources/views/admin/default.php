<?php $this->layout('layouts/default'); ?>

<?php foreach ($fields ?? [] as $name => $section) : ?>
    <?php if ($section['title'] ?? false) : ?>
        <h3><?php echo esc_html($section['title']) ?></h3>
    <?php endif; ?>
    <?php if ($section['subtitle'] ?? false) : ?>
        <p><?php echo esc_html($section['subtitle']) ?></p>
    <?php endif; ?>
    <table class="form-table" <?php echo html_build_attributes($section['condition'] ?? []) ?>>
        <?php foreach ($section['fields'] ?? [] as $name => $field) : ?>
            <tr <?php echo html_build_attributes($field['condition'] ?? []) ?>>
                <th>
                    <?php echo esc_html($field['label']) ?>
                    <?php if ($field['sublabel'] ?? false) : ?>
                        <sup>(<?php echo esc_html($field['sublabel']) ?>)</sup>
                    <?php endif; ?>
                </th>
                <td>
                    <fieldset class="ag-fields ag-fields--<?php esc_attr($field['type']) ?>">

                    <?php
                        try {
                            $this->insert('partials/fields/' . $field['type'], ['name' => $name, 'field' => $field]);
                        } catch (Exception $e) {
                            // dump($e->getMessage());
                            $this->insert('partials/fields/input', ['name' => $name, 'field' => $field]);
                        }

                        if ($field['translate'] ?? false) {
                            foreach ($languages['available'] ?? [] as $code => $lang) {
                                $langName = $code . '.' . $name;

                                if ($field['attributes']['required'] ?? false) {
                                    unset($field['attributes']['required']);
                                }

                                try {
                                    $this->insert('partials/fields/'.$field['type'], ['name' => $langName, 'field' => $field, 'lang' => $code]);
                                } catch (Exception $e) {
                                    $this->insert('partials/fields/input', ['name' => $langName, 'field' => $field, 'lang' => $code]);
                                }
                            }
                        }

                    ?>
                    </fieldset>
                    <?php if ($field['docs'] ?? false) : ?>
                        <a href="<?php echo esc_url($field['docs']['link'] ?? '#') ?>" class="<?php echo esc_attr($field['docs']['class'] ?? 'button') ?> ag-docs-link" target="_blank" rel="noopener noreferrer"><?php echo esc_html($field['docs']['label'] ?? __('Documentation', 'age-gate')) ?> <span class="dashicons dashicons-external"></span></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <hr />
<?php endforeach; ?>


<?php $this->start('after') ?>
    <?php echo $this->section('additional') ?>
<?php $this->stop() ?>
