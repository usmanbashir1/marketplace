define(['jquery','full_page','scrolloverflow'], function ($) {
    "use strict";

    var FullPageScroll = {
        action: function (countBlocks) {
            var pageHeader = $('.page-header'),
                header = $('header'),
                headerCnt = $('header .header.content'),
                headerPanel = $('header .panel.header'),
                headerH = '',
                search = $('header .block-search'),
                footer = $('footer'),
                nav = $('.nav-sections'),
                breadcrumbs = $('.breadcrumbs'),
                headerOH = header.outerHeight(), // this var is uses in default module js
                headerCntOH = headerCnt.outerHeight(),
                headerPanelOH = headerPanel.outerHeight(),
                searchH = '',
                footerOH = footer.outerHeight(),
                navOH = nav.outerHeight(),
                breadcrumbsOH = breadcrumbs.outerHeight(),
                body = $('body'),
                ww = $(window).width(),
                multiStore = $('.header-multistore .multistore-desktop'),
                multiStoreMobile = $('.header-multistore #multistore-mobile-switcher-language'),
                globalPromo = $('.header-global-promo'),
                round = 0,
                multiStoreOH = multiStore.outerHeight(),
                globalPromoOH = globalPromo.outerHeight(),
                multiStoreMobileOH = multiStoreMobile.outerHeight();

            header.addClass('fps active');
            body.addClass('fullpagescroll');

            $(window).resize(function () {
                ww = $(window).width();
                headerOH = header.outerHeight();
                headerCntOH = headerCnt.outerHeight() + 4;
                headerPanelOH = headerPanel.outerHeight();
                footerOH = footer.outerHeight();
                navOH = nav.outerHeight();
                breadcrumbsOH = breadcrumbs.outerHeight();
                multiStoreOH = multiStore.outerHeight();
                globalPromoOH = globalPromo.outerHeight();
                multiStoreMobileOH = multiStoreMobile.outerHeight();


                if (pageHeader.hasClass('page-header-v1')) {
                    headerH = headerPanelOH + headerCntOH;
                } else if (pageHeader.hasClass('page-header-v2')) {

                    round = 0;

                    if (ww > 768) {
                        headerH = headerCntOH - round;
                        searchH = search.outerHeight();
                    } else {
                        var values = (headerCntOH - round - multiStoreMobileOH);
                        if (values < 0) {
                            headerH = Math.abs(values) + headerCntOH;
                        } else {
                            if (multiStoreMobileOH > 0) {
                                headerH = multiStoreMobileOH;
                            } else {
                                headerH = headerCntOH - round;
                            }
                        }
                    }
                } else if (pageHeader.hasClass('page-header-v3')) {
                    round = 10;
                    headerH = headerPanelOH + headerCntOH - navOH + round;
                } else if (pageHeader.hasClass('page-header-v4')) {
                    round = 0; //8
                    headerH = headerPanelOH + headerCntOH - round;
                }

                headerH = headerH + multiStoreOH + globalPromoOH;

                footer.css('margin-bottom', -footerOH);
                breadcrumbs.css('top', headerH + navOH).addClass('fps active');

                if (window.stickyEnabled == 0) {
                    if (ww > 767 && header.hasClass('active')) {
                        if (pageHeader.hasClass('page-header-v3')) {
                            nav.css('top', 0).addClass('fps');
                        } else {
                            nav.css('top', headerH).addClass('fps');
                        }
                    } else {
                        nav.css('top', 0).removeClass('fps');
                    }
                }
            });

            $(document).ready(function () {
                setTimeout(function () {
                    $(window).trigger('resize');
                }, 1000);
            });

            $('#fullpage').fullpage({
                verticalCentered: true,
                onLeave: function (index, nextIndex, direction) {
                    window.onLeaveIndex = index;
                    window.onLeaveDirection = direction;
                    if (
                        index == 1 &&
                        nextIndex == 2 &&
                        direction == 'down'
                    ) {
                        if (window.stickyEnabled == 0) {
                            if (ww > 767) {
                                $('.fps').removeClass('active');
                                nav.css('top', 0);
                                if (headerH != '') {
                                    header.css('margin-top', -headerH);
                                    //nav.css('margin-top', -(headerH + navOH));
                                    breadcrumbs.css('margin-top', -(headerH + navOH + breadcrumbsOH + 20));
                                }
                                if (searchH != '') {
                                    search.css('margin-top', -searchH);
                                }
                            } else {
                                $('.fps').removeClass('active');
                                header.css('margin-top', -headerH);
                            }
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
                    $(window).trigger('resize');
                }
            });
        }
    };
    return FullPageScroll;
});
