<?php if (!defined('ABSPATH')) {
    exit;
}

// Exit if accessed directly

if (isset($_GET['page']) && strpos($_GET['page'], 'upgrade-to-premium-page') !== false) {
    echo '<div id="wwp-upgrade-page" class="wwp-page wrap nosubsub">';
    echo "<table class='form-table'>";
}?>

<tr>
    <td>
        <div class="free-vs-premium box">
            <img src="<?php echo WWP_IMAGES_URL ?>wholesale-suite-activation-notice-logo.png" alt="<?php _e('WooCommerce Wholesale Prices Premium', 'woocommerce-wholesale-prices');?>"/>
            <div class="page-title"><?php _e('Free vs Premium', 'woocommerce-wholesale-prices');?></div>
            <p class="sub"><?php _e('If you are serious about growing your wholesale sales within your WooCommerce store then the Premium add-on to the free WooCommerce Wholesale Prices plugin that you are currently using can help you.', 'woocommerce-wholesale-prices');?></p>

            <table>
                <thead>
                    <tr>
                        <th><?php _e('Features', 'woocommerce-wholesale-prices');?></th>
                        <th><?php _e('Free Plugin', 'woocommerce-wholesale-prices');?></th>
                        <th><?php _e('Premium Add-on', 'woocommerce-wholesale-prices');?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e('Flexible wholesale pricing', 'woocommerce-wholesale-prices');?></td>
                        <td><span class="dashicons dashicons-no"></span><?php _e('Not available. Only basic wholesale pricing at the product level allowed.', 'woocommerce-wholesale-prices');?></td>
                        <td><span class="dashicons dashicons-yes-alt"></span><?php _e('Set wholesale pricing at the global (%), category (%) or the product level. Also includes quantity based pricing.', 'woocommerce-wholesale-prices');?></td>
                    </tr>
                    <tr>
                        <td><?php _e('Product visibility control', 'woocommerce-wholesale-prices');?></td>
                        <td><span class="dashicons dashicons-no"></span><?php _e('Not available', 'woocommerce-wholesale-prices');?></td>
                        <td><span class="dashicons dashicons-yes-alt"></span><?php _e('Make products "Wholesale Only", hide "Retail Only" products from wholesale customers, create variations that are "Wholesale Only".', 'woocommerce-wholesale-prices');?></td>
                    </tr>
                    <tr>
                        <td><?php _e('Multiple wholesale role levels', 'woocommerce-wholesale-prices');?></td>
                        <td><span class="dashicons dashicons-no"></span><?php _e('Not available. Only one wholesale role.', 'woocommerce-wholesale-prices');?></td>
                        <td><span class="dashicons dashicons-yes-alt"></span><?php _e('Add multiple wholesale role levels and use them to manage wholesale pricing, shipping mapping, payment mapping, tax exemption, order minimums and more.', 'woocommerce-wholesale-prices');?></td>
                    </tr>
                    <tr>
                        <td><?php _e('Advanced tax control', 'woocommerce-wholesale-prices');?></td>
                        <td><span class="dashicons dashicons-no"></span><?php _e('Not available', 'woocommerce-wholesale-prices');?></td>
                        <td><span class="dashicons dashicons-yes-alt"></span><?php _e('Fine grained control over price tax display for wholesale, tax exemptions per user role and more.', 'woocommerce-wholesale-prices');?></td>
                    </tr>
                    <tr>
                        <td><?php _e('Shipping method mapping', 'woocommerce-wholesale-prices');?></td>
                        <td><span class="dashicons dashicons-no"></span><?php _e('Not available', 'woocommerce-wholesale-prices');?></td>
                        <td><span class="dashicons dashicons-yes-alt"></span><?php _e('Manage which shipping methods wholesale customers can see and use compared to retail customers.', 'woocommerce-wholesale-prices');?></td>
                    </tr>
                    <tr>
                        <td><?php _e('Payment gateway mapping', 'woocommerce-wholesale-prices');?></td>
                        <td><span class="dashicons dashicons-no"></span><?php _e('Not available', 'woocommerce-wholesale-prices');?></td>
                        <td><span class="dashicons dashicons-yes-alt"></span><?php _e('Manage which payment gateways wholesale customers can see and use compared to retail customers.', 'woocommerce-wholesale-prices');?></td>
                    </tr>
                    <tr>
                        <td><?php _e('Set product and order minimums', 'woocommerce-wholesale-prices');?></td>
                        <td><span class="dashicons dashicons-no"></span><?php _e('Not available', 'woocommerce-wholesale-prices');?></td>
                        <td><span class="dashicons dashicons-yes-alt"></span><?php _e('Use product minimums and order minimums to ensure wholesale customers are meeting requirements.', 'woocommerce-wholesale-prices');?></td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="page-title"><?php _e('+100\'s of other premium wholesale features', 'woocommerce-wholesale-prices');?></div>
                            <a href="https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=wwp&utm_medium=upsell&utm_campaign=upgradepagewwppbutton" target="_blank" class="full-features">
                                <?php _e('See the full feature list', 'woocommerce-wholesale-prices');?>
                                <span class="dashicons dashicons-arrow-right-alt" style="margin-top: 1px"></span>
                            </a>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>
        <div class="wholesale-suite-bundle box">
            <div class="page-title"><?php _e('Wholesale Suite Bundle', 'woocommerce-wholesale-prices');?></div>
            <p class="sub"><?php _e('Everything you need to sell to wholesale customers in WooCommerce. <br/>The most complete wholesale solution for building wholesale sales into your existing WooCommerce driven store.', 'woocommerce-wholesale-prices');?></p>
            <br/><br/>
            <div class="products">
                <div class="page-title-h2"><?php _e('WooCommerce Wholesale Prices Premium', 'woocommerce-wholesale-prices');?></div>
                <div class="row">
                    <div class="column">
                        <p><?php _e('Easily add wholesale pricing to your products. Control product visibility. Satisfy your country\'s strictest tax requirements & control
                            pricing display. Force wholesalers to use certain shipping & payment gateways. Enforce order minimums and individual product minimums. And 100\'s
                            of other product and pricing related wholesale features.', 'woocommerce-wholesale-prices');?>
                        </p>
                        <p><a href="https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=wwp&utm_medium=upsell&utm_campaign=upgradepagewwpplearnmore" target="_blank"><?php _e('Learn more about Prices Premium', 'woocommerce-wholesale-prices');?></a></p>
                    </div>
                    <div class="column">
                        <img src="<?php echo WWP_IMAGES_URL ?>upgrade-page-wwpp-box.png" alt="<?php _e('WooCommerce Wholesale Prices Premium', 'woocommerce-wholesale-prices');?>"/>
                    </div>
                </div>
            </div >
            <div class="products">
                <div class="page-title-h2"><?php _e('WooCommerce Wholesale Order Form', 'woocommerce-wholesale-prices');?></div>
                <div class="row">
                    <div class="column">
                        <p><?php _e('Decrease frustration and increase order size with the most efficient one-page WooCommerce order form.
                            Your wholesale customers will love it. No page loading means less back & forth, full ajax enabled add to cart buttons,
                            responsive layout for on-the-go ordering and your whole product catalog available at your customer\'s fingertips.', 'woocommerce-wholesale-prices');?>
                        </p>
                        <p><a href="https://wholesalesuiteplugin.com/woocommerce-wholesale-order-form/?utm_source=wwp&utm_medium=upsell&utm_campaign=upgradepagewwoflearnmore" target="_blank"><?php _e('Learn more about Order Form', 'woocommerce-wholesale-prices');?></a></p>
                    </div>
                    <div class="column">
                        <img src="<?php echo WWP_IMAGES_URL ?>upgrade-page-wwof-box.png" alt="<?php _e('WooCommerce Wholesale Prices Premium', 'woocommerce-wholesale-prices');?>"/>
                    </div>
                </div>
            </div>
            <div class="products">
                <div class="page-title-h2"><?php _e('WooCommerce Wholesale Lead Capture', 'woocommerce-wholesale-prices');?></div>
                <div class="row">
                    <div class="column">
                        <p><?php _e('Take the pain out of manually recruiting & registering wholesale customers. Lead Capture will save you admin time and recruit wholesale customers
                            for your WooCommerce store on autopilot. Full registration form builder, automated email onboarding email sequence, full automated or manual approvals
                            system and much more.', 'woocommerce-wholesale-prices');?>
                        </p>
                        <p><a href="https://wholesalesuiteplugin.com/woocommerce-wholesale-lead-capture/?utm_source=wwp&utm_medium=upsell&utm_campaign=upgradepagewwlclearnmore" target="_blank"><?php _e('Learn more about Lead Capture', 'woocommerce-wholesale-prices');?></a></p>
                    </div>
                    <div class="column">
                                    <img src="<?php echo WWP_IMAGES_URL ?>upgrade-page-wwlc-box.png" alt="<?php _e('WooCommerce Wholesale Prices Premium', 'woocommerce-wholesale-prices');?>"/>
                    </div>
                </div>
            </div>
            <div class="products">
                <div class="page-title"><?php _e('The WooCommerce extensions to grow your wholesale business', 'woocommerce-wholesale-prices');?></div>
                <a href="https://wholesalesuiteplugin.com/bundle/?utm_source=wwp&utm_medium=upsell&utm_campaign=upgradepagebundlebutton" target="_blank" class="see-bundle">
                    <?php _e('See the full bundle now', 'woocommerce-wholesale-prices');?>
                    <span class="dashicons dashicons-arrow-right-alt" style="margin-top: 1px"></span>
                </a>
            </div>
        </div>
    </td>
</tr>
<?php
if (isset($_GET['page']) && strpos($_GET['page'], 'upgrade-to-premium-page') !== false) {
    echo '</table>';
    echo "<div>";
}?>