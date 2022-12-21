<?php $this->layout('theme::partials/form/sections/fieldset'); ?>

<ol <?php echo $this->attr('age-gate-form-elements')?>>
    <?php foreach ($fields as $key => $field) : ?>
        <li <?php echo $this->attr('age-gate-form-section') ?>>
            <?php echo
                $this->fetch(
                    'theme::partials/form/fields/select',
                    array_merge(
                        [
                            'key' => $key
                        ],
                        $field
                    )
                )
            ?>
        </li>
    <?php endforeach; ?>
</ol>
