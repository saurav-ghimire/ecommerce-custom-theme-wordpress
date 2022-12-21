<!DOCTYPE html>
<html <?php echo get_language_attributes() ?> <?php echo $this->attr('age-gate-html') ?>>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php if (!current_theme_supports('title-tag')): ?>
        <title><?php wp_title('-') ?></title>
    <?php endif; ?>
    <?php if ($settings->viewport): ?>
        <meta name="viewport" content="<?php echo esc_attr(apply_filters('age_gate/meta/viewport', 'width=device-width, minimum-scale=1, maximum-scale=1')) ?>">
    <?php endif; ?>
    <?php wp_head() ?>
</head>
    <body <?php echo $this->attr('age-gate-body') ?>>
        <?php echo $this->section('content') ?>
        <?php wp_footer() ?>
    </body>
</html>
