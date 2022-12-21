<?php if ($settings->headline) : ?>
    <<?php echo esc_attr($settings->headlineElement ?: 'h2') ?> <?php echo $this->attr('age-gate-headline') ?>>
        <?php echo esc_html(sprintf($this->stringTemplate($settings->headline, ['age' => $content->getAge($settings->anonymous)]), $content->getAge($settings->anonymous))) ?>
    </<?php echo esc_attr($settings->headlineElement ?: 'h2') ?>>
<?php endif; ?>
