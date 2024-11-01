(function ($) {
  $(document).ready(function () {
    $('body').on('click', 'div[data-for="ssl-atlas-notice"] button', function () {
      $.ajax({
        url: ajaxurl,
        data: {
          'action': 'ssl_atlas_event_on_notice_dismiss',
          'ssl_atlas_notice_nonce': ssl_atlas_notice_nonce.nonce
        },
        success: function (data) {
        },
        error: function (errorThrown) {
        }
      })
    })
  });
})(jQuery);
