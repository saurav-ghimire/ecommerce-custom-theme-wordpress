<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly ?>

<div id='wwpp-wholesale-roles-page' class='wwp-page wrap nosubsub'>
    <h2><?php _e('Wholesale Roles', 'woocommerce-wholesale-prices');?></h2>
    <a href="#" class="page-title-action"><?php _e('Add New Role', 'woocommerce-wholesale-prices');?></a>
    <div id="col-container">
        <div id="col-right">
            <div class="col-wrap">
                <div>
                    <table id="wholesale-roles-table" class="wp-list-table widefat fixed tags" style="margin-top: 74px;">

                        <thead>
                            <tr>
                                <th scope="col" id="role-name" class="manage-column column-role-name"><span><?php _e('Name', 'woocommerce-wholesale-prices');?></span></th>
                                <th scope="col" id="role-key" class="manage-column column-role-key"><span><?php _e('Key', 'woocommerce-wholesale-prices');?></span></th>
                                <th scope="col" id="role-desc" class="manage-column column-role-desc"><span><?php _e('Description', 'woocommerce-wholesale-prices');?></span></th>
                            </tr>
                        </thead>

                        <tbody id="the-list">
                        <?php
$count = 0;
foreach ($all_registered_wholesale_roles as $role_key => $role) {
    $count++;
    $alternate = '';

    if ($count % 2 != 0) {
        $alternate = 'alternate';
    }
    ?>

                            <tr id="<?php echo $role_key; ?>" class="<?php echo $alternate; ?>">

                                <td class="role-name column-role-name">

                                    <?php if (array_key_exists('main', $role) && $role['main']) {?>

                                        <strong><a class="main-role-name"><?php echo $role['roleName']; ?></a></strong>

                                        <div class="row-actions">
                                            <span class="edit"><a class="edit-role" href="#"><?php _e('Edit', 'woocommerce-wholesale-prices');?></a>
                                        </div>

                                    <?php } else {?>

                                        <strong><a><?php echo $role['roleName']; ?></a></strong><br>

                                        <div class="row-actions">
                                            <span class="edit"><a class="edit-role" href="#"><?php _e('Edit', 'woocommerce-wholesale-prices');?></a> | </span>
                                            <span class="delete"><a class="delete-role" href="#"><?php _e('Delete', 'woocommerce-wholesale-prices');?></a></span>
                                        </div>

                                    <?php }?>

                                </td>

                                <td class="role-key column-role-key"><?php echo $role_key; ?></td>

                                <td class="role-desc column-role-desc"><?php echo $role['desc']; ?></td>

                            </tr>
                        <?php }?>
                        </tbody>

                        <tfoot>
                            <tr>
                                <th scope="col" id="role-name" class="manage-column column-role-name"><span><?php _e('Name', 'woocommerce-wholesale-prices');?></span></th>
                                <th scope="col" id="role-key" class="manage-column column-role-key"><span><?php _e('Key', 'woocommerce-wholesale-prices');?></span></th>
                                <th scope="col" id="role-desc" class="manage-column column-role-desc"><span><?php _e('Description', 'woocommerce-wholesale-prices');?></span></th>
                            </tr>
                        </tfoot>

                    </table>

                    <br class="clear">
                </div>
                <div class="upsell-area">
                    <h1><?php _e('Add additional wholesale roles', 'woocommerce-wholesale-prices');?></h1>
                    <p><?php _e('You\'re currently using the free version of WooCommerce Wholesale Prices which lets you have one level of wholesale customers.', 'woocommerce-wholesale-prices');?></p>
                    <p><?php
echo sprintf(__('In the <a href="%1$s" target="_blank">Premium add-on</a> you can add multiple wholesale roles. This will let you create separate "levels" of wholesale customers,
                        each of which can have separate wholesale pricing, shipping and payment mapping, order minimums and more.', 'woocommerce-wholesale-prices'), 'https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=wwp&utm_medium=upsell&utm_campaign=wwprolespagelink'); ?>
                    </p>
                    <p>
                        <a class="button" href="https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=wwp&utm_medium=upsell&utm_campaign=wwprolespagebutton" target="_blank">
                            <?php _e('See the full feature list', 'woocommerce-wholesale-prices');?>
                            <span class="dashicons dashicons-arrow-right-alt" style="margin-top: 7px"></span>
                        </a>
                    </p>
                </div>
            </div><!--.col-wrap-->

        </div><!--#col-right-->

        <div id="col-left">

            <div class="col-wrap">

                <div class="form-wrap">
                    <h3><?php _e('Edit Wholesale Role', 'woocommerce-wholesale-prices');?></h3>

                    <div id="wholesale-form">

                        <div class="form-field form-required">
                            <label for="role-name"><?php _e('Role Name', 'woocommerce-wholesale-prices');?></label>
                            <input id="role-name" value="" size="40" type="text">
                            <p><?php _e('Required. Recommended to be unique.', 'woocommerce-wholesale-prices');?></p>
                        </div>

                        <div class="form-field form-required">
                            <label for="role-key"><?php _e('Role Key', 'woocommerce-wholesale-prices');?></label>
                            <input id="role-key" value="" size="40" type="text">
                            <p><?php _e('Required. Must be unique. Must only contain letters, numbers and underscores', 'woocommerce-wholesale-prices');?></p>
                        </div>

                        <div class="form-field form-required">
                            <label for="role-desc"><?php _e('Description', 'woocommerce-wholesale-prices');?></label>
                            <textarea id="role-desc" rows="5" cols="40"></textarea>
                            <p><?php _e('Optional.', 'woocommerce-wholesale-prices');?></p>
                        </div>

                        <p class="submit edit-controls">
                            <input id="edit-wholesale-role-submit" class="button button-primary" value="<?php _e("Edit Wholesale Role", "woocommerce-wholesale-prices");?>" type="button"><span class="spinner"></span>
                            <input id="cancel-edit-wholesale-role-submit" class="button button-secondary" value="<?php _e("Cancel Edit", "woocommerce-wholesale-prices");?>" type="button"/>
                        </p>

                    </div>
                </div>

            </div><!--.col-wrap-->

        </div><!--#col-left-->

    </div><!--#col-container-->

</div><!--#wwpp-wholesale-roles-page-->