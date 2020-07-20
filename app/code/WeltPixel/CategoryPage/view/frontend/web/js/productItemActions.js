define(['jquery','underscore'], function (jQuery,_) {
    "use strict";

    var CategoryPage = {

        displayAddToCart: function (actions) {
            var displayAddToCart = actions['displayAddToCart'],
                addtocart_position = '.addtocart_position_',
                productItem = '.products-grid .product-item',
                productImage = '.product_image',
                priceBox = '.price-box.price-final_price';

            if (displayAddToCart == 1 || displayAddToCart == 2) {
                jQuery(productItem).each(function () {
                    var el = jQuery(this),
                        btn = el.find(addtocart_position + displayAddToCart);

                    el.find(productImage).append(btn);
                });
            } else if (displayAddToCart == 3 || displayAddToCart == 4) {
                jQuery(productItem).each(function () {
                    var el = jQuery(this),
                        btn = el.find(addtocart_position + displayAddToCart);

                    el.find(priceBox).after(btn);
                });
            }
        },

        displayWishlist: function (actions) {
            var displayWishlist = actions['displayWishlist'],
                whishlist_position = '.whishlist_position_',
                productItem = '.products-grid .product-item',
                productImageAction = '.product_image .actions .actions-secondary';

            if (displayWishlist == 1 || displayWishlist == 2 || displayWishlist == 3 || displayWishlist == 4) {
                jQuery(productItem).each(function () {
                    var el = jQuery(this),
                        btn = el.find(whishlist_position + displayWishlist);

                    el.find(productImageAction).append(btn);
                });
            }
        },

        displayCompare: function (actions) {
            var displayCompare = actions['displayCompare'],
                compare_position = '.compare_position_',
                productItem = '.products-grid .product-item',
                productImageAction = '.product_image .actions .actions-secondary';

            if (displayCompare == 1 || displayCompare == 2 || displayCompare == 3 || displayCompare == 4) {
                jQuery(productItem).each(function () {
                    var el = jQuery(this),
                        btn = el.find(compare_position + displayCompare);

                    el.find(productImageAction).append(btn);
                });
            }
        },

        toCartWidth: function (actions) {
            var displayAddToCart = actions['displayAddToCart'],
                toCart = jQuery('.products-grid .product-item .product_image .addtocart_position_' + displayAddToCart + ' .tocart'),
                toCartPos = jQuery('.products-grid .product-item .product_image .addtocart_position_' + displayAddToCart),
                toCartWidth = toCart.outerWidth();
            toCartPos.css('width', toCartWidth);
        },

        buttonQuickView: function () {
            var productItem = '.products-grid .product-item',
                image = '.product-item-photo img',
                buttonQuickView_1 = '.weltpixel_quickview_button_v1',
                buttonQuickView_2 = '.weltpixel_quickview_button_v2',
                eTrue = false,
                addToCart = productItem + ' .product_image [class*="addtocart_position_"]';

            if (jQuery(addToCart)[0]) {
                eTrue = true;
            } else {
                eTrue = false;
            }

            jQuery(productItem).each(function () {
                var el = jQuery(this),
                    img = el.find(image).parent().outerHeight(),
                    quickViewH_1 = el.find(buttonQuickView_1).outerHeight(),
                    quickViewH_2 = el.find(buttonQuickView_2).outerHeight() + 10;

                if (eTrue) {
                    el.find(buttonQuickView_1).addClass('weltpixel_quickview_button_v2').removeClass('weltpixel_quickview_button_v1');
                    var quickViewH_2 = el.find(buttonQuickView_2).outerHeight() + 10;

                } else {

                    el.find(buttonQuickView_1).css('top', img - quickViewH_2 - 45);
                    el.find(buttonQuickView_2).css('top', img - quickViewH_2 - 10);
                }

            });
        },

        hoverShow: function (actions) {
            var displaySwatches = actions['displaySwatches'],
                prodItemDetails = jQuery('.product-item .product-item-details div[class*="swatch-opt-"]');

            if (displaySwatches == 2) {
                prodItemDetails.addClass('hoverShow');
            }
        },

        itemHover: function () {
            var productItem = '.products-grid .product-item',
                ItemHeight = jQuery(productItem).outerHeight();
            jQuery(productItem).mouseenter(function () {
                jQuery(this).css('height',ItemHeight);
            }).mouseleave(function () {
                jQuery(this).removeAttr('style');
            });
        },

        itemHeight: function (reset = false) {
            var productItem = '.products-grid .product-item',
                productItemHeightByCount = {}, productItemHeights = [];
            jQuery(productItem).each(function() {
                if (reset) {
                    jQuery(this).height('auto');
                }
                var height = jQuery(this).height();
                productItemHeights.push(height);
                if (jQuery(this).filter('[style*=height]').length) {
                    return false;
                }
            });
            productItemHeightByCount = _.countBy(productItemHeights);
            var finalHeight = _.max(Object.keys(productItemHeightByCount), function (o) {
                return productItemHeightByCount[o];
            });
            jQuery(productItem).height(finalHeight);
        },

        waitUntilExists: function (isReady, success, error, count, interval){
            var that = this;

            if (count === undefined) { count = 300; }
            if (interval === undefined) { interval = 20; }
            if (isReady()) { success(); return; }

            setTimeout(function(){
                if (!count) {
                    if (error !== undefined) {
                        error();
                    }
                } else {
                    that.waitUntilExists(isReady, success, error, count -1, interval);
                }
            }, interval);
        },

        actions: function () {
            var that = this;
            var actions = window.actions;
            this.displayAddToCart(actions);
            this.displayWishlist(actions);
            this.displayCompare(actions);
            this.toCartWidth(actions);
            this.hoverShow(actions);
            this.buttonQuickView();

            if (actions.displaySwatches === '1') {
                that.waitUntilExists(function() {
                    return jQuery('[class^="swatch-opt"]').length;
                }, function() {
                    setTimeout(that.itemHeight, 2000);
                }, function() {
                    // do nothing
                }, 100, 100);
            } else {
                that.itemHeight();
            }

            jQuery(window).resize(function() {
                that.toCartWidth(actions);
                that.hoverShow(actions);
                that.buttonQuickView();
                that.itemHeight(true);
                that.itemHover();
            });


        }

    };

    window.CategoryPage = CategoryPage;
    return CategoryPage;
});

