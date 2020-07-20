define([
    'jquery'
    ], function ($) {
    "use strict";

    var wpProductLabels = {
        init: function(requestUrl) {
            if (!requestUrl.length) {
                return false;
            }

            $(document).on('wpproductlabels:init', {}, function() {
                var productIds = [];
                $("[data-wpproductlabel='1']").each(function() {
                    productIds.push($(this).attr('data-product-id'));
                });

                if (productIds.length) {
                    $.ajax({
                        url: requestUrl,
                        method: 'POST',
                        cache: false,
                        global: false,
                        data: {
                            product_ids: productIds
                        },
                        success: function (result) {
                            for (var i in result) {
                                var resultObj = result[i];
                                var prId = resultObj.productId;
                                var prHtml = resultObj.html;
                                $("[data-product-id='"+prId+"']").attr("data-wpproductlabel", 0).find('img').after(prHtml.imagePosition);
                            }
                        }
                    });
                }
            });
        },

        /** @Deprecated Not used anymore */
        adjustRightLabels: function() {
            var verticalThumbs = $('.fotorama__nav-wrap--vertical');
            var adjustmentElements = $('.wp-product-label.wp-product-label-top-right, .wp-product-label.wp-product-label-middle-right, .wp-product-label.wp-product-label-bottom-right');
            if (verticalThumbs.length) {
                var verticalThumbWidth = verticalThumbs.width() + 5;
                var adjustmentElements = $('.wp-product-label.wp-product-label-top-right, .wp-product-label.wp-product-label-middle-right, .wp-product-label.wp-product-label-bottom-right');
                adjustmentElements.css('margin-right', verticalThumbWidth + 'px')
            } else {
                adjustmentElements.css('margin-right', 0)
            }
        }
    };

    return wpProductLabels;
});