<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly ?>

<tr valign="top">

    <th scope="row" class="titledesc">
        <label for="<?php echo $data['id']; ?>"><?php echo $data['title']; ?></label>
        <?php echo isset($data['desc_tip']) && !empty($data['desc_tip']) ? wc_help_tip($data['desc_tip']) : ''; ?>
    </th>

    <td class="forminp forminp-<?php echo sanitize_title($data['type']); ?>">
        <style type="text/css"><?php echo "div#wp-" . $data['id'] . "-wrap{width: 70% !important;}"; ?></style><?php 
        
        $data_id_option = get_option($data['id']);
        $ass_wysiwyg_val = !empty($data_id_option) ? $data_id_option : $data['default'];

        wp_editor(html_entity_decode($ass_wysiwyg_val), $data['id'], array(
            'wpautop' => true,
            'textarea_name' => "wwp_editor[" . $data['id'] . "]",
            'textarea_rows' => 8
        )); ?>
        
        <p><?php echo $data['desc']; ?></p>
    </td>

</tr>