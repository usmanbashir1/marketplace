define([
    'jquery',
    'magnificPopup'
    ], function ($, magnificPopup) {
    "use strict";

    var quickview = {
        displayContent: function(prodUrl) {
            if (!prodUrl.length) {
                return false;
            }

            var windowWidth = $(window).width();
            var url = window.weltpixel_quickview.baseUrl + 'weltpixel_quickview/index/updatecart';
            var showMiniCart = parseInt(window.weltpixel_quickview.showMiniCart);
            var wpQv_closeOnBgClick = window.weltpixel_quickview.closeOnBgClick;
            var wpQv_mainClass = window.weltpixel_quickview.popupMainClass;

            if (windowWidth < 768) {
                wpQv_mainClass = '';
            }

            window.weltpixel_quickview.showMiniCartFlag = false;

            $.magnificPopup.open({
                items: {
                  src: prodUrl
                },
                type: 'iframe',
                closeOnBgClick: wpQv_closeOnBgClick,
                preloader: true,
                mainClass: wpQv_mainClass,
                tLoading: '',
                callbacks: {
                    open: function() {
                      $('.mfp-preloader').css('display', 'block').addClass('mfp-preloader-image');
                      $('.mfp-close').css('display', 'none');
                    },
                    beforeClose: function() {
                        if (window.weltpixel_quickview.showMiniCartFlag) {
                            $('[data-block="minicart"]').trigger('contentLoading');
                            $.ajax({
                                url: url,
                                method: "POST"
                            });
                        }
                    },
                    close: function() {
                        $('.mfp-preloader').css('display', 'none');
                    },
                    afterClose: function() {
                        /* Show only if product was added to cart and enabled from admin */
                        if (window.weltpixel_quickview.showMiniCartFlag && showMiniCart) {
                            $("html, body").animate({ scrollTop: 0 }, "slow");
                            setTimeout(function(){
                                if (!jQuery('.block-minicart').is(":visible")) {
                                    $('.action.showcart').trigger('click');
                                }
                            }, 1000);
                        }
                    }
                  }
            });
        }
    };

    window.quickview = quickview;
    return quickview;
});
