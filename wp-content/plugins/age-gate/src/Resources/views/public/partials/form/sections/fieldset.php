<div <?php echo $this->attr('age-gate-form-fields') ?>>
    <?php do_action('age_gate/fields/before'); ?>
    <?php echo $this->section('content') ?>
    <?php do_action('age_gate/fields/after') ?>
</div>
