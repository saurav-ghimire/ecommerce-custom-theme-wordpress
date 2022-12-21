<?php do_action('age_gate/script_template/before'); ?>
<template id="tmpl-age-gate">
    <?php do_action('age_gate/script_content/before'); ?>
    <?php echo $this->section('content'); ?>
    <?php do_action('age_gate/script_content/after'); ?>
</template>
<?php do_action('age_gate/script_template/after'); ?>
