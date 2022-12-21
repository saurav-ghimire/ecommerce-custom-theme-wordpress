<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

$acfw_is_installed = WWP_Helper_Functions::is_acfwf_installed() ? ' acfw-installed' : '';
$acfw_is_active    = WWP_Helper_Functions::is_acfwf_active() ? ' acfw-active' : '';

$plugin_file = 'advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php';

?>

<div id="wwp-advanced-coupons-page" class="wwp-page wrap nosubsub">

  <div class="row-container company-logos">
    <img id="wws-logo" src="<?php echo WWP_IMAGES_URL; ?>/logo.png" alt="<?php _e('Wholesale Suite', 'woocommerce-wholesale-prices');?>" />
    <span>+</span>
    <img id="acfw-logo" src="<?php echo WWP_IMAGES_URL; ?>/acfwf-logo.png" alt="<?php _e('Advanced Coupons', 'woocommerce-wholesale-prices');?>" />
  </div>

  <div class="row-container">
    <div class="one-column">

      <div class="page-title"><?php _e('Get Better Results With Better WooCommerce Coupons', 'woocommerce-wholesale-prices');?></div>

      <p class="page-description"><?php _e('Want coupons that bring more customers to your store?', 'woocommerce-wholesale-prices');?></p>
      <p class="page-description"><?php _e('Advanced Coupons extends your coupon features so you can market you store better.', 'woocommerce-wholesale-prices');?></p>

    </div>
  </div>

  <div id="box-row" class="row-container">
    <div class="two-column">
      <img class="box-image" src="<?php echo WWP_IMAGES_URL; ?>/acfw-logo-150.png" alt="<?php _e('WooCommerce Wholesale Order Form', 'woocommerce-wholesale-prices');?>" />
    </div>

    <div class="two-column">
      <ul class="reasons-box">
        <li><?php _e('Trusted by over 15,000+ stores', 'woocommerce-wholesale-prices');?></li>
        <li><?php _e('5-star customer satisfaction rating', 'woocommerce-wholesale-prices');?></li>
        <li><?php _e('Creative new coupon options like BOGO Deals, URL coupons, add products, auto apply, shipping, + more!', 'woocommerce-wholesale-prices');?></li>
        <li><?php _e('Full store credits system', 'woocommerce-wholesale-prices');?></li>
        <li><?php _e('Extra features like gift cards and loyalty program available', 'woocommerce-wholesale-prices');?></li>
      </ul>
    </div>
  </div>

  <div id="step-1" class="row-container step-container<?php echo $acfw_is_installed ? ' grayout' : ''; ?>">
    <div class="two-column">
      <span class="step-number"><?php _e('1', 'woocommerce-wholesale-prices');?></span>
    </div>
    <div class="two-column">
      <h3><?php _e('Install Free Advanced Coupons Plugin', 'woocommerce-wholesale-prices');?></h3>
      <p><?php _e('Yep, it\'s totally free! Enjoy extended coupons for WooCommerce with the Advanced Coupons Free plugin. In minutes you can be creating amazing new coupons offers for your store that will explode your revenue.', 'woocommerce-wholesale-prices');?></p>

      <p><a class="<?php echo $acfw_is_installed ? "button-grey" : " button-green" ?>" href="<?php echo wp_nonce_url('update.php?action=install-plugin&plugin=advanced-coupons-for-woocommerce-free', 'install-plugin_advanced-coupons-for-woocommerce-free') ?>"><?php _e('Install Advanced Coupons Free Plugin', 'woocommerce-wholesale-prices');?></a></p>
    </div>
  </div>

  <div id="step-2" class="row-container step-container<?php echo !$acfw_is_installed || $acfw_is_active ? ' grayout' : ''; ?>">
    <div class="two-column">
      <span class="step-number"><?php _e('2', 'woocommerce-wholesale-prices');?></span>
    </div>
    <div class="two-column">
      <h3><?php _e('Create Better Coupons Offers', 'woocommerce-wholesale-prices');?></h3>
      <p><?php _e('Explore the differences between your old coupons and new extended coupons with Advanced Coupons. Set up a coupon and start earning more revenue on your next offer.', 'woocommerce-wholesale-prices');?></p>
      <p><a class="<?php echo !$acfw_is_installed || $acfw_is_active ? "button-grey" : " button-green" ?>" href="<?php echo wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;s', 'activate-plugin_' . $plugin_file); ?>"><?php _e('Activate Plugin', 'woocommerce-wholesale-prices');?></a></p>
    </div>
  </div>
</div>