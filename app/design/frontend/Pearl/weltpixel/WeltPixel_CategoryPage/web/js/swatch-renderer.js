define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {

        $.widget('mage.SwatchRenderer', widget, {
            _create: function () {
                this._super();
                var options = this.options,
                productData = this._determineProductData(),
                $main = productData.isInProductView ?
                        this.element.parents('.column.main') :
                        this.element.parents('.product-item-info');


                var lazyLoadActivated = $main.find('.product-image-photo').attr('data-original');
                if (lazyLoadActivated && !productData.isInProductView) {
                    options.mediaGalleryInitial = [{
                        'img': lazyLoadActivated
                    }];
                }
            }
        });

        return $.mage.SwatchRenderer;
    }
});