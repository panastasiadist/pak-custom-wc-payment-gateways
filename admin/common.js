window.pak_custom_wc_payment_gateways.api = function(action, data) {
  return new Promise((resolve, reject) => {
    window.jQuery
      .ajax({
        data: { action: `pak_custom_wc_payment_gateways_${action}`, data, nonce: window.pak_custom_wc_payment_gateways.nonce },
        dataType: 'json',
        type: 'post',
        url: window.pak_custom_wc_payment_gateways.url_ajax
      })
      .done((response) => resolve(response))
      .fail((error) => reject(error));
  });
};
