define(['jquery'], function ($) {
    "use strict";

    var SEMICOLONSTESTIMONIALSGRID = SEMICOLONSTESTIMONIALSGRID || {};

    SEMICOLONSTESTIMONIALSGRID.widget = {
        init: function () {
            SEMICOLONSTESTIMONIALSGRID.widget.testimonialsGrid();
        },

        testimonialsGrid: function(){
            var $testimonialsGridEl = $('.testimonials-grid'),
                $body = $('body');
            if( $testimonialsGridEl.length > 0 ) {
                if( $body.hasClass('device-sm') || $body.hasClass('device-md') || $body.hasClass('device-lg') ) {
                    var maxHeight = 0;
                    $testimonialsGridEl.each( function(){
                        $(this).find("li > .testimonial").each(function(){
                            if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
                        });
                        $(this).find("li").height(maxHeight);
                        maxHeight = 0;
                    });
                } else {
                    $testimonialsGridEl.find("li").css({ 'height': 'auto' });
                }
            }
        }
    };

    return SEMICOLONSTESTIMONIALSGRID;
});
