<div id="wwp-about-page" class="wwp-page wrap nosubsub">

  <div class="row-container">
    <img id="wws-logo" src="<?php echo WWP_IMAGES_URL; ?>logo.png" alt="<?php _e('Wholesale Suite', 'woocommerce-wholesale-prices');?>" />
  </div>

  <div class="row-container">
    <div class="one-column">
      <div class="page-title"><?php _e('About Wholesale Suite', 'woocommerce-wholesale-prices');?></div>
      <p class="page-description"><?php _e('Hello and welcome to Wholesale Suite, the most popular wholesale solution for WooCommerce.', 'woocommerce-wholesale-prices');?></p>
    </div>
  </div>

  <div class="row-container main">
    <div class="two-column">
      <h3><?php _e('About The Makers - Rymera Web Co', 'woocommerce-wholesale-prices');?></h3>
      <p><?php _e('Over the years we\'ve worked thousands of smart store owners that were frustrated by having separate workflows for wholesale between their online WooCommerce store and old-school offline methods.', 'woocommerce-wholesale-prices');?></p>
      <p><?php _e('That\'s why we decided to make Wholesale Suite - a state of the art solution focused suite of plugins that make it easy to sell to wholesale alongside your existing WooCommerce store.', 'woocommerce-wholesale-prices');?></p>
      <p><?php _e('Wholesale Suite is brought to you by the same team that\'s behind the best coupon feature plugin for WooCommerce, Advanced Coupons. We\'ve also been in the WordPress space for over a decade.', 'woocommerce-wholesale-prices');?></p>
      <p><?php _e('We\'re thrilled you\'re using our tool and invite you to try our other tools as well.', 'woocommerce-wholesale-prices');?></p>
    </div>

    <div class="two-column">
      <img id="wws-logo" src="<?php echo WWP_IMAGES_URL; ?>rymera-team.jpg" alt="<?php _e('Wholesale Suite', 'woocommerce-wholesale-prices');?>" />
    </div>
  </div>

  <div class="row-container two-columns">
      <div class="left-box">
        <div class="desc">
          <div class="page-title"><img id="acfw-marketing-logo" src="<?php echo WWP_IMAGES_URL; ?>acfw-marketing-logo.png" alt="<?php _e('Advanced Coupons', 'woocommerce-wholesale-prices');?>" />&nbsp;<?php _e('Advanced Coupons for WooCommerce (Free Plugin)', 'woocommerce-wholesale-prices');?></div>
          <p><?php _e('Extends your coupon features so you can market your store better. Adds cart conditions (coupon rules), buy one get one (BOGO) deals, url coupons, coupon categories and loads more. Install this free plugin.', 'woocommerce-wholesale-prices');?></p>
        </div>
        <div class="acfw-installed check-installed">
          <span><strong><?php _e('Status:', 'woocommerce-wholesale-prices')?></strong>&nbsp;<?php echo WWP_Helper_Functions::is_acfwf_installed() ? __('Installed', 'woocommerce-wholesale-prices') : __('Not installed', 'woocommerce-wholesale-prices'); ?></span>
          <?php if (!WWP_Helper_Functions::is_acfwf_installed()) {?>
            <a href="<?php echo wp_nonce_url('update.php?action=install-plugin&plugin=advanced-coupons-for-woocommerce-free', 'install-plugin_advanced-coupons-for-woocommerce-free') ?>" class="button-green"><?php _e('Install Plugin', 'woocommerce-wholesale-prices')?></a>
          <?php }?>
        </div>
      </div>
      <div class="right-box">
        <div class="desc">
          <div class="page-title"><img id="wws-marketing-logo" src="<?php echo WWP_IMAGES_URL; ?>wws-marketing-logo.png" alt="<?php _e('Wholesale Suite', 'woocommerce-wholesale-prices');?>" />&nbsp;<?php _e('Wholesale Suite Bundle', 'woocommerce-wholesale-prices');?></div>
          <p><?php _e('Selling to wholesale in WooCommerce requires a full strategy, that\'s why we made the Wholesale Suite bundle. Advanced wholesale pricing, tax, shipping mapping, payment gateway mapping, an optimized form and a wholesale registration.', 'woocommerce-wholesale-prices');?></p>
        </div>
        <div class="wwp-installed check-installed">
          <span><strong><?php _e('Status:', 'woocommerce-wholesale-prices')?></strong>&nbsp;<?php echo $bundle_installed ? __('Installed', 'woocommerce-wholesale-prices') : __('Not installed', 'woocommerce-wholesale-prices'); ?></span></span>
          <?php if (!$bundle_installed) {?>
            <a href="https://wholesalesuiteplugin.com/bundle/?utm_source=wwp&utm_medium=aboutpage&utm_campaign=aboutpagebundlebutton" target="_blank" class="button-green"><?php _e('Learn More', 'woocommerce-wholesale-prices');?></a>
          <?php }?>
        </div>
      </div>
  </div>

</div>
