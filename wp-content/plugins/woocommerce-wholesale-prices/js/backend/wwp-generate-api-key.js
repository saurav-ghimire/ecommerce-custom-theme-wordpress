jQuery(document).ready(function ($) {
  var errorMessageDuration = "10000",
    successMessageDuration = "5000";

  $('body').find("a#wwp_auto_generate_api_key").on("click", function (e) {
    
    if(jQuery(e.target).css('display') == "none"){
      return
    }
    
    e.preventDefault();
    var $this = $(this);
    
    $this
      .attr("disabled", "disabled")
      .siblings(".spinner")
      .css("display", "inline-block")
      .css("visibility", "visible");

    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: {
        action: "woocommerce_update_api_key",
        security: api_keys.security_generate_key,
        key_id: 0,
        description: api_keys.description,
        user: api_keys.user_id,
        permissions: api_keys.permissions,
      },
      dataType: "json",
    })
      .done(function (data) {
        if (data.success === true) {
          toastr.success(api_keys.success_message, api_keys.i18n.success, {
            closeButton: true,
            showDuration: successMessageDuration,
          });

          // Hide Auto Generate button
          $("body").find("a#wwp_auto_generate_api_key").hide();

          // Make status valid
          $("body").find("span.status.valid").show();
          $("body").find("span.status.invalid").hide();

          // Update consumer key and secret
          $("body")
            .find("#wwp_woocommerce_api_consumer_key")
            .val(data.data.consumer_key);

          $("body")
            .find("#wwp_woocommerce_api_consumer_secret")
            .val(data.data.consumer_secret);

          // Update WWOF API Key
          $.ajax({
            url: api_keys.root + "wholesale/v1/api-keys",
            type: "POST",
            headers: {
              "X-WP-Nonce": api_keys.nonce,
            },
            data: {
              data: data.data,
            },
            dataType: "json",
          });
        } else {
          toastr.error(data.message, api_keys.i18n.fail, {
            closeButton: true,
            showDuration: errorMessageDuration,
          });
        }
      })
      .fail(function (jqxhr) {
        console.log(jqxhr);
      })
      .always(function () {
        $this
          .removeAttr("disabled")
          .siblings(".spinner")
          .css("display", "none");
      });
  });
});
