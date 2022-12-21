jQuery(document).ready(function ($) {

  $("#wwpp_settings_variable_product_price_display_dummy").select2({minimumResultsForSearch: -1});

  $(
    ".wwp_settings_explicitly_use_product_regular_price_on_discount_calc_dummy"
  ).click(function () {
    var $tax_exempt = $(this);
    if ($tax_exempt.prop("checked") == true && _.size(vex.getAll()) === 0) {
      vex.dialog.open({
        className: "vex-theme-plain vex-wwpp-upsell",
        showCloseButton: true,
        unsafeMessage:
          '<img class="logo" src="' +
          price_settings_args.images_url +
          'logo.png" > ' +
          price_settings_args.use_regular_price.title +
          price_settings_args.use_regular_price.msg +
          '<div class="actions">' +
          '<a class="vex-btn" target="_blank" href="' +
          price_settings_args.use_regular_price.link +
          '" >' +
          price_settings_args.button_text +
          "</a>" +
          '<img class="5star" src="' +
          price_settings_args.images_url +
          '5star.png">' +
          "</div>",
        buttons: [],
        beforeClose: function () {
          $tax_exempt.prop("checked", false);
        },
      });
    }
  });

  $(".wwp_settings_variable_product_price_display_dummy").change(function () {
    var $price_display = $(this);
    if (
      ["minimum", "maximum"].includes($price_display.val()) &&
      _.size(vex.getAll()) === 0
    ) {
      vex.dialog.open({
        className: "vex-theme-plain vex-wwpp-upsell",
        showCloseButton: true,
        unsafeMessage:
          '<img class="logo" src="' +
          price_settings_args.images_url +
          'logo.png" > ' +
          price_settings_args.variable_product_price_display.title +
          price_settings_args.variable_product_price_display.msg +
          '<div class="actions">' +
          '<a class="vex-btn" target="_blank" href="' +
          price_settings_args.variable_product_price_display.link +
          '">' +
          price_settings_args.button_text +
          "</a>" +
          '<img class="5star" src="' +
          price_settings_args.images_url +
          '5star.png">' +
          "</div>",
        buttons: [],
        beforeClose: function () {
          $price_display.val("price-range");
          $price_display.select2({minimumResultsForSearch: -1});
        },
      });
    }
  });
});
