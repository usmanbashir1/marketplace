define(['jquery', 'designelements_default', 'flexslider'], function ($, SEMICOLONDEFAULT) {
    "use strict";

    var SEMICOLONFLEXSLIDER = SEMICOLONFLEXSLIDER || {};

    SEMICOLONFLEXSLIDER.widget = {
        init: function () {
            SEMICOLONFLEXSLIDER.widget.loadFlexSlider();
            $('.fslider').addClass('preloader2');
        },

        loadFlexSlider: function(){
            var $flexSliderEl = $('.fslider:not(.customjs)').find('.flexslider');
            if( $flexSliderEl.length > 0 ){
                $flexSliderEl.each(function() {
                    var $flexsSlider = $(this),
                        flexsAnimation = $flexsSlider.parent('.fslider').attr('data-animation'),
                        flexsEasing = $flexsSlider.parent('.fslider').attr('data-easing'),
                        flexsDirection = $flexsSlider.parent('.fslider').attr('data-direction'),
                        flexsSlideshow = $flexsSlider.parent('.fslider').attr('data-slideshow'),
                        flexsPause = $flexsSlider.parent('.fslider').attr('data-pause'),
                        flexsSpeed = $flexsSlider.parent('.fslider').attr('data-speed'),
                        flexsVideo = $flexsSlider.parent('.fslider').attr('data-video'),
                        flexsPagi = $flexsSlider.parent('.fslider').attr('data-pagi'),
                        flexsArrows = $flexsSlider.parent('.fslider').attr('data-arrows'),
                        flexsThumbs = $flexsSlider.parent('.fslider').attr('data-thumbs'),
                        flexsHover = $flexsSlider.parent('.fslider').attr('data-hover'),
                        flexsSheight = $flexsSlider.parent('.fslider').attr('data-smooth-height'),
                        flexsUseCSS = false;

                    if( !flexsAnimation ) { flexsAnimation = 'slide'; }
                    if( !flexsEasing || flexsEasing == 'swing' ) {
                        flexsEasing = 'swing';
                        flexsUseCSS = true;
                    }
                    if( !flexsDirection ) { flexsDirection = 'horizontal'; }
                    if( !flexsSlideshow ) { flexsSlideshow = true; } else { flexsSlideshow = false; }
                    if( !flexsPause ) { flexsPause = 5000; }
                    if( !flexsSpeed ) { flexsSpeed = 600; }
                    if( !flexsVideo ) { flexsVideo = false; }
                    if( flexsSheight == 'false' ) { flexsSheight = false; }
                    if( flexsDirection == 'vertical' ) { flexsSheight = false; }
                    if( flexsPagi == 'false' ) { flexsPagi = false; } else { flexsPagi = true; }
                    if( flexsThumbs == 'true' ) { flexsPagi = 'thumbnails'; } else { flexsPagi = flexsPagi; }
                    if( flexsArrows == 'false' ) { flexsArrows = false; } else { flexsArrows = true; }
                    if( flexsHover == 'false' ) { flexsHover = false; } else { flexsHover = true; }

                    $flexsSlider.flexslider({
                        selector: ".slider-wrap > .slide",
                        animation: flexsAnimation,
                        easing: flexsEasing,
                        direction: flexsDirection,
                        slideshow: flexsSlideshow,
                        slideshowSpeed: Number(flexsPause),
                        animationSpeed: Number(flexsSpeed),
                        pauseOnHover: flexsHover,
                        video: flexsVideo,
                        controlNav: flexsPagi,
                        directionNav: flexsArrows,
                        smoothHeight: flexsSheight,
                        useCSS: flexsUseCSS,
                        start: function(slider){
                            SEMICOLONDEFAULT.widget.verticalMiddle();
                            slider.parent().removeClass('preloader2');
                            $('.flex-prev').html('<i class="icon-angle-left"></i>');
                            $('.flex-next').html('<i class="icon-angle-right"></i>');
                        }
                    });
                });
            }
        }
    };

    return SEMICOLONFLEXSLIDER;
});
