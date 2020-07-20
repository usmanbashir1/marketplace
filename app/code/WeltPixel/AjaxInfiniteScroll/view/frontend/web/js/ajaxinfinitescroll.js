define(['jquery', 'ias', 'ajaxinfinitescroll'],
function($) {
    "use strict";

    window.ajaxInfiniteScroll = {
        initInfiniteScroll: function ()
        {
            jQuery(function($) {
                var config = {
                    container:       '.products.wrapper .product-items',
                    item:            '.product-item',
                    pagination:      '.toolbar .pages, .toolbar .limiter',
                    next:            '.pages .action.next',
                    negativeMargin:  window.negativeMargin
                };

                // reset toolbar pager
                var pagertext = jQuery(".toolbar-amount");
                var toolbarCount = jQuery(".toolbar-amount .toolbar-number").length;
                if(toolbarCount > 2) {
                    jQuery(".toolbar-amount .toolbar-number:nth-child(1)").text('1');
                    jQuery(".toolbar-amount .toolbar-number:nth-child(2)").hide();
                    pagertext.html(pagertext.html().replace(/\-/g, ''));
                    pagertext.html(pagertext.html().replace(/\of/g, 'to'));
                }

                /** added to prevent jquery to add extra "_" parameter to link */
                $.ajaxSetup({ cache: true });

                /** add infinite-scroll class */
                $(config.container).closest('.column.main').addClass('infinite-scroll');
                /** load ias */
                var ias = $.ias(config);

                ias.getNextUrl = function(container) {
                    if (!container) {
                        container = ias.$container;
                    }
                    /** always take the last matching item + fix to be protocol relative */
                    var nexturl = $(ias.nextSelector, container).last().attr('href');
                    if(typeof nexturl !== "undefined") {
                        if (window.location.protocol == 'https:') {
                            nexturl = nexturl.replace('http:', window.location.protocol);
                        } else {
                            nexturl = nexturl.replace('https:', window.location.protocol);
                        }
                        nexturl = window.ajaxInfiniteScroll.removeQueryStringParameter('_', nexturl);
                    }

                    return nexturl;
                };

                /** added to prevent jquery to add extra "_" parameter to link */
                ias.on('load', function(event) {
                    //window.ajaxInfiniteScroll.scrollToLocation(); -- will be included in next release
                    var url = event.url;
                    event.ajaxOptions.cache = true;
                    event.url = window.ajaxInfiniteScroll.removeQueryStringParameter('_', event.url);
                });
                /** adds extra functionality to Infinite AJAX Scroll */
                ias.extension(new IASPagingExtension());
                ias.on('pageChange', function(pageNum, scrollOffset, url) {
                    window.page = pageNum;
                });

                ias.on('loaded', function(data, items) {
                    /** fix lazy load images */
                    window.ajaxInfiniteScroll.reloadImages(items);
                    window.ajaxInfiniteScroll.dataLayerUpdate(data);
                    window.ajaxInfiniteScroll.updateQuickviewPrevNext(data);
                });
                /** fix ajax add to cart */
                ias.on('rendered', function(items) {
                    window.ajaxInfiniteScroll.fixAddToCart();
                    /** re-init Pearl related elements */
                    window.ajaxInfiniteScroll.reloadQuickView();
                    window.ajaxInfiniteScroll.reloadCategoryPage();
                    $('body').trigger('contentUpdated');
                    /** update next/prev head links */
                    if (window.showCanonical == 1) {
                        window.ajaxInfiniteScroll.reloadCanonicalPrevNext();
                    }
                    $('.product-item-info a').each(function() {
                        if( typeof $(this).attr('data-item-page') === 'undefined') {
                            $(this).attr('data-item-page', window.page);
                        }
                    });
                    $(document).trigger("wpproductlabels:init");
                    $.mage.formKey();
                    $('li.product-item').trigger('contentUpdated');
                    if (window.isSlCustomPopupUsed && parseInt(window.isSlCustomPopupUsed)) {
                        $('li.product-item').find('.towishlist').each(function() {
                            $(this).removeAttr('data-post');
                        })
                    }
                });

                /** adds a text when there are no more pages to load */
                ias.extension(new IASNoneLeftExtension({
                    html: '<span class="ias-no-more ' + window.displaySwatchClass + '">' + window.textNoMore + '</span>'

                }));
                /** displays a customizable loader image when loading a new page */
                var loadingHtml  = '<div class="ias-spinner">';
                loadingHtml += '<img src="{src}"/>';
                loadingHtml += '<span>' + window.textLoadingMore + '</span>';
                loadingHtml += '</div>';
                ias.extension(new IASSpinnerExtension({
                    src: window.loadingImage,
                    html: loadingHtml
                }));

                /** adds "Load More" and "Load Previous" button */
                if (window.LoadMore > 0) {
                    ias.extension(new IASTriggerExtension({
                        text: window.textNext,
                        html: '<button class="button action ias-load-more" type="button"><span>{text}</span></button>',
                        textPrev: window.textPrevious,
                        htmlPrev: '<button class="button action ias-load-prev" type="button"><span>{text}</span></button>',
                        offset: window.LoadMore
                    }));
                } else {
                    ias.extension(new IASTriggerExtension({
                        textPrev: window.textPrevious,
                        htmlPrev: '<button class="button action ias-load-prev" type="button"><span>{text}</span></button>',
                        offset: 1000
                    }));
                }

                /** adds history support */
                ias.extension(new IASHistoryExtension({prev: '.previous'}));

            });
        },
        initNextPage: function ()
        {

            /** remove first toolbar block */
            if(jQuery('.toolbar-products .pages').length > 1) {
                jQuery('.toolbar-products .pages').first().remove();
            }

            jQuery(function($) {
                var config = {
                    container:  '.products.wrapper .product-items',
                    item:       '.product-item',
                    next:       '',
                    textNext:   'Load next items',
                    pageLink:   '.pages li.item a.page',
                    prevLink:   '.toolbar-products .action.previous',
                    nextLink:   '.toolbar-products .action.next'
                };
                /** added to prevent jquery to add extra "_" parameter to link */
                $.ajaxSetup({ cache: true });

                /** assign an unique id for each page selector */
                window.ajaxInfiniteScroll.addPageSelector(config.pageLink);
                /** add next-page class */
                $(config.container).closest('.column.main').addClass('next-page');

                var onClickElements = config.pageLink + ', ' + config.prevLink + ', ' + config.nextLink;
                var wpLayeredNavigationElement = $('.wp-filters-ajax');
                if(wpLayeredNavigationElement.length > 0) {
                    $(onClickElements).off('click')
                } else {
                    $(document).on('click', onClickElements, function(e) {
                        e.preventDefault();

                        /** assign an unique id for each page selector */
                        window.ajaxInfiniteScroll.addPageSelector(config.pageLink);

                        if ($(this).attr('id')) {
                            config.next = '#' + $(this).attr('id');
                        } else {
                            config.next = '#page-' + window.ajaxInfiniteScroll.getUrlParameter('p', $(this).attr('href'));
                        }
                        var ias = $.ias(config),
                            clicked = $(this);

                        /** displays a customizable loader image when loading a new page */
                        var loadingHtml  = '<div class="ias-overlay">';
                        loadingHtml += '<div class="ias-spinner" style="display: none">';
                        loadingHtml += '<img src="{src}"/>';
                        loadingHtml += '<span>' + window.textLoadingMore + '</span>';
                        loadingHtml += '</div>';
                        loadingHtml += '</div>';
                        ias.extension(new IASSpinnerExtension({
                            src: window.loadingImage,
                            html: loadingHtml
                        }));

                        /** triggered when a new page is about to be loaded from the server */
                        ias.on('load', function() {
                            /** scroll back to top and hide previous items */
                            window.ajaxInfiniteScroll.backToTop();
                            var sidebarEl = $('.sidebar');
                            //if(window.isLayeredNavigationEnabled === false || typeof window.isLayeredNavigationEnabled === 'undefined' || sidebarEl.length == 0) {
                            var spinnerLeft = parseInt($('.ias-spinner').outerWidth()) / 2;
                            $('.ias-spinner').css({'left': 'calc(50% - ' + spinnerLeft + 'px)'}).fadeIn();
                            //}
                            $(config.item).each(function() {
                                $(this).addClass('remove');
                            });
                            ias.destroy();
                            /** reload pager and limiter */
                            //if(window.isLayeredNavigationEnabled === false || typeof window.isLayeredNavigationEnabled === 'undefined' || sidebarEl.length == 0) {
                            window.ajaxInfiniteScroll.reloadPagination(clicked, config.pageLink);
                            //}
                            //ias.destroy();
                        });

                        ias.on('loaded', function(data, items) {
                            /** fix lazy load images */
                            window.ajaxInfiniteScroll.reloadImages(items);
                            window.ajaxInfiniteScroll.dataLayerUpdate(data);
                            window.ajaxInfiniteScroll.updateQuickviewPrevNext(data);
                        });

                        /** triggered after new items have rendered */
                        ias.on('rendered', function() {
                            /** fix ajax add to cart */
                            window.ajaxInfiniteScroll.fixAddToCart();
                            /** remove previous items and loading spinner */
                            $(config.item).each(function() {
                                if ($(this).hasClass('remove')) {
                                    $(this).remove();
                                }
                            });
                            $('.ias-overlay').remove();
                            /** re-init Pearl related elements */
                            window.ajaxInfiniteScroll.reloadQuickView();
                            window.ajaxInfiniteScroll.reloadCategoryPage();
                            /** remove first toolbar block */
                            if(jQuery('.toolbar-products .pages').length > 1) {
                                jQuery('.toolbar-products .pages').first().remove();
                            }
                            $(document).trigger("wpproductlabels:init");
                            $.mage.formKey();
                            $('li.product-item').trigger('contentUpdated');
                            if (window.isSlCustomPopupUsed && parseInt(window.isSlCustomPopupUsed)) {
                                $('li.product-item').find('.towishlist').each(function() {
                                    $(this).removeAttr('data-post');
                                })
                            }
                        });

                        ias.next();
                        ias.destroy();
                    });
                }

            });
        },
        resetIasPagination: function(page, url) {
            jQuery.ias().destroy();
            var newUrl = window.ajaxInfiniteScroll.replaceUrlPrameter(page, url);
            window.history.replaceState("","",newUrl);

            var config = {
                container:  '.products.wrapper .product-items',
                item:       '.product-item',
                next:       '',
                textNext:   'Load next items',
                pageLink:   '.pages li.item a.page',
                prevLink:   '.toolbar-products .action.previous',
                nextLink:   '.toolbar-products .action.next'
            };
            /** added to prevent jquery to add extra "_" parameter to link */
            $.ajaxSetup({ cache: true });
            /** assign an unique id for each page selector */
            window.ajaxInfiniteScroll.addPageSelector(config.pageLink);
            /** add next-page class */
            $(config.container).closest('.column.main').addClass('next-page');


        },
        fixAddToCart: function () {
            if (require.defined('catalogAddToCart') && $("form[data-role='tocart-form']").length) {
                $("form[data-role='tocart-form']").catalogAddToCart();
            }
        },
        reloadQuickView: function()
        {
            if (window.quickview) {
                var quickView = $('.weltpixel-quickview');
                if (quickView.length) {
                    $('.weltpixel-quickview').bind('click', function() {
                        var prodUrl = $(this).attr('data-quickview-url');
                        if (prodUrl.length) {
                            window.quickview.displayContent(prodUrl);
                        }
                    });
                }
            }
        },
        reloadCategoryPage: function()
        {
            if (window.CategoryPage) {
                window.CategoryPage.actions();
            }
        },
        reloadImages: function (items) {
            $(items).each(function() {
                /** reload images if lazy load is enabled */
                var productImg = $(this).find('.product-image-photo');
                if (productImg.hasClass('lazy')) {
                    productImg
                        .hide()
                        .attr('src', productImg.data('original'))
                        .css({'max-width' : '100%'})
                        .fadeIn('slow');
                }
            });
        },
        dataLayerUpdate: function(data) {
            var dataLayerObjects = data.match(/var dlObjects = (.*?);/);

            if ( ( dataLayerObjects != null ) && (typeof dataLayerObjects == 'object') && (dataLayerObjects.length == 2) ) {
                var dlObjects = JSON.parse(dataLayerObjects[1]);
                window.dataLayer = window.dataLayer || [];
                for (var i in dlObjects) {
                    window.dataLayer.push(dlObjects[i]);
                }
            }
        },
        updateQuickviewPrevNext: function(data) {
            var listingProductIds = data.match(/window.quickviewProductIds.*\[(.*)];/);
            if ( ( listingProductIds != null ) && (typeof listingProductIds == 'object') && (listingProductIds.length == 2) ) {
                var productIds = listingProductIds[1].split(",");
                for (var i =0; i<productIds.length; i++) {
                    window.quickviewProductIds.push(productIds[i].replace(/['"]/g, ''))
                }
            }
        },
        addPageSelector: function(pageLink)
        {
            $(pageLink).each(function() {
                $(this).attr('id', 'page-' + $(this).find('span:last-child').text());
            });
        },
        backToTop: function()
        {
            var stickyHeader = $('.sticky-header, .sticky-header-mobile'),
                stickyHeaderHeight = 0;
            if (stickyHeader.length) {
                stickyHeaderHeight = parseInt(stickyHeader.outerHeight());
            }

            $('html, body').animate({
                scrollTop: ($('.column.main').offset().top - stickyHeaderHeight)
            }, 'slow');
        },
        scrollToLocation: function()
        {
            if(window.location.hash) {
                var hash = location.hash.substr(1),
                    location = $('*[data-product-id="'+hash+'"]');
                if (location.length) {
                    $('html, body').animate({
                        scrollTop: (location.offset().top)
                    }, 'slow');

                    window.history.pushState("", document.title, window.location.pathname + window.location.search);
                }
            }

        },
        reloadPagination: function (clicked, pageLink)
        {
            var qeryStr = window.ajaxInfiniteScroll.getUrlParameter('q', window.ajaxInfiniteScroll.removeQueryStringParameter('p'));
            $.ajax({
                cache: true,
                url: window.ajaxReloadPaginationUrl,
                data: {
                    is_ajax: 1,
                    category_id: window.currentCategory,
                    q: qeryStr,
                    p: window.ajaxInfiniteScroll.getUrlParameter('p', clicked.attr('href')),
                    pager_url: window.ajaxInfiniteScroll.removeQueryStringParameter('p'),
                    limiter_url: window.ajaxInfiniteScroll.removeQueryStringParameter('product_list_limit'),
                },
                success: function(result){
                    $('.toolbar.toolbar-products').last().html(result.pager).trigger('contentUpdated');
                    var toolbar = $(result.toolbar);
                    $('.toolbar-amount').replaceWith(toolbar.find('.toolbar-amount')).trigger('contentUpdated');
                    /** window.ajaxInfiniteScroll.initNextPage(); */
                    window.ajaxInfiniteScroll.addPageSelector(pageLink);
                    /** history support */
                    var obj = {page: '', url: clicked.attr('href')};
                    history.pushState(obj, obj.page, obj.url);
                    /** update next/prev head links */
                    if (window.showCanonical == 1) {
                        window.ajaxInfiniteScroll.reloadCanonicalPrevNext();
                    }
                }
            });
        },
        reloadCanonicalPrevNext: function ()
        {
            $.ajax({
                cache: true,
                url: window.ajaxCanonicalRefresh,
                data: {
                    is_ajax: 1,
                    current_url: window.location.href
                },
                success: function(result){
                    /** update prev link */
                    if (result.prev) {
                        var currentPrev = $('link[rel="prev"]');
                        if (currentPrev.length) {
                            currentPrev.attr('href', result.prev);
                        } else {
                            $('<link rel="prev" href="' + result.prev + '">').insertAfter('link[rel="canonical"]');
                        }
                    } else {
                        $('link[rel="prev"]').remove();
                    }
                    /** update next link */
                    setTimeout(function() {
                        if (result.next && $('.ias-no-more').length == 0) {
                            var currentNext = $('link[rel="next"]');
                            if (currentNext.length) {
                                currentNext.attr('href', result.next);
                            } else {
                                $('<link rel="next" href="' + result.next + '">').insertAfter('link[rel="canonical"]');
                            }
                        } else {
                            $('link[rel="next"]').remove();
                        }
                    }, 1500);
                }
            });
        },
        removeQueryStringParameter: function (key, url)
        {
            if (!url) url = window.location.href;
            var hashParts = url.split('#'),
                regex = new RegExp("([?&])" + key + "=.*?(&|#|$)", "i");

            if (hashParts[0].match(regex)) {
                url = hashParts[0].replace(regex, '$1');
                url = url.replace(/([?&])$/, '');
                if (typeof hashParts[1] !== 'undefined' && hashParts[1] !== null)
                    url += '#' + hashParts[1];
            }

            return url;
        },
        getUrlParameter: function (sParam, url)
        {
            if (!url) url = window.location.href;
            var results = new RegExp('[\?&]' + sParam + '=([^&#]*)').exec(url);
            if(results == null) {
                return 0;
            } else {
                return results[1] || 0;
            }
        },
        replaceUrlPrameter: function(paramVal, url)
        {
            var newUrl = url.replace(/(p=).*?(&|$)/,'$1' + paramVal + '$2');
            return newUrl;
        }
    }
});
