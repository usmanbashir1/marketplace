define(['jquery'], function($) {
    'use strict';
    return function(stickyWidget) {
        $.widget('mage.sticky', $.mage.sticky, {
            _getOptionValue: function (option) {
                var value = this._super();
                if (option == 'spacingTop') {
                    if (this.element.hasClass('cart-summary')) {
                        $('.sticky-header').each(function() {
                            value += jQuery(this).height();
                        });
                    }
                }
                return value;
            }
        });
        return $.mage.sticky;
    }
});