define([
  'jquery'
], function ($) {
  'use strict';

  function processReviews(url, htmlContainer, loaderSelector) {
    loaderSelector.show();
    $.ajax({
      url: url,
      cache: true,
      dataType: 'html',
      showLoader: false
    }).done(function (data) {
      htmlContainer.append(data);
      htmlContainer.find('img.lazy').each(function(i, img) {
        jQuery(img).attr('src', jQuery(img).attr('data-original'));
      });
      loaderSelector.remove();
    });
  }

  return function (config) {
    var reviewsContainer = $(config.reiewsElementSelector),
        loaderSelector = $(config.loaderSelector),
        customerReviewsUrl = config.customerReviewsUrl;

    $(function () {
      processReviews(customerReviewsUrl, reviewsContainer, loaderSelector);
    });
  };
});
