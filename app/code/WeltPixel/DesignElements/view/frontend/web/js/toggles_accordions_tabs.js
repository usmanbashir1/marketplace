define(['jquery', 'jRespond', 'jquery-ui-modules/tabs'], function ($) {
    "use strict";

    var SEMICOLONTABS = SEMICOLONTABS || {};

    SEMICOLONTABS.widget = {
        init: function () {
            SEMICOLONTABS.widget.tabs();
            SEMICOLONTABS.widget.tabsJustify();
            SEMICOLONTABS.widget.toggles();
            SEMICOLONTABS.widget.accordions();
        },
        tabs: function () {
            var $tabs = $('.tabs:not(.customjs)');
            if ($tabs.length > 0) {
                $tabs.each(function () {
                    var element = $(this),
                            elementSpeed = element.attr('data-speed'),
                            tabActive = element.attr('data-active');

                    if (!elementSpeed) {
                        elementSpeed = 400;
                    }
                    if (!tabActive) {
                        tabActive = 0;
                    } else {
                        tabActive = tabActive - 1;
                    }

                    $.ui.tabs({
                        active: Number(tabActive),
                        show: {
                            effect: "fade",
                            duration: Number(elementSpeed)
                        }
                    }, element);
                });
            }
        },
        tabsJustify: function () {
            if (!$('body').hasClass('device-xxs') && !$('body').hasClass('device-xs')) {
                var $tabsJustify = $('.tabs.tabs-justify');
                if ($tabsJustify.length > 0) {
                    $tabsJustify.each(function () {
                        var element = $(this),
                                elementTabs = element.find('.tab-nav > li'),
                                elementTabsNo = elementTabs.length,
                                elementContainer = 0,
                                elementWidth = 0;

                        if (element.hasClass('tabs-bordered') || element.hasClass('tabs-bb')) {
                            elementContainer = element.find('.tab-nav').outerWidth();
                        } else {
                            if (element.find('tab-nav').hasClass('tab-nav2')) {
                                elementContainer = element.find('.tab-nav').outerWidth() - (elementTabsNo * 10);
                            } else {
                                elementContainer = element.find('.tab-nav').outerWidth() - 30;
                            }
                        }

                        elementWidth = Math.floor(elementContainer / elementTabsNo);
                        elementTabs.css({'width': elementWidth + 'px'});

                    });
                }
            } else {
                $('.tabs.tabs-justify').find('.tab-nav > li').css({'width': 'auto'});
            }
        },
        toggles: function () {
            var $toggle = $('.toggle');
            if ($toggle.length > 0) {
                $toggle.each(function () {
                    var element = $(this),
                            elementState = element.attr('data-state');

                    if (elementState != 'open') {
                        element.find('.togglec').hide();
                    } else {
                        element.find('.togglet').addClass("toggleta");
                    }

                    element.find('.togglet').click(function () {
                        $(this).toggleClass('toggleta').next('.togglec').slideToggle(300);
                        return true;
                    });
                });
            }
        },
        accordions: function () {
            var $accordionEl = $('.accordion');
            if ($accordionEl.length > 0) {
                $accordionEl.each(function () {
                    var element = $(this),
                            elementState = element.attr('data-state'),
                            accordionActive = element.attr('data-active');

                    if (!accordionActive) {
                        accordionActive = 0;
                    } else {
                        accordionActive = accordionActive - 1;
                    }

                    element.find('.acc_content').hide();

                    if (elementState != 'closed') {
                        element.find('.acctitle:eq(' + Number(accordionActive) + ')').addClass('acctitlec').next().show();
                    }

                    element.find('.acctitle').click(function () {
                        if ($(this).next().is(':hidden')) {
                            element.find('.acctitle').removeClass('acctitlec').next().slideUp("normal");
                            $(this).toggleClass('acctitlec').next().slideDown("normal");
                        }
                        return false;
                    });
                });
            }
        }

    };

    return SEMICOLONTABS;
});
