<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

$wwof_is_installed = WWP_Helper_Functions::is_wwof_installed() ? ' wwof-installed' : '';
$wwof_is_active    = WWP_Helper_Functions::is_wwof_active() ? ' wwof-active' : '';

$plugin_file = 'woocommerce-wholesale-order-form/woocommerce-wholesale-order-form.bootstrap.php';

?>

<div id="wwp-wholesale-order-form-page" class="wwp-page wrap nosubsub">

  <div class="row-container">
    <img id="wws-logo" src="<?php echo WWP_IMAGES_URL; ?>/logo.png" alt="<?php _e('Wholesale Suite', 'woocommerce-wholesale-prices');?>" />
  </div>

  <div class="row-container">
    <div class="one-column">

      <div class="page-title"><?php _e('Get More Wholesale Sales With An Optimized Ordering Form', 'woocommerce-wholesale-prices');?></div>

      <p class="page-description"><?php _e('Wholesale Order Form is proven to get more wholesale sales than your normal shop layout.<br/>Give your wholesale customers searchable access to your entire catalog on one page.', 'woocommerce-wholesale-prices');?></p>
    </div>
  </div>

  <div id="box-row" class="row-container">
    <div class="two-column">
      <img class="box-image" src="<?php echo WWP_IMAGES_URL; ?>/upgrade-page-wwof-box.png" alt="<?php _e('WooCommerce Wholesale Order Form', 'woocommerce-wholesale-prices');?>" />
    </div>

    <div class="two-column">
      <ul class="reasons-box">
        <li><?php _e('Trusted by over 20,000+ stores', 'woocommerce-wholesale-prices');?></li>
        <li><?php _e('5-star customer satisfaction rating', 'woocommerce-wholesale-prices');?></li>
        <li><?php _e('Search entire catalog on one page', 'woocommerce-wholesale-prices');?></li>
        <li><?php _e('Mobile & tablet friendly', 'woocommerce-wholesale-prices');?></li>
        <li><?php _e('Easy product table', 'woocommerce-wholesale-prices');?></li>
      </ul>
    </div>
  </div>

  <div id="step-1" class="row-container step-container<?php echo $wwof_is_installed ? ' grayout' : ''; ?>">
    <div class="two-column">
      <span class="step-number"><?php _e('1', 'woocommerce-wholesale-prices');?></span>
    </div>
    <div class="two-column">
      <h3><?php _e('Purchase & Install Wholesale Order Form', 'woocommerce-wholesale-prices');?></h3>
      <p><?php _e('Less "admin busy work" for you and your team and quicker ordering for your customers.
      Get the most efficient one-page WooCommerce order form – your wholesale customers will love it!', 'woocommerce-wholesale-prices');?></p>

      <p><a class="<?php echo $wwof_is_installed ? "button-grey" : " button-green" ?>" href="https://wholesalesuiteplugin.com/woocommerce-wholesale-order-form/?utm_source=wwp&utm_medium=upsell&utm_campaign=wwofpage" target="_blank"><?php _e('Get Wholesale Order Form', 'woocommerce-wholesale-prices');?></a></p>
    </div>
  </div>

  <div id="step-2" class="row-container step-container<?php echo !$wwof_is_installed || $wwof_is_active ? ' grayout' : ''; ?>">
    <div class="two-column">
      <span class="step-number"><?php _e('2', 'woocommerce-wholesale-prices');?></span>
    </div>
    <div class="two-column">
      <h3><?php _e('Configure Wholesale Order Form', 'woocommerce-wholesale-prices');?></h3>
      <p><?php _e('Wholesale Order Form is easy to set up and provides your customers with your entire catalog on one page.', 'woocommerce-wholesale-prices');?></p>
      <p><a class="<?php echo !$wwof_is_installed || $wwof_is_active ? "button-grey" : " button-green" ?>" href="<?php echo wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;s', 'activate-plugin_' . $plugin_file); ?>"><?php _e('Activate Plugin', 'woocommerce-wholesale-prices');?></a></p>
    </div>
  </div>
</div>