define([
    'jquery',
    'mage/cookies'
], function ($) {
    'use strict';

    $.widget('weltpixel.newsletterpopup', {
        options: {
            opened: false,
            overlayDivId: 'wpn-lightbox-overlay',
            lightboxDivId: 'wpn-lightbox-content',
            closeDivId:    'wpn-lightbox-close-newsletter',
            closeOnOverlayAlso : false,
            cookieName: 'weltpixel_newsletter',
            pageCounter: 'weltpixel_pagecounter',
            cookieLifetime: 4,
            visitedPages: 1,
            secondsToDisplay: 1,
            isAjax: false,
            justCountPages: false,
            content: ''
        },

        _create: function () {

            if (!this.options.justCountPages) {
                this.options.content =  this.element[0];

                if (!this.getNewsletterCookie() && ( this.getPageCount() >= this.options.visitedPages )) {
                    setTimeout(this.showPopup.bind(this), 1000 * this.options.secondsToDisplay);
                } else if (!this.getNewsletterCookie() && ! this.options.isAjax) {
                    this.countPages();
                }

                var that = this;
                $('#weltpixel_newsletter').bind('submit', function() {
                    if ($(this).valid()) {
                        that.closeCallback();
                    }
                });
            } else {
                if (!this.getNewsletterCookie() && ( this.getPageCount() < this.options.visitedPages )) {
                    this.countPages();
                }
            }

        },

        closeCallback: function () {
            $.cookie(this.options.cookieName, 'true', { expires: parseInt(this.options.cookieLifetime) });
        },

        countPages: function() {
            $.cookie(this.options.pageCounter, this.getPageCount() +1, { expires: parseInt(this.options.cookieLifetime) });
        },

        getPageCount: function () {
            return $.cookie(this.options.pageCounter) - 0;
        },

        getNewsletterCookie: function () {
            return $.cookie(this.options.cookieName);
        },

        showPopup: function () {
            this.initPopup();
            this.openPopup();
        },

        initPopup: function () {
            $('<div/>', {id: this.options.overlayDivId}).appendTo('body');
            $('<div/>', {id: this.options.lightboxDivId}).appendTo('body');

            if (this.options.closeOnOverlayAlso) {
                try {
                    this.closeCallback();
                } catch(e) {}
            }

            var that = this;
            $(this._getElementIdSelector(this.options.overlayDivId)).bind('click', function () {
                that.closePopup();
            });
            $(window).resize(function(){
                that.adjustLightbox();
            });
        },

        openPopup: function() {
            //
            if (this.options.opened) {
                this.closePopup();
                this.options.opened = false;
            }

            $(this.options.content).prependTo($(this._getElementIdSelector(this.options.lightboxDivId)));
            $(this._getElementIdSelector(this.options.lightboxDivId)).append("<div id='"+this.options.closeDivId+"'>X</div>");

            var that = this;
            $(this._getElementIdSelector(this.options.closeDivId)).bind('click', function () {
                that.forceClose();
            });
            $(this.options.content).show();

            $(this._getElementIdSelector(this.options.overlayDivId)).show();
            $(this._getElementIdSelector(this.options.lightboxDivId)).show();

            this.options.opened = true;
            this.adjustLightbox();
        },

        adjustLightbox: function() {
            if (!this.options.opened) {
                return;
            }

            var lightboxHeight = $(this._getElementIdSelector(this.options.lightboxDivId)).outerHeight();
            var lightboxWidth = $(this._getElementIdSelector(this.options.lightboxDivId)).outerWidth();

            var leftPos = 0;
            if (document.body.offsetWidth > lightboxWidth) {
                leftPos += (document.body.offsetWidth - lightboxWidth)/2;
            }
            var topPos = window.pageYOffset;
            if (window.innerHeight > lightboxHeight) {
                topPos += (window.innerHeight - lightboxHeight)/2;
            }

            $(this._getElementIdSelector(this.options.lightboxDivId)).css({
                left: leftPos + 'px',
                top: topPos + 'px'
            });
        },

        closePopup: function () {
            $(this._getElementIdSelector(this.options.lightboxDivId)).html('');
            $(this._getElementIdSelector(this.options.overlayDivId)).hide();
            $(this._getElementIdSelector(this.options.lightboxDivId)).hide();
        },

        forceClose: function() {
            try {
                this.closeCallback();
            } catch(e) {}
            this.closePopup();
        },

        _getElementIdSelector: function(idName) {
            return '#' + idName;
        }
    });

    return $.weltpixel.newsletterpopup;
});
