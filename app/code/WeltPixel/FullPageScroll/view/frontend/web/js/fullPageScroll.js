define(['jquery','full_page','scrolloverflow'], function ($) {
    "use strict";

    var FullPageScroll = {
        action: function (countBlocks) {

            var header = $('header'),
                footer = $('footer'),
                nav = $('.nav-sections'),
                breadcrumbs = $('.breadcrumbs'),
                headerOH = header.outerHeight(),
                footerOH = footer.outerHeight(),
                navOH = nav.outerHeight(),
                breadcrumbsOH = breadcrumbs.outerHeight(),
                body = $('body'),
                ww = $(window).width();

            header.addClass('fps');
            footer.css('margin-bottom', -footerOH);
            breadcrumbs.css('top', headerOH + navOH).addClass('fps active');
            body.addClass('fullpagescroll');

            if (ww > 767) {
                nav.css('top', headerOH).addClass('fps');
            } else {
                nav.css('top', 0).removeClass('fps');
            }

            $(window).resize(function () {
                if (ww > 767) {
                    nav.css('top', headerOH).addClass('fps');
                } else {
                    nav.css('top', 0).removeClass('fps');
                }
            });

            $('#fullpage').fullpage({
                verticalCentered: true,
                onLeave: function (index, nextIndex, direction) {
                    if (
                        index == 1 &&
                        nextIndex == 2 &&
                        direction == 'down'
                    ) {
                        if (ww > 767) {
                            $('.fps').removeClass('active');
                            header.css('margin-top', -headerOH);
                            nav.css('margin-top', -(headerOH + navOH));
                            breadcrumbs.css('margin-top', -(headerOH + navOH + breadcrumbsOH + 20));
                        } else {
                            $('.fps').removeClass('active');
                            header.css('margin-top', -headerOH);
                        }
                    }
                    if (index == 2 && nextIndex == 1 && direction == 'up') {
                        $('.fps').addClass('active');
                    }
                    if (index == countBlocks && nextIndex == (countBlocks + 1) && direction == 'down') {
                        $('footer').addClass('active');
                    }
                    if (index == (countBlocks + 1) && nextIndex == countBlocks && direction == 'up') {
                        $('footer').removeClass('active');
                    }
                }
            });
        }

    };
    return FullPageScroll;
});
