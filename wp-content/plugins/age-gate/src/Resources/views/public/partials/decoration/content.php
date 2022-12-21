<?php if ($preview['content'] ?? $settings->content) : ?>
    <div <?php echo $this->attr('age-gate-additional-information') ?>>
        <?php echo do_shortcode($this->mdText($preview['content'] ?? $settings->content)) ?>
    </div>
<?php endif; ?>
