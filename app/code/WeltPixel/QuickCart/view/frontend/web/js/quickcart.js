define(['jquery', 'domReady'], function ($) {
    "use strict";

    var quickcart =
        {
            initialize: function() {
                var ua = window.navigator.userAgent;
                var msie = ua.indexOf("MSIE ");

                $('.quickcart-content-wrapper').on('click', '.qty-update', function () {
                    quickcart.updateQty($(this));
                });
                $('.quickcart-content-wrapper').on('click', '.action.delete', function () {
                    $('#btn-minicart-close').trigger('click');
                    $(".logo").removeAttr('style');
                });
                $('.quickcart-content-wrapper').on('click', '.action.close', function () {
                    $(".logo").removeAttr('style');
                });
                $('.showcart').on('click', function () {
                    $(".logo").attr('style', 'z-index: 0');
                    if(quickcart.checkSafariBrowser()){
                        $('.page-wrapper').css('overflow-x','visible');
                    }
                    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)){
                        $('.block-quickcart').addClass('quickCartIE');
                    }
                });

                $('.quickcart-content-wrapper').on('click', '.close', function () {
                    $('.page-wrapper').css('overflow-x','hidden');
                    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)){
                        $('.block-quickcart').removeClass('quickCartIE');
                    }
                });
                $('.quickcart-content-wrapper').on('click', '.product-item-photo, .product-item-name a', function () {
                    var href = $(this).attr('href');
                    window.location.href = href;
                });
                if (this.openMinicart()) {
                    var minicart = $('.minicart-wrapper');
                    var that = this;
                    minicart.on('contentLoading', function () {
                        minicart.on('contentUpdated', function () {
                            if (that.shouldOpenMinicart()) {
                                $(".logo").attr('style', 'z-index: 0');
                                if(quickcart.checkSafariBrowser()){
                                    $('.page-wrapper').css('overflow-x','visible');
                                }
                                $('.logo').focus();
                                minicart.find('[data-role="dropdownDialog"]').dropdownDialog("open");
                            }
                        });
                    });
                }
            },
            openMinicart: function() {
                if (window.openMinicart == 1) {
                    return true;
                } else {
                    return false;
                }
            },
            shouldOpenMinicart: function() {
                if (window.shouldOpenMinicart == 1) {
                    return true;
                } else {
                    return false;
                }
            },
            updateQty: function (el) {
                var qtyContainer = el.closest('.details-qty'),
                    currentQty = parseFloat(qtyContainer.find('input').val());

                if (el.hasClass('item-plus')) {
                    var newQty = currentQty + 1;
                    this.updateItemQty(el, newQty);
                } else {
                    if (currentQty > 1) {
                        var newQty = parseFloat(currentQty) - 1;
                        this.updateItemQty(el, newQty);
                    } else {
                        this.deleteCartItem(el);
                    }
                }
            },
            showSpinner: function (el) {
                el.closest('.details-qty').find('.spinner').show();
                this.updateUpdateCart(el);
            },
            updateItemQty: function (el, qty) {
                el.closest('.details-qty').find('input').val(qty).hide();
                this.showSpinner(el);
            },
            updateUpdateCart: function (el) {
                el.closest('.details-qty').find('button.update-cart-item').trigger('click');
            },
            deleteCartItem: function (el) {
                el.closest('.product-item-details').find('.product .action.delete').trigger('click');
            },
            checkSafariBrowser: function () {
                var is_safari =  navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1 &&  navigator.userAgent.indexOf('Android') == -1
                if (is_safari){
                    return true;
                }else{
                    return false;
                }
            }
        };

    return quickcart;
});
