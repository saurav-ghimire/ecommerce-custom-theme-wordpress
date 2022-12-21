<div <?php echo $this->attr('age-gate-shortcode'); ?><?php echo ($settings->poster ? ' style="background-image: url(' . esc_url($settings->poster) . ');"' : '') ?>>
    <form method="post" <?php echo $this->attr('age-gate-form'); ?>>
        <div <?php echo $this->attr('age-gate-shortcode-inner'); ?>>
            <?php if ($settings->inputType !== 'buttons') : ?>
                <<?php echo esc_attr($settings->challengeElement ?: 'p') ?> <?php echo $this->attr('age-gate-challenge') ?>>
                    <?php echo esc_html(sprintf($this->stringTemplate($settings->labelButtons, ['age' => $content->getAge($settings->anonymous)]), $content->getAge($settings->anonymous))) ?>
                </<?php echo esc_attr($settings->challengeElement ?: 'p') ?>>
            <?php endif ?>
            <?php
                try {
                    $this->insert('partials/form/sections/' . $settings->inputType);
                } catch (Exception $e) {
                    $settings->set('inputType', 'buttons');
                    $this->insert('partials/form/sections/buttons');
                }
            ?>
            <?php if ($settings->method === 'js') : ?>
                <div <?php echo $this->attr('age-gate-errors') ?>></div>
            <?php endif; ?>

            <?php if ($settings->method !== 'js' && $e && $c == ($_POST['ag_sc'] ?? false)) : ?>
                <div <?php echo $this->attr('age-gate-errors') ?>>
                    <p <?php echo $this->attr('age-gate-error') ?>><?php echo $this->mdLine(end($e)) ?></p>

                </div>
            <?php endif; ?>

            <?php if ($settings->inputType !== 'buttons') : ?>
                <?php $this->insert('partials/form/submit') ?>
            <?php endif; ?>
            <input type="hidden" name="age_gate[age]" value="<?php echo esc_attr($encrypt->encrypt($content->getAge())) ?>" />
            <input type="hidden" name="age_gate[shortcode]" value="<?php echo esc_attr($encrypt->encrypt($settings->inputType)) ?>" />
            <input type="hidden" name="ag_sc" value="<?php echo esc_attr($c) ?>" />
        </div>
    </form>
</div>
