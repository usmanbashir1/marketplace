define(['jquery', 'jquery_important'], function ($) {
    "use strict";

    var SEMICOLONSANIMATIONS = SEMICOLONSANIMATIONS || {};

    SEMICOLONSANIMATIONS.widget = {
        init: function () {
            SEMICOLONSANIMATIONS.widget.animations();
        },

        animations: function(){

            var $body = $('body'), $dataAnimateEl = $('[data-animate]');
            if( $dataAnimateEl.length > 0 ){
                if( $body.hasClass('device-lg') || $body.hasClass('device-md') || $body.hasClass('device-sm') || $body.hasClass('wp-device-xs') ){
                    $dataAnimateEl.each(function(){
                        var element = $(this),
                            animationDelay = element.attr('data-delay'),
                            animationDelayTime = 0;
                        if( element.parents('.fslider.no-thumbs-animate').length > 0 ) { return true; }

                        if( animationDelay ) { animationDelayTime = Number( animationDelay ) + 500; } else { animationDelayTime = 500; }

                        if( !element.hasClass('animated') ) {
                            element.addClass('not-animated');
                            var elementAnimation = element.attr('data-animate');
                            element.appear(function () {
                                setTimeout(function() {
                                    element.removeClass('not-animated').addClass( elementAnimation + ' animated');
                                }, animationDelayTime);
                            },{accX: 0, accY: -120},'easeInCubic');
                        }
                    });
                }
            }
        }
    };

    return SEMICOLONSANIMATIONS;
});
