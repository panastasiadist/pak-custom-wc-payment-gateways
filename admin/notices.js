jQuery(document).ready(function($) {
  $('.notice-pakcwcpg button').click(async function() {
    const id = $(this).attr('data-notice-id');
    const action = $(this).attr('data-notice-action-id');
    const { data } = await window.pak_custom_wc_payment_gateways.api(action, {});

    if (data && data.hasOwnProperty('url')) {
      window.open(data.url, '_blank');
    }

    if (data && data.hasOwnProperty('dismiss') && data.dismiss) {
      $(this).parents('.notice-pakcwcpg').remove();
    }
  });
});
