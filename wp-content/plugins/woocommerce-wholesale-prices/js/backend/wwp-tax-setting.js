jQuery(document).ready(function ($) {
  $('input[type="checkbox"]#wwp_settings_tax_exempt_wholesale_users').click(
    function () {
      var $tax_exempt = $(this);
      if ($tax_exempt.prop("checked") == true && _.size(vex.getAll()) === 0) {
        vex.dialog.open({
          className: "vex-theme-plain vex-wwpp-upsell",
          showCloseButton: true,
          unsafeMessage:
            '<img class="logo" src="' +
            tax_settings_args.images_url +
            'logo.png" > ' +
            tax_settings_args.tax_exemption.title +
            tax_settings_args.tax_exemption.msg +
            '<div class="actions">' +
            '<a class="vex-btn" target="_blank" href="' +
            tax_settings_args.tax_exemption.link +
            '" >' +
            tax_settings_args.button_text +
            "</a>" +
            '<img class="5star" src="' +
            tax_settings_args.images_url +
            '5star.png">' +
            "</div>",
          buttons: [],
          beforeClose: function () {
            $tax_exempt.prop("checked", false);
          },
        });
      }
    }
  );

  $(
    "select#wwp_settings_incl_excl_tax_on_wholesale_price, select#wwp_settings_wholesale_tax_display_cart"
  ).change(function () {
    var $tax_display = $(this);
    if (
      ["incl", "excl"].includes($tax_display.val()) &&
      _.size(vex.getAll()) === 0
    ) {
      vex.dialog.open({
        className: "vex-theme-plain vex-wwpp-upsell",
        showCloseButton: true,
        unsafeMessage:
          '<img class="logo" src="' +
          tax_settings_args.images_url +
          'logo.png" > ' +
          tax_settings_args.tax_display.title +
          tax_settings_args.tax_display.msg +
          '<div class="actions">' +
          '<a class="vex-btn" target="_blank" href="' +
          tax_settings_args.tax_display.link +
          '">' +
          tax_settings_args.button_text +
          "</a>" +
          '<img class="5star" src="' +
          tax_settings_args.images_url +
          '5star.png">' +
          "</div>",
        buttons: [],
        beforeClose: function () {
          $tax_display.val("");
          $tax_display.select2();
        },
      });
    }
  });

  $(
    "input#wwp_settings_override_price_suffix_regular_price, input#wwp_settings_override_price_suffix"
  ).on("click focus", function () {
    if (_.size(vex.getAll()) === 0) {
      var $suffix_overrides = $(this);
      $suffix_overrides.val("");
      vex.dialog.open({
        className: "vex-theme-plain vex-wwpp-upsell",
        showCloseButton: true,
        unsafeMessage:
          '<img class="logo" src="' +
          tax_settings_args.images_url +
          'logo.png" > ' +
          tax_settings_args.suffix_overrides.title +
          tax_settings_args.suffix_overrides.msg +
          '<div class="actions">' +
          '<a class="vex-btn" target="_blank" href="' +
          tax_settings_args.suffix_overrides.link +
          '">' +
          tax_settings_args.button_text +
          "</a>" +
          '<img class="5star" src="' +
          tax_settings_args.images_url +
          '5star.png">' +
          "</div>",
        buttons: [],
        beforeClose: function () {
          $suffix_overrides.val("");
        },
      });
    }
  });
});
