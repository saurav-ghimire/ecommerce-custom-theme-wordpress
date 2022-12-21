<<?php echo esc_attr($settings->subHeadlineElement ?: 'p') ?> <?php echo $this->attr('age-gate-subheadline') ?>>
    <?php echo esc_html(sprintf($this->stringTemplate($settings->subheadline, ['age' => $content->getAge($settings->anonymous)]), $content->getAge($settings->anonymous))) ?>
</<?php echo esc_attr($settings->subHeadlineElement ?: 'p') ?>>
