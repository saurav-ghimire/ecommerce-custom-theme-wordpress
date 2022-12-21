<?php $this->layout('layouts/default'); ?>

<?php foreach ($fields ?? [] as $name => $section) : ?>
    <?php if ($section['title'] ?? false) : ?>
        <h3><?php echo esc_html($section['title']) ?></h3>
    <?php endif; ?>
    <?php if ($section['subtitle'] ?? false) : ?>
        <p><?php echo esc_html($section['subtitle']) ?></p>
    <?php endif; ?>
    <table class="form-table">
        <?php foreach ($section['fields'] ?? [] as $name => $field) : ?>
            <tr>
                <th><?php echo esc_html($field['label']) ?></th>
                <td>
                    <fieldset class="ag-fields ag-fields--<?php echo esc_attr($field['type']) ?>">

                    <?php
                        try {
                            $this->insert('partials/fields/' . $field['type'], ['name' => $name, 'field' => $field]);
                        } catch (Exception $e) {
                            $this->insert('partials/fields/input', ['name' => $name, 'field' => $field]);
                        }

                        if ($field['translate'] ?? false) {
                            foreach ($languages['available'] ?? [] as $code => $lang) {
                                $langName = $code . '.' . $name;

                                try {
                                    $this->insert('partials/fields/'.$field['type'], ['name' => $langName, 'field' => $field, 'lang' => $code]);
                                } catch (Exception $e) {
                                    $this->insert('partials/fields/input', ['name' => $langName, 'field' => $field, 'lang' => $code]);
                                }
                            }
                        }

                    ?>

                    </fieldset>

                    <?php echo esc_html($field['type']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <hr />
<?php endforeach; ?>
