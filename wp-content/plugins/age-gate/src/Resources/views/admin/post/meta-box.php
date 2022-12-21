<?php
    $checked = false;

    if ($content->getType() === 'term') {
        $checked = get_term_meta($content->getID(), $contentOption, true);
    } else {
        $checked = get_post_meta($content->getID(), $contentOption, true);
    }

    $restricted = $settings->type === 'all' ? !$checked : $checked;
?>

<div class="ag-post-metabox">
    <h4 class="ag-post-metabox__title" data-age="<?php echo esc_attr($content->getAge()) ?>" data-text-restrict="<?php echo esc_attr($settings->anonymous ? __('Restricted', 'age-gate') : __('Restricted to {0}', 'age-gate')) ?>" data-text-unrestrict="<?php echo esc_attr(__('Unrestricted', 'age-gate')) ?>">
        <i class="dashicons <?php echo esc_attr($restricted ? 'dashicons-lock' : 'dashicons-unlock') ?>"></i>
        <span class="ag-post-metabox__text">
            <?php echo ($restricted ? esc_html($settings->anonymous ? __('Restricted', 'age-gate') : sprintf(__('Restricted to %s', 'age-gate'), $content->getAge())) : esc_html(__('Unrestricted', 'age-gate'))) ?>
        </span>
        <?php if ($settings->multiAge && $setAge && !$settings->anonymous) : ?>
            <button type="button" class="button-link ag-post-metabox__age-toggle"<?php echo (!$restricted ? ' style="display: none;"' : ''); ?>><?php echo esc_html__('Change', 'age-gate' ) ?></button>
        <?php endif; ?>
    </h4>

    <?php if ($settings->multiAge && $setAge && !$settings->anonymous) : ?>
        <?php $this->insert('post/meta/age') ?>
    <?php endif; ?>

    <?php if ($setRestriction) : ?>
        <div class="ag-post-metabox__item">
            <?php $this->insert('post/type/' . $settings->type, ['checked' => $checked]) ?>
        </div>
    <?php endif; ?>
    <?php if ($content->getType() === 'post' && !$checked && $content->getRestricted() && $settings->type === 'selected') : ?>
        <?php
            $inherited = collect($content->getTerms())->where( 'age', $content->getAge())->first();
            if ($inherited) : ?>
                <p><?php echo esc_html(sprintf(__('This content is unrestricted, but is inheriting an %s restriction from term %s'), $content->getAge(), $inherited->name)) ?></p>
            <?php endif; ?>
    <?php endif; ?>
    <?php wp_nonce_field( 'age_gate_post_edit', '_agn') ?>
</div>
