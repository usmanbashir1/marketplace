define(['jquery'], function($) {
    'use strict';

    return function(navigationMenu) {
        $.widget('mage.menu', navigationMenu.menu, {
            options: {
                delay: 300,
                mediaBreakpoint: '(max-width: ' + window.widthThreshold + 'px)'
            },
            /**
             * Toggle.
             */
            toggle: function () {
                if ($(window).width() <= window.widthThreshold || window.widthThreshold === undefined) {
                    var html = $('html');
                    if (html.hasClass('nav-open')) {
                        html.removeClass('nav-open');
                        setTimeout(function () {
                            html.removeClass('nav-before-open');
                        }, this.options.hideDelay);
                    } else {
                        html.addClass('nav-before-open');
                        setTimeout(function () {
                            html.addClass('nav-open');
                        }, this.options.showDelay);
                    }
                }
            }
        });
        return $.mage.menu;
    }
});