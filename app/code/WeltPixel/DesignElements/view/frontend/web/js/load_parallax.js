define(['jquery', 'designelements_default', 'stellar', 'jquery_important', 'jquery_transition'], function ($, SEMICOLONDEFAULT) {
    "use strict";

    (function() {
        var lastTime = 0;
        var vendors = ['ms', 'moz', 'webkit', 'o'];
        for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
            window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
            window.cancelAnimationFrame = window[vendors[x]+'CancelAnimationFrame']
                || window[vendors[x]+'CancelRequestAnimationFrame'];
        }

        if (!window.requestAnimationFrame)
            window.requestAnimationFrame = function(callback, element) {
                var currTime = new Date().getTime();
                var timeToCall = Math.max(0, 16 - (currTime - lastTime));
                var id = window.setTimeout(function() { callback(currTime + timeToCall); },
                    timeToCall);
                lastTime = currTime + timeToCall;
                return id;
            };

        if (!window.cancelAnimationFrame)
            window.cancelAnimationFrame = function(id) {
                clearTimeout(id);
            };
    }());



    function debounce(func, wait, immediate) {
        var timeout, args, context, timestamp, result;
        return function() {
            context = this;
            args = arguments;
            timestamp = new Date();
            var later = function() {
                var last = (new Date()) - timestamp;
                if (last < wait) {
                    timeout = setTimeout(later, wait - last);
                } else {
                    timeout = null;
                    if (!immediate) result = func.apply(context, args);
                }
            };
            var callNow = immediate && !timeout;
            if (!timeout) {
                timeout = setTimeout(later, wait);
            }
            if (callNow) result = func.apply(context, args);
            return result;
        };
    }


    var requesting = false;

    var killRequesting = debounce(function () {
        requesting = false;
    }, 100);


    function onScrollSliderParallax() {
        if (!requesting) {
            requesting = true;
            requestAnimationFrame(function(){
                SEMICOLONPARALLAX.widget.sliderParallax();
            });
        }
        killRequesting();
    }

    var SEMICOLONPARALLAX = SEMICOLONPARALLAX || {};
    var $body = $('body'), $window = $(window), $header = $('.page-header'), $pageTitle = $('#page-title'), $parallaxEl = $('.parallax'), $slider = $('#slider'), $sliderParallaxEl = $('.slider-parallax'), $parallaxPageTitleEl = $('.page-title-parallax'), $parallaxPortfolioEl = $('.portfolio-parallax').find('.portfolio-image');

    SEMICOLONPARALLAX.widget = {
        init: function () {
            window.addEventListener('scroll', onScrollSliderParallax, false);
            SEMICOLONPARALLAX.widget.parallax();
            if ($slider.length) {SEMICOLONPARALLAX.widget.sliderParallax();}
        },

        parallax: function(){
            if( $parallaxEl.length > 0 || $parallaxPageTitleEl.length > 0 || $parallaxPortfolioEl.length > 0 ) {
                if( !SEMICOLONDEFAULT.isMobile.any() ){
                    $.stellar({
                        horizontalScrolling: false,
                        verticalOffset: 150
                    });
                } else {
                    $parallaxEl.addClass('mobile-parallax');
                    $parallaxPageTitleEl.addClass('mobile-parallax');
                    $parallaxPortfolioEl.addClass('mobile-parallax');
                }
            }
        },

        sliderParallaxOffset: function(){
            var sliderParallaxOffsetTop = 0;
            var headerHeight = $header.outerHeight();
            if( $body.hasClass('side-header') || $header.hasClass('transparent-header') ) { headerHeight = 0; }
            if( $pageTitle.length > 0 ) {
                var pageTitleHeight = $pageTitle.outerHeight();
                sliderParallaxOffsetTop = pageTitleHeight + headerHeight;
            } else {
                sliderParallaxOffsetTop = headerHeight;
            }

            if( $slider.next('#header').length > 0 ) { sliderParallaxOffsetTop = 0; }

            return sliderParallaxOffsetTop;
        },


        sliderParallax: function(){
            if( $sliderParallaxEl.length > 0 ) {
                if( ( $body.hasClass('device-lg') || $body.hasClass('device-md') ) && !SEMICOLONDEFAULT.isMobile.any() ) {
                    var parallaxOffsetTop = SEMICOLONPARALLAX.widget.sliderParallaxOffset(),
                        parallaxElHeight = $sliderParallaxEl.outerHeight();

                    if( ( parallaxElHeight + parallaxOffsetTop + 50 ) > $window.scrollTop() ){
                        if ($window.scrollTop() > parallaxOffsetTop) {
                            var tranformAmount = (($window.scrollTop()-parallaxOffsetTop) / 1.5 ).toFixed(2);
                            var tranformAmount2 = (($window.scrollTop()-parallaxOffsetTop) / 7 ).toFixed(2);
                            $sliderParallaxEl.stop(true,true).transition({ y: tranformAmount },0);
                            $('.slider-parallax .slider-caption,.ei-title').stop(true,true).transition({ y: -tranformAmount2 },0);
                        } else {
                            $('.slider-parallax,.slider-parallax .slider-caption,.ei-title').transition({ y: 0 },0);
                        }
                    }
                    if (requesting) {
                        requestAnimationFrame(function(){
                            SEMICOLONPARALLAX.widget.sliderParallax();
                        });
                    }
                } else {
                    $('.slider-parallax,.slider-parallax .slider-caption,.ei-title').transition({ y: 0 },0);
                }
            }
        },
    };

    return SEMICOLONPARALLAX;
});
