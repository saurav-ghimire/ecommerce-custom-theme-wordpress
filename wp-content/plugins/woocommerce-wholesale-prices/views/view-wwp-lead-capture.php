<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

$wwlc_is_installed = WWP_Helper_Functions::is_wwlc_installed() ? ' wwlc-installed' : '';
$wwlc_is_active    = WWP_Helper_Functions::is_wwlc_active() ? ' wwlc-active' : '';

$plugin_file = 'woocommerce-wholesale-lead-capture/woocommerce-wholesale-lead-capture.bootstrap.php';

?>

<div id="wwp-wholesale-lead-capture-page" class="wwp-page wrap nosubsub">

  <div class="row-container">
    <img id="wws-logo" src="<?php echo WWP_IMAGES_URL; ?>/logo.png" alt="<?php _e('Wholesale Suite', 'woocommerce-wholesale-prices');?>" />
  </div>

  <div class="row-container">
    <div class="one-column">

      <div class="page-title"><?php _e('Get More Wholesale Customers On Autopilot', 'woocommerce-wholesale-prices');?></div>

      <p class="page-description"><?php _e('Wholesale Lead Capture gives you a customizable wholesale-specific registration form,<br />
        wholesale user approvals system, email notifications, user upgrading, and more.', 'woocommerce-wholesale-prices');?></p>
    </div>
  </div>

  <div id="box-row" class="row-container">
    <div class="two-column">
      <img class="box-image" src="<?php echo WWP_IMAGES_URL; ?>/upgrade-page-wwlc-box.png" alt="<?php _e('WooCommerce Wholesale Lead Capture', 'woocommerce-wholesale-prices');?>" />
    </div>

    <div class="two-column">
      <ul class="reasons-box">
        <li><?php _e('Trusted by over 20,000+ stores', 'woocommerce-wholesale-prices');?></li>
        <li><?php _e('5-star customer satisfaction rating', 'woocommerce-wholesale-prices');?></li>
        <li><?php _e('Custom wholesale registration form', 'woocommerce-wholesale-prices');?></li>
        <li><?php _e('Wholesale approvals system', 'woocommerce-wholesale-prices');?></li>
        <li><?php _e('Customizable emails', 'woocommerce-wholesale-prices');?></li>
      </ul>
    </div>
  </div>

  <div id="step-1" class="row-container step-container<?php echo $wwlc_is_installed ? ' grayout' : ''; ?>">
    <div class="two-column">
      <span class="step-number"><?php _e('1', 'woocommerce-wholesale-prices');?></span>
    </div>
    <div class="two-column">
      <h3><?php _e('Purchase & Install Wholesale Lead Capture', 'woocommerce-wholesale-prices');?></h3>
      <p><?php _e('To grow your wholesale business you need an efficient way to onboard new wholesale customers without adding too
        much admin overhead. Wholesale Lead Capture gives you all the forms & approval system tools you need.', 'woocommerce-wholesale-prices');?></p>

      <p><a class="<?php echo $wwlc_is_installed ? "button-grey" : " button-green" ?>" href="https://wholesalesuiteplugin.com/woocommerce-wholesale-lead-capture/?utm_source=wwp&utm_medium=upsell&utm_campaign=wwlcpage" target="_blank"><?php _e('Get Wholesale Lead Capture', 'woocommerce-wholesale-prices');?></a></p>
    </div>
  </div>

  <div id="step-2" class="row-container step-container<?php echo !$wwlc_is_installed || $wwlc_is_active ? ' grayout' : ''; ?>">
    <div class="two-column">
      <span class="step-number"><?php _e('2', 'woocommerce-wholesale-prices');?></span>
    </div>
    <div class="two-column">
      <h3><?php _e('Configure Wholesale Lead Capture', 'woocommerce-wholesale-prices');?></h3>
      <p><?php _e('Wholesale Lead Capture comes mostly configured out of the box, but with some small tweaks you will have the
        perfect lead capturing form & approval process for your wholesale customers.', 'woocommerce-wholesale-prices');?></p>

      <p><a class="<?php echo !$wwlc_is_installed || $wwlc_is_active ? "button-grey" : " button-green" ?>" href="<?php echo wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;s', 'activate-plugin_' . $plugin_file); ?>"><?php _e('Activate Plugin', 'woocommerce-wholesale-prices');?></a></p>
    </div>
  </div>
</div>