<?php if (!defined('ABSPATH')) { exit; } ?>

<div id="wwp-license-upgrade">
    <!--Logo-->
    <p><img src="<?php echo WWP_IMAGES_URL ?>logo.png" alt="<?php _e('Wholesale Suite', 'woocommerce-wholesale-prices');?>" /></p>
    <!--Header Title-->
    <h1>WooCommerce Wholesale Prices License Activation</h1>
    <!--Header Description-->
    <p><?php _e('WooCommerce Wholesale Prices comes in two versions - the free version (with feature limitations) and the Premium add-on.', 'woocommerce-wholesale-prices') ?></p>
    <!--Comparison Button-->
    <a class="action-button feature-comparison"><?php _e('See feature comparisons','woocommerce-wholesale-prices'); ?></a>
    <!--License Information Box-->
    <div class="license-info-box">
        <div class="content-header">
            <div class="left-content-header">
                <span><?php _e('Your current license for WooCommerce Wholesale Prices:'); ?></span>
            </div>
            <div class="right-content-header">
                <a class="action-button upgrade-to-premium"><?php _e('Upgrade To Premium', 'woocommerce-wholesale-prices'); ?></a>
            </div>
        </div>
        <div class="content-body">
            <h2><?php _e('Free Version', 'woocommerce-wholesale-prices'); ?></h2>
            <p><?php _e('You are currently using WooCommerce Wholesale Prices Free  on a GPL license. The free version includes a heap of great extra features for your Wholesale Prices. The only requirement for the free version is that you have WooCommerce installed.', 'woocommerce-wholesale-prices'); ?></p>
            <table class="license-specs">
                <thead>
                    <tr>
                        <th><?php _e('Plan', 'woocommerce-wholesale-prices'); ?></th>
                        <th><?php _e('Version', 'woocommerce-wholesale-prices'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e('Free Version', 'woocommerce-wholesale-prices'); ?></td>
                        <td><?php echo WWP_Helper_Functions::get_wwp_version(); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>