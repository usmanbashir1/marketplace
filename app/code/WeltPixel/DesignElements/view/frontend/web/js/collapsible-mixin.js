define(['jquery'], function($) {
    'use strict';
    return function(collpsibleyWidget) {
        $.widget('mage.collapsible', $.mage.collapsible, {
            _scrollToTopIfVisible: function (elem) {
                var shouldScroll = window.Pearl && window.Pearl.scrollCollapsibleToTop;
                if (shouldScroll && !this._isElementOutOfViewport(elem)) {
                    elem.scrollIntoView();
                    window.scrollBy(0, -jQuery('.sticky-header').height() );
                }
            },
            _scrollToTopIfNotVisible: function () {
                var shouldScroll = window.Pearl && window.Pearl.scrollCollapsibleToTop;
                if (shouldScroll && this._isElementOutOfViewport()) {
                    this.header[0].scrollIntoView();
                    window.scrollBy(0, -jQuery('.sticky-header').height() );
                }
            },
        });
        return $.mage.collapsible;
    }
});
