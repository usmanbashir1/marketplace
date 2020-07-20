define(['jquery', 'domReady!'], function ($) {
    stickyHeader = {
        stickyHeader: function () {
            var config = {
                pageWrapper:        $('.page-wrapper'),
                headerSection:      $('.page-wrapper div.page-header'),
                headerContent:      $('.header.content'),
                headerLogo:         $('.header.content').find('.logo'),
                panelWrapper:       $('.panel.wrapper'),
                navSection:         $('.sections.nav-sections'),
                searchBlock:        $('.header.content').find('.block-search').not('.wpx-block-search'),
                headerMultiStore:   $('.header-multistore'),
                switcherMultiStore: $('.multistore-switcher'),
                globalPromo:        $('.page-wrapper .page-header').find('.header-global-promo'),
                switcherCurrency:   $('.panel.wrapper').find('.switcher-currency'),
                greetWelcome:       $('.panel.wrapper').find('.greet.welcome'),
                headerPlaceholder:  '<div class="header-placeholder"></div>',
                stickyMobile:       window.stickyMobileEnabled,
                design:             $('.section-items .section-item-title').first().css('display') == 'none' ? 'desktop' : 'mobile',
                headerElHeight:     0
            };

            /** abort if header-content or nav-sections was not found */
            if (config.headerContent.length == 0 || config.navSection.length == 0) {
                return;
            }

            var that = this;

            /** insert header-placeholder and move header elements */
            config.pageWrapper.prepend(config.headerPlaceholder);
            config.headerPlaceholder = $('.header-placeholder');

            if (that.getHeaderVersion(config.headerSection) != 'v3') {
                that.appendElements(config.headerSection, config.navSection, that, config);
            } else {
                that.appendElements(config.headerSection, null, that, config);
                config.headerContent.find('.compare.wrapper').after(config.navSection);
            }

            /** adjust header-placeholder height if global-promo-message is active */
            config.headerElHeight = parseInt(config.headerPlaceholder.outerHeight());
            var globalNotificationWrapper = config.globalPromo.find('.global-notification-wrapper');
            var checkHeight = setInterval(function () {
                if (globalNotificationWrapper.is(':visible') && globalNotificationWrapper.height()) {
                    if (config.headerPlaceholder.length) {
                        that.adjustHeaderPlaceholderHeight(that, config);
                        /** reset min-height on global message close */
                        globalNotificationWrapper.on('click', '.close-global-notification', function() {
                            that.adjustHeaderPlaceholderHeight(that, config);
                        });
                        clearInterval(checkHeight);
                    }
                }
            }, 500);

            /**
             * adjust the position of top-navigation
             * if its width is greater than the available room left in header-content
             */
            if (config.design != 'mobile') {
                that.adjustNavigation(that, config);
                that.fixFullWidthMenus(that, config);
            }

            $(window).on('scroll resize', function (e) {
                /** if design has changed force reset settings */
                var oldDesign = config.design;
                config.design = $('.section-items .section-item-title').first().css('display') == 'none' ? 'desktop' : 'mobile';
                if (oldDesign != config.design) {
                    that.resetSettings(that, config, oldDesign);
                }

                if (config.design == 'desktop') {
                    config.headerSection.removeClass('sticky-header-mobile');
                    switch (that.getHeaderVersion(config.headerSection))
                    {
                        case 'v1':
                            if (that.doSticky(that, config)) {
                                if (that.notStickyYet(config)) {
                                    that.moveElementsOnSticky(config.headerSection, config.navSection, 'out', config);
                                    config.searchBlock.after(config.navSection);
                                    that.showHideElements('hide', [
                                        config.globalPromo,
                                        config.switcherMultiStore,
                                        config.panelWrapper,
                                        config.headerMultiStore
                                    ]);
                                }
                            } else {
                                that.moveElementsOnSticky(config.headerSection, config.navSection, 'in', config);
                                config.headerSection.after(config.navSection);
                                that.showHideElements('show', [
                                    config.globalPromo,
                                    config.switcherMultiStore,
                                    config.panelWrapper,
                                    config.headerMultiStore
                                ]);
                            }
                            break;
                        case 'v2':
                            if (that.doSticky(that, config)) {
                                if (that.notStickyYet(config)) {
                                    that.moveElementsOnSticky(config.headerSection, config.navSection, 'out', config);
                                    if (!config.searchBlock.hasClass('minisearch-v2')) {
                                        config.searchBlock.after(config.navSection);
                                    } else {
                                        config.headerContent.find('.header_right').after(config.navSection);
                                    }
                                    that.showHideElements('hide', [
                                        config.globalPromo,
                                        config.switcherMultiStore,
                                        config.headerMultiStore
                                    ]);
                                }
                            } else {
                                that.moveElementsOnSticky(config.headerSection, config.navSection, 'in', config);
                                config.headerSection.after(config.navSection);
                                that.showHideElements('show', [
                                    config.globalPromo,
                                    config.switcherMultiStore,
                                    config.headerMultiStore
                                ]);
                            }
                            break;
                        case 'v3':
                            if (that.doSticky(that, config)) {
                                if (that.notStickyYet(config)) {
                                    that.moveElementsOnSticky(config.headerSection, null, 'out', config);
                                    that.showHideElements('hide', [
                                        config.globalPromo,
                                        config.switcherMultiStore,
                                        config.panelWrapper,
                                        config.headerMultiStore
                                    ]);
                                }
                            } else {
                                that.moveElementsOnSticky(config.headerSection, null, 'in', config);
                                that.showHideElements('show', [
                                    config.globalPromo,
                                    config.switcherMultiStore,
                                    config.panelWrapper,
                                    config.headerMultiStore
                                ]);
                            }
                            break;
                        case 'v4':
                            if (that.doSticky(that, config)) {
                                if (that.notStickyYet(config)) {
                                    that.moveElementsOnSticky(config.headerSection, config.navSection, 'out', config);
                                    config.navSection.addClass('sticky-header');
                                    that.showHideElements('hide', [
                                        config.globalPromo,
                                        config.greetWelcome,
                                        config.switcherMultiStore,
                                        config.headerMultiStore
                                    ]);
                                }
                            } else {
                                that.moveElementsOnSticky(config.headerSection, config.navSection, 'in', config);
                                config.navSection.removeClass('sticky-header');
                                that.showHideElements('show', [
                                    config.globalPromo,
                                    config.greetWelcome,
                                    config.switcherMultiStore,
                                    config.headerMultiStore
                                ]);
                            }
                            break;
                        default:
                            // nothing to do here
                            break;
                    }

                    /**
                     * adjust the position of top-navigation and the width of full-width sub-menu
                     */
                    if (config.design != 'mobile') {
                        that.adjustNavigation(that, config);
                        that.fixFullWidthMenus(that, config);
                    }


                } else {
                    config.headerSection.removeClass('sticky-header');
                    config.navSection.removeClass('sticky-header sticky-header-nav');

                    if (that.getHeaderVersion(config.headerSection) != 'v3') {
                        config.headerSection.after(config.navSection);
                    } else {
                        config.navSection.appendTo(config.headerContent);
                        if (that.getHeaderVersion(config.headerSection) != 'v2') {
                            config.switcherMultiStore.hide();
                        }
                    }

                    if (config.stickyMobile == 1) {
                        var headerVersion = that.getHeaderVersion(config.headerSection);
                        if (that.doSticky(that, config)) {
                            config.headerSection.addClass('sticky-header-mobile');
                            if (headerVersion != 'v2' && headerVersion != 'v4') {
                                that.showHideElements('hide', [
                                    config.panelWrapper
                                ]);
                            }

                            that.showHideElements('hide', [
                                config.globalPromo,
                                config.headerMultiStore
                            ]);
                        } else {
                            config.headerSection.removeClass('sticky-header-mobile');
                            if (headerVersion != 'v2' && headerVersion != 'v4') {
                                that.showHideElements('show', [
                                    config.panelWrapper
                                ]);
                            }

                            that.showHideElements('show', [
                                config.globalPromo,
                                config.headerMultiStore
                            ]);
                        }
                    }
                }
            });
        },
        adjustHeaderPlaceholderHeight: function (that, config, oldDesign) {
            if (oldDesign == 'mobile') {
                setTimeout(function() {
                    config.headerPlaceholder.css('min-height', '');
                    config.headerPlaceholder.css('min-height', parseInt(config.headerPlaceholder.outerHeight()) + 'px');
                }, 250);
            } else {
                config.headerPlaceholder.css('min-height', '');
                config.headerPlaceholder.css('min-height', parseInt(config.headerPlaceholder.outerHeight()) + 'px');
            }
        },
        resetSettings: function (that, config, oldDesign) {
            config.headerElHeight = 0;
            if (that.getHeaderVersion(config.headerSection) != 'v3') {
                that.appendElements(config.headerSection, config.navSection, that, config, oldDesign);
            } else {
                that.appendElements(config.headerSection, null, that, config, oldDesign);
                config.headerContent.find('.compare.wrapper').after(config.navSection);
            }
        },
        appendElements: function (a, b, that, config, oldDesign) {
            if (a) {
                a.appendTo(config.headerPlaceholder);
            }

            if (b) {
                b.appendTo(config.headerPlaceholder);
            }
            that.adjustHeaderPlaceholderHeight(that, config, oldDesign);
        },
        notStickyYet: function (config) {
            return !config.headerSection.hasClass('sticky-header');
        },
        doSticky: function (that, config) {
            var isFullPageScroll = $('.fullpagescroll').length;
            if (isFullPageScroll) {
                if (window.onLeaveIndex || window.onLeaveDirection) {
                    if (window.onLeaveDirection == 'down') {
                        return true;
                    } else {
                        if (window.onLeaveIndex == '2') {
                            return false;
                        } else {
                            return true;
                        }
                    }
                }

                return false;
            } else {
                if (that.getHeaderVersion(config.headerSection) == 'v4') {
                    var panelWrapperHeight = parseInt($('.panel.wrapper').height());
                    return $(window).scrollTop() > $('.panel.wrapper').position().top;
                }
                return $(window).scrollTop() > config.headerContent.position().top;
            }
        },
        moveElementsOnSticky: function (a, b, direction, config) {
            if (direction == 'out') {
                if (b) {
                    b.prependTo($('.page-wrapper')).before(config.headerPlaceholder);
                    b.addClass('sticky-header-nav');
                }
                if (a) {
                    a.prependTo($('.page-wrapper')).before(config.headerPlaceholder);
                    a.addClass('sticky-header');
                }
            } else {
                if (a) {
                    a.appendTo(config.headerPlaceholder);
                    a.removeClass('sticky-header');
                }
                if (b) {
                    b.appendTo(config.headerPlaceholder);
                    b.removeClass('sticky-header-nav');
                }
            }
        },
        showHideElements: function (action, els) {
            for (var i = 0; i < els.length; i++) {
                if (action == 'show') {
                    els[i].slideDown('fast');
                } else {
                    els[i].hide();
                }
            }
        },
        getHeaderVersion: function (headerSection) {
            if (headerSection.hasClass('page-header-v1')) {
                return 'v1';
            } else if (headerSection.hasClass('page-header-v2')) {
                return 'v2';
            } else if (headerSection.hasClass('page-header-v3')) {
                return 'v3';
            } else if (headerSection.hasClass('page-header-v4')) {
                return 'v4';
            }
        },
        adjustNavigation: function (that, config) {
            var navigationLis    = config.navSection.find('.navigation li.level0'),
                headerW          = config.headerContent.outerWidth(),
                logoW            = config.headerLogo.outerWidth(),
                headerMinicartW  = config.headerContent.find('.minicart-wrapper').outerWidth(),
                fullwidthWrapper = config.navSection.find('.fullwidth-wrapper'),
                searchBlockW    = 0;

            if (that.getHeaderVersion(config.headerSection) != 'v4') {
                /** get the real width of active search-block */
                config.searchBlock.each(function() {
                    if (!$(this).hasClass('wpx-block-search')) {
                        searchBlockW = $(this).outerWidth();
                    }
                });

                /** get the real width of the top-navigation container */
                var navigationW = 0, navCount = 0;
                navigationLis.each(function() {
                    navCount++;
                    navigationW += $(this).outerWidth();
                    if (navCount < navigationLis.length)
                        navigationW += 10; // left margin of nav li
                });

                /** do some math */
                var navRoom = headerW - logoW - headerMinicartW - searchBlockW - 80;
                var headerLinks = config.headerContent.find('.header.content .header.links');
                if (headerLinks.length && headerLinks.is(':visible')) {
                    navRoom -= config.headerContent.find('.header.content .header.links').outerWidth();
                }

                /** apply or remove adjustments */
                if (navigationW >= navRoom) {
                    config.navSection.addClass('too-wide');
                    config.headerContent.css('padding-bottom', '10px');
                    fullwidthWrapper.find('.columns-group').first().css({'margin-left': 'initial'});
                } else {
                    config.navSection.removeClass('too-wide');
                    config.headerContent.css('padding-bottom', '');
                    fullwidthWrapper.find('.columns-group').first().css({'margin-left': '-20px'});
                }
            }
            /** fix top position of sub-menus for header-v3 */
            if (that.getHeaderVersion(config.headerSection) == 'v3') {
                config.navSection.find('.level0.submenu').each(function() {
                    $(this).addClass('top-moved');
                });
            }
        },
        fixFullWidthMenus: function (that, config) {
            var pageWrapperW = config.pageWrapper.width(),
                headerContentW = config.headerContent.outerWidth(),
                leftPosition = parseInt(((pageWrapperW - headerContentW) / 2) * -1),
                navSectionLeft = parseInt(config.navSection.offset().left * -1),
                fullwidthWrapper = config.navSection.find('.fullwidth-wrapper'),
                headerVersion = stickyHeader.getHeaderVersion(config.headerSection);

            switch (headerVersion) {
                case 'v3':
                    fullwidthWrapper.css({'left': leftPosition + 'px'});
                    break;
                default:
                    if (that.notStickyYet(config)) {
                        fullwidthWrapper.css({'left': ''});
                    } else {
                        fullwidthWrapper.css({'left': navSectionLeft + 'px'});
                    }
                    break;
            }


        }
    };

    return stickyHeader;
});
