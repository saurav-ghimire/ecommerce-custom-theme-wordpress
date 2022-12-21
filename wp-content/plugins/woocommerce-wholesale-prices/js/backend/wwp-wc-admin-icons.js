jQuery(document).ready(function ($) {
  
  $(document).on("DOMNodeInserted", function (e) {
    if (
      !$(e.target).hasClass(
        "woocommerce-marketing-recommended-extensions-item"
      )
    )
      return;
      
    var link = $(e.target);
    
    var getImage = function (url) {
      if (url.includes("wholesalesuiteplugin.com"))
        return wwpAdminIcons.imgUrl + "wws-marketing-logo.png";

      if (url.includes("advanced-coupons-for-woocommerce-free"))
        return wwpAdminIcons.imgUrl + "acfw-marketing-logo.png";

      return false;
    };
    
    var image = getImage(link.prop("href"));  

    if (image) {

      link
      .find("svg")
      .replaceWith(
        '<img src="' + image + '" width="100%" height="auto" />'
      );

      link.find('.woocommerce-admin-marketing-product-icon').css({'background':'inherit','padding':'2px'});

    }  

  });

});
