<?php 
/**
 * WWP Notice bar template
 * 
 * @since 2.1.2
 */

if (!defined('ABSPATH')) {exit;} // Exit if accessed directly
?>
<style>#screen-meta, #contextual-help-link-wrap, #screen-options-link-wrap { top: 5px !important; }</style>
<div id="wwp-notice-bar" class="wwp-dismiss-container top-lite">
    <span class="wwp-notice-bar-message">
        <?php
        printf(
            wp_kses($message,
                [
                    'a' => [
                        'href'   => [],
                        'target' => [],
                    ]
                ]
            ),
            esc_url($upgrade_link)
        );
        ?>
    </span>
</div>