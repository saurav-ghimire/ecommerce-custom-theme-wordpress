<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
if (!empty($meta_options)) {
    $meta_options = explode($options_separator, $meta_options);
} else {
    $meta_options = array();
}


$request = woof()->get_request_data();
$woof_value = "";
if (isset($request['select_' . $meta_key])) {
    $woof_value = $request['select_' . $meta_key];
}
$show_title_label = (isset($meta_settings['show_title_label'])) ? $meta_settings['show_title_label'] : 1;
$css_classes = "woof_block_html_items";
$show_toggle = 0;
$shown_options_tags = 0;
if (isset($meta_settings['show_toggle_button'])) {
    $show_toggle = (int) $meta_settings['show_toggle_button'];
}
//***
$block_is_closed = true;
if (!empty($woof_value)) {
    $block_is_closed = false;
}
if ($show_toggle === 1 AND empty($woof_value)) {
    $css_classes .= " woof_closed_block";
}

if ($show_toggle === 2 AND empty($woof_value)) {
    $block_is_closed = false;
}

$tooltip_text = "";
if (isset($meta_settings['tooltip_text'])) {
    $tooltip_text = $meta_settings['tooltip_text'];
}
if (in_array($show_toggle, array(1, 2))) {
    $block_is_closed = apply_filters('woof_block_toggle_state', $block_is_closed);
    if ($block_is_closed) {
        $css_classes .= " woof_closed_block";
    } else {
        $css_classes = str_replace('woof_closed_block', '', $css_classes);
    }
}
//***
if (WOOF_REQUEST::isset('hide_terms_count_txt_short') AND intval(WOOF_REQUEST::get('hide_terms_count_txt_short')) !== -1) {
    if (intval(WOOF_REQUEST::get('hide_terms_count_txt_short')) === 1) {
        WOOF_REQUEST::set('hide_terms_count_txt', 1);
    } else {
        WOOF_REQUEST::set('hide_terms_count_txt', 0);
    }
}
//***
//meta options
$all_options = array();
$show_count = get_option('woof_show_count', 0);
$show_count_dynamic = get_option('woof_show_count_dynamic', 0);
$hide_dynamic_empty_pos = (intval(WOOF_VERSION) === 1) ? 0 : get_option('woof_hide_dynamic_empty_pos', 0);

if (is_array($meta_options)) {
    foreach ($meta_options as $key => $option) {

        if (!$option) {
            continue;
        }
        $option_title = $option;
        $custom_title = explode('^', $option, 2);
        if (count($custom_title) > 1) {
            $option = $custom_title[1];
            $option_title = $custom_title[0];
        }
        $count_string = "";
        $count = 0;

        if (intval($woof_value) != $key + 1) {
            if ($show_count) {
                $meta_field = array(
                    'key' => $meta_key,
                    'value' => $option,
                );
                if ($show_count_dynamic) {
                    $count_data = array();
                    $count = woof()->dynamic_count(array(), 'select', (WOOF_REQUEST::isset('additional_taxes')) ? WOOF_REQUEST::get('additional_taxes') : "", $meta_field);
                    $count_string = '(' . $count . ')';
                } else {
                    $count = 1;
                    //$count = $term['count'];
                }
            }
            //+++
            if ($hide_dynamic_empty_pos AND $count == 0) {
                continue;
            }
        }

        if (WOOF_REQUEST::get('hide_terms_count_txt')) {
            $count_string = "";
        }
        $all_options[$key + 1] = array(
            'name' => WOOF_HELPER::wpml_translate(null, $option_title) . $count_string,
            'count' => $count
        );
    }
}

if (!count($all_options)) {
    return "";
}
?>
<div data-css-class="woof_meta_select_container" class="woof_meta_select_container woof_container woof_container_<?php echo esc_attr($meta_key) ?>  woof_container_<?php echo esc_attr("select_" . $meta_key) ?>">
    <div class="woof_container_inner">
        <div class="woof_container_inner woof_container_inner_meta_select">
            <?php if ($show_title_label) {
                ?>
                <<?php esc_html_e(apply_filters('woof_title_tag', 'h4')); ?>>
                <?php echo esc_html(WOOF_HELPER::wpml_translate(null, $options['title'])) ?>
                <?php WOOF_HELPER::draw_tooltipe(WOOF_HELPER::wpml_translate(null, $options['title']), $tooltip_text) ?>
                <?php WOOF_HELPER::draw_title_toggle($show_toggle, $block_is_closed); ?>
                </<?php esc_html_e(apply_filters('woof_title_tag', 'h4')); ?>>
            <?php }
            ?>
            <div class="<?php echo esc_attr($css_classes) ?>">
                <?php $meta_id = 'woof_meta_select_' . $meta_key ?>
                <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($meta_id) ?>"><?php echo esc_html(WOOF_HELPER::wpml_translate(null, $options['title'])) ?></label>				
                <select id="<?php echo esc_attr($meta_id) ?>" class="woof_meta_select woof_meta_select_<?php echo esc_attr($meta_key) ?>" name="<?php echo esc_attr("select_" . $meta_key) ?>">
                    <option value="0"><?php echo esc_html(WOOF_HELPER::wpml_translate(null, $options['title'])) ?></option>

                    <?php if (!empty($all_options)): ?>
                        <?php foreach ($all_options as $key => $option) : ?>

                            <option <?php if ($show_count AND $option['count'] == 0 AND $key != $woof_value): ?>disabled=""<?php endif; ?> value="<?php echo esc_attr($key) ?>" <?php selected($key == intval($woof_value)) ?>>
                                <?php
                                echo esc_html($option['name']);
                                ?>
                            </option>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </select> 
                <?php
                $curr_title = "";
                if (isset($meta_options[intval($woof_value) - 1])) {
                    $op_title = explode('^', $meta_options[intval($woof_value) - 1], 2);
                    if (count($op_title) > 1) {
                        $curr_title = $op_title[0];
                    } else {
                        $curr_title = $meta_options[intval($woof_value) - 1];
                    }
                }
                ?>   
                <input type="hidden" value="<?php echo esc_html(WOOF_HELPER::wpml_translate(null, $curr_title)); ?>" data-anchor="woof_n_<?php echo esc_attr("select_" . $meta_key) ?>_<?php echo esc_attr($woof_value) ?>" />

            </div>    
        </div>        
    </div>
</div>
