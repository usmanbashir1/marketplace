define(['jquery', 'jRespond'], function ($) {
    "use strict";

    var $fullScreenEl = $('.full-screen'), $window = $(window), $body = $('body'), $verticalMiddleEl = $('.vertical-middle'), $header = $('.page-header'), $body = $('body'), $slider = $('#slider');

    var SEMICOLONDEFAULT = SEMICOLONDEFAULT || {};

    SEMICOLONDEFAULT.widget = {
        init: function () {
            SEMICOLONDEFAULT.widget.responsiveClasses();
            SEMICOLONDEFAULT.widget.dataResponsiveClasses();
            SEMICOLONDEFAULT.widget.dataResponsiveHeights();
            SEMICOLONDEFAULT.widget.verticalMiddle();
            SEMICOLONDEFAULT.widget.fullScreen();
        },
        responsiveClasses: function(){
            var jRes = jRespond([
                    {
                            label: 'smallest',
                            enter: 0,
                            exit: 479
                    },{
                            label: 'handheld',
                            enter: 480,
                            exit: 767
                    },{
                            label: 'tablet',
                            enter: 768,
                            exit: 991
                    },{
                            label: 'laptop',
                            enter: 992,
                            exit: 1199
                    },{
                            label: 'desktop',
                            enter: 1200,
                            exit: 10000
                    }
            ]);
            jRes.addFunc([
                    {
                            breakpoint: 'desktop',
                            enter: function() { $body.addClass('device-lg'); },
                            exit: function() { $body.removeClass('device-lg'); }
                    },{
                            breakpoint: 'laptop',
                            enter: function() { $body.addClass('device-md'); },
                            exit: function() { $body.removeClass('device-md'); }
                    },{
                            breakpoint: 'tablet',
                            enter: function() { $body.addClass('device-sm'); },
                            exit: function() { $body.removeClass('device-sm'); }
                    },{
                            breakpoint: 'handheld',
                            enter: function() { $body.addClass('device-xs'); },
                            exit: function() { $body.removeClass('device-xs'); }
                    },{
                            breakpoint: 'smallest',
                            enter: function() { $body.addClass('device-xxs'); },
                            exit: function() { $body.removeClass('device-xxs'); }
                    }
            ]);
        },
        dataResponsiveClasses: function(){
            var $dataClassXxs = $('[data-class-xxs]'),
                $dataClassXs = $('[data-class-xs]'),
                $dataClassSm = $('[data-class-sm]'),
                $dataClassMd = $('[data-class-md]'),
                $dataClassLg = $('[data-class-lg]');

            if( $dataClassXxs.length > 0 ) {
                $dataClassXxs.each( function(){
                    var element = $(this),
                        elementClass = element.attr('data-class-xxs'),
                        elementClassDelete = element.attr('data-class-xs') + ' ' + element.attr('data-class-sm') + ' ' + element.attr('data-class-md') + ' ' + element.attr('data-class-lg');

                    if( $body.hasClass('device-xxs') ) {
                        element.removeClass( elementClassDelete );
                        element.addClass( elementClass );
                    }
                });
            }

            if( $dataClassXs.length > 0 ) {
                $dataClassXs.each( function(){
                    var element = $(this),
                        elementClass = element.attr('data-class-xs'),
                        elementClassDelete = element.attr('data-class-xxs') + ' ' + element.attr('data-class-sm') + ' ' + element.attr('data-class-md') + ' ' + element.attr('data-class-lg');

                    if( $body.hasClass('device-xs') ) {
                        element.removeClass( elementClassDelete );
                        element.addClass( elementClass );
                    }
                });
            }

            if( $dataClassSm.length > 0 ) {
                $dataClassSm.each( function(){
                    var element = $(this),
                        elementClass = element.attr('data-class-sm'),
                        elementClassDelete = element.attr('data-class-xxs') + ' ' + element.attr('data-class-xs') + ' ' + element.attr('data-class-md') + ' ' + element.attr('data-class-lg');

                    if( $body.hasClass('device-sm') ) {
                        element.removeClass( elementClassDelete );
                        element.addClass( elementClass );
                    }
                });
            }

            if( $dataClassMd.length > 0 ) {
                $dataClassMd.each( function(){
                    var element = $(this),
                        elementClass = element.attr('data-class-md'),
                        elementClassDelete = element.attr('data-class-xxs') + ' ' + element.attr('data-class-xs') + ' ' + element.attr('data-class-sm') + ' ' + element.attr('data-class-lg');

                    if( $body.hasClass('device-md') ) {
                        element.removeClass( elementClassDelete );
                        element.addClass( elementClass );
                    }
                });
            }

            if( $dataClassLg.length > 0 ) {
                $dataClassLg.each( function(){
                    var element = $(this),
                        elementClass = element.attr('data-class-lg'),
                        elementClassDelete = element.attr('data-class-xxs') + ' ' + element.attr('data-class-xs') + ' ' + element.attr('data-class-sm') + ' ' + element.attr('data-class-md');

                    if( $body.hasClass('device-lg') ) {
                        element.removeClass( elementClassDelete );
                        element.addClass( elementClass );
                    }
                });
            }
        },
        dataResponsiveHeights: function(){
            var $dataHeightXxs = $('[data-height-xxs]'),
                $dataHeightXs = $('[data-height-xs]'),
                $dataHeightSm = $('[data-height-sm]'),
                $dataHeightMd = $('[data-height-md]'),
                $dataHeightLg = $('[data-height-lg]');

            if( $dataHeightXxs.length > 0 ) {
                $dataHeightXxs.each( function(){
                    var element = $(this),
                        elementHeight = element.attr('data-height-xxs');

                    if( $body.hasClass('device-xxs') ) {
                        if( elementHeight != '' ) { element.css( 'height', elementHeight ); }
                    }
                });
            }

            if( $dataHeightXs.length > 0 ) {
                $dataHeightXs.each( function(){
                    var element = $(this),
                        elementHeight = element.attr('data-height-xs');

                    if( $body.hasClass('device-xs') ) {
                        if( elementHeight != '' ) { element.css( 'height', elementHeight ); }
                    }
                });
            }

            if( $dataHeightSm.length > 0 ) {
                $dataHeightSm.each( function(){
                    var element = $(this),
                        elementHeight = element.attr('data-height-sm');

                    if( $body.hasClass('device-sm') ) {
                        if( elementHeight != '' ) { element.css( 'height', elementHeight ); }
                    }
                });
            }

            if( $dataHeightMd.length > 0 ) {
                $dataHeightMd.each( function(){
                    var element = $(this),
                        elementHeight = element.attr('data-height-md');

                    if( $body.hasClass('device-md') ) {
                        if( elementHeight != '' ) { element.css( 'height', elementHeight ); }
                    }
                });
            }

            if( $dataHeightLg.length > 0 ) {
                $dataHeightLg.each( function(){
                    var element = $(this),
                        elementHeight = element.attr('data-height-lg');

                    if( $body.hasClass('device-lg') ) {
                        if( elementHeight != '' ) { element.css( 'height', elementHeight ); }
                    }
                });
            }
        },
        verticalMiddle: function() {
            if ($verticalMiddleEl.length > 0) {
                $verticalMiddleEl.each(function () {
                    var element = $(this),
                        verticalMiddleH = element.outerHeight(),
                        headerHeight = $header.outerHeight();

                    if (element.parents('#slider').length > 0 && !element.hasClass('ignore-header')) {
                        if ($header.hasClass('transparent-header') && ( $body.hasClass('device-lg') || $body.hasClass('device-md') )) {
                            verticalMiddleH = verticalMiddleH - 70;
                            if ($slider.next('#header').length > 0) {
                                verticalMiddleH = verticalMiddleH + headerHeight;
                            }
                        }
                    }

                    if ($body.hasClass('device-xs') || $body.hasClass('device-xxs')) {
                        if (element.parents('.full-screen').length && !element.parents('.force-full-screen').length) {
                            if (element.children('.col-padding').length > 0) {
                                element.css({
                                    position: 'relative',
                                    top: '0',
                                    width: 'auto',
                                    marginTop: '0'
                                }).addClass('clearfix');
                            } else {
                                element.css({
                                    position: 'relative',
                                    top: '0',
                                    width: 'auto',
                                    marginTop: '0',
                                    paddingTop: '60px',
                                    paddingBottom: '60px'
                                }).addClass('clearfix');
                            }
                        } else {
                            element.css({
                                position: 'absolute',
                                top: '50%',
                                width: '100%',
                                paddingTop: '0',
                                paddingBottom: '0',
                                marginTop: -(verticalMiddleH / 2) + 'px'
                            });
                        }
                    } else {
                        element.css({
                            position: 'absolute',
                            top: '50%',
                            width: '100%',
                            paddingTop: '0',
                            paddingBottom: '0',
                            marginTop: -(verticalMiddleH / 2) + 'px'
                        });
                    }
                });
            }
        },

        fullScreen: function(){
            if( $fullScreenEl.length > 0 ) {
                $fullScreenEl.each( function(){
                    var element = $(this),
                        scrHeight = window.innerHeight ? window.innerHeight : $window.height(),
                        negativeHeight = element.attr('data-negative-height');

                    if( element.attr('id') == 'slider' ) {
                        var sliderHeightOff = $slider.offset().top;
                        scrHeight = scrHeight - sliderHeightOff;
                        if( element.hasClass('slider-parallax') ) {
                            var transformVal = element.css('transform'),
                                transformX = transformVal.match(/-?[\d\.]+/g);
                            if( !transformX ) { var transformXvalue = 0; } else { var transformXvalue = transformX[5]; }
                            scrHeight = ( ( window.innerHeight ? window.innerHeight : $window.height() ) + Number( transformXvalue ) ) - sliderHeightOff;
                        }
                        if( $('#slider.with-header').next('#header:not(.transparent-header)').length > 0 && ( $body.hasClass('device-lg') || $body.hasClass('device-md') ) ) {
                            var headerHeightOff = $header.outerHeight();
                            scrHeight = scrHeight - headerHeightOff;
                        }
                    }
                    if( element.parents('.full-screen').length > 0 ) { scrHeight = element.parents('.full-screen').height(); }

                    if( $body.hasClass('device-xs') || $body.hasClass('device-xxs') ) {
                        if( !element.hasClass('force-full-screen') ){ scrHeight = 'auto'; }
                    }

                    if( negativeHeight ){ scrHeight = scrHeight - Number(negativeHeight); }

                    element.css('height', scrHeight);
                    if( element.attr('id') == 'slider' && !element.hasClass('canvas-slider-grid') ) { if( element.has('.swiper-slide') ) { element.find('.swiper-slide').css('height', scrHeight); } }
                });
            }
        }

    };

    SEMICOLONDEFAULT.isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (SEMICOLONDEFAULT.isMobile.Android() || SEMICOLONDEFAULT.isMobile.BlackBerry() || SEMICOLONDEFAULT.isMobile.iOS() || SEMICOLONDEFAULT.isMobile.Opera() || SEMICOLONDEFAULT.isMobile.Windows());
        }
    };

    return SEMICOLONDEFAULT;
});
