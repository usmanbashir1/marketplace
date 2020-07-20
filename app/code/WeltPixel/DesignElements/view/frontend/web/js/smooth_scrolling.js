define(['jquery'], function ($) {
    "use strict";

    var SEMICOLONSMOOTHSCROLL = SEMICOLONSMOOTHSCROLL || {};

    SEMICOLONSMOOTHSCROLL.widget = {
        init: function () {
            SEMICOLONSMOOTHSCROLL.widget.linkScroll();
        },

        linkScroll: function(){
            $("a[data-scrollto]").click(function(){
                var element = $(this),
                    divScrollToAnchor = element.attr('data-scrollto'),
                    divScrollSpeed = element.attr('data-speed'),
                    divScrollOffset = element.attr('data-offset'),
                    divScrollEasing = element.attr('data-easing');

                if( !divScrollSpeed ) { divScrollSpeed = 750; }
                if( !divScrollOffset ) { divScrollOffset = 0; }
                if( !divScrollEasing ) { divScrollEasing = 'easeOutQuad'; }

                $('html,body').stop(true).animate({
                    'scrollTop': $( divScrollToAnchor ).offset().top - Number(divScrollOffset)
                }, Number(divScrollSpeed), divScrollEasing);

                return false;
            });
        }
    };

    return SEMICOLONSMOOTHSCROLL;
});
