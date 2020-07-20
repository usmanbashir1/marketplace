define(['jquery'], function ($) {
    "use strict";

    var SEMICOLONBTTBUTTON = SEMICOLONBTTBUTTON || {};

    SEMICOLONBTTBUTTON.widget = {
        init: function (options) {
            this.options = $.parseJSON(options);
            SEMICOLONBTTBUTTON.widget.btt_button();
        },

        btt_button: function(){
            var backToTop = $('.btt-button'),
                offset = this.options.offset,
                offsetOpacity = this.options.offsetOpacity,
                scrollTopDuration = this.options.scrollTopDuration;

            /** hide or show the "back to top" link */
            $(window).scroll(function(){
                ( $(this).scrollTop() > offset ) ? backToTop.addClass('cd-is-visible') : backToTop.removeClass('cd-is-visible cd-fade-out');
                if( $(this).scrollTop() > offsetOpacity ) {
                    backToTop.addClass('cd-fade-out');
                }
            });

            /** smooth scroll to top */
            backToTop.on('click', function(event){
                event.preventDefault();
                $('body,html').animate({
                        scrollTop: 0 ,
                    }, scrollTopDuration
                );
            });
        }
    };

    return SEMICOLONBTTBUTTON;
});
