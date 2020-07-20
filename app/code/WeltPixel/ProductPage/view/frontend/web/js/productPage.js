define([
	'jquery',
	'mage/mage',
	'mage/ie-class-fixer',
	'mage/gallery/gallery'
], function (jQuery) {
	"use strict";


	var productPage = {
		init: function () {
			jQuery('.togglet').bind('click', function() {
				setTimeout(function() {jQuery(window).trigger('resize')}, 300);
			});
		},

		load: function () {
			this.action();
			this.mageSticky();
			this.addMinHeight();
		},

		ajaxComplete: function () {
			this.mageSticky();
			this.adjustHeight();
		},

		resize: function () {
			this.action();
			this.adjustHeight();
			this.mageSticky();
		},

		adjustHeight: function() {
			// adjust left media height as well, in case it is smallers
			var media = jQuery('.product.media'),
				mediaGallery = jQuery('.product.media .gallery'),
				infoMain = jQuery('.product-info-main');

			if ( jQuery('body').hasClass('wp-device-xs') ||
				jQuery('body').hasClass('wp-device-s') ||
				jQuery('body').hasClass('wp-device-m')
			) {
				media.height('auto');
			} else {
				if ( ( mediaGallery.height() > 0 ) && ( mediaGallery.height() < infoMain.height())) {
					media.height(infoMain.height());
				}
			}
		},

		mageSticky: function () {
			var positionProductInfo = window.positionProductInfo;

			if (positionProductInfo == 1) {
				if (jQuery('body').hasClass('product-page-v2')) {
					jQuery('.product-info-main.product_v2.cart-summary').mage('sticky', {
						container: '.product-top-main.product_v2',
						spacingTop: 100
					});
				}
				if (jQuery('body').hasClass('product-page-v4')) {
					jQuery('.product-info-main.product_v4.cart-summary').mage('sticky', {
						container: '.product-top-main.product_v4',
						spacingTop: 25
					});
				}

			} else {
				if (jQuery('body').hasClass('product-page-v2') || jQuery('body').hasClass('product-page-v4')) {
					jQuery('.product-info-main.product_v2.cart-summary, .product-info-main.product_v4.cart-summary').addClass("no-sticky-product-page");
				}
			}

		},

		action: function () {
			var media = jQuery('.product.media.product_v2'),
				media_v4 = jQuery('.product.media.product_v4'),
				swipeOff = jQuery('.swipe_desktop_off #swipeOff');

			if(jQuery(window).width() > 768) {
				media.addClass('v2');
				media_v4.addClass('v4');
			} else {
				media.removeClass('v2');
				media_v4.removeClass('v4');
			}

			if(jQuery(window).width() > 1024) {
				swipeOff.addClass('active');
			} else {
				swipeOff.removeClass('active');
			}
		},

		addMinHeight: function() {
			var media_v4 = jQuery('.product.media.product_v4');
			if (media_v4.length) {
				var mediaContainer = media_v4.find('.gallery-placeholder'),
					selector = '.fotorama__loaded--img';
				this.waitForEl(function() {
					return jQuery(selector).length;
				}, function() {
					var prodImg = mediaContainer.find(selector).first();
					mediaContainer.css('min-height', prodImg.outerHeight());
				}, function() {
					// do nothing
				});
			}
		},

		waitForEl: function (isReady, success, error, count, interval) {
			if (count === undefined) count = 10;
			if (interval === undefined) interval = 200;

			if (isReady()) {
				success();
				return;
			}
			var that = this;
			setTimeout(function(){
				if (!count) {
					if (error !== undefined) {
						error();
					}
				} else {
					that.waitForEl(isReady, success, error, count -1, interval);
				}
			}, interval);
		},

		bindStickyScroll: function() {
			var productInfoMain = jQuery('.product-info-main'),
				productInfoMainLeft = parseInt(productInfoMain.offset().left),
				productInfoMainWidth = parseInt(productInfoMain.width()),
				bottomCorrection = '27px',
				leftCorrection = productInfoMainLeft + 'px',
				topOffset = parseInt(jQuery('header.page-header').height()),
				lastScrollTop = -50,
				fixedPos = 0;

			var that = this;
			if(that.isMobileCheck()){
				return true;
			}
			if(jQuery('body').hasClass('product-page-v4')) {
				productInfoMain.removeAttr('style');
				productInfoMainLeft = parseInt(productInfoMain.offset().left);
				productInfoMainWidth = parseInt(productInfoMain.width());
				leftCorrection = productInfoMainLeft + 'px';
				productInfoMain.removeClass('pp-fixed').addClass('pp-floating-v4').css({
					'left': productInfoMainLeft+'px',
					'width': productInfoMainWidth+'px'});
			}

			jQuery(window).on('scroll mousedown wheel DOMMouseScroll mousewheel keyup', function(e) {
				if(that.isMobileCheck()){
					return true;
				}
				var autoScroll = false;
				if(e.which == 1 && e.type == 'mousedown') {
					autoScroll = true;
				}

				var scrollTopPos = parseInt(jQuery(window).scrollTop()),
					scrollPos = parseInt(jQuery(window).scrollTop()) + parseInt(jQuery(window).outerHeight()),
					productInfoMainBottom = parseInt(productInfoMain.offset().top) + parseInt(productInfoMain.outerHeight()),
					topPos = scrollTopPos + parseInt(productInfoMain.outerHeight()) + 95,
					productInfoMainTop = parseInt(productInfoMain.offset().top) - parseInt(productInfoMain.css('top')),
					v2MediaBlock = jQuery('.product.media.product_v2.v2'),
					v4MediaBlock = jQuery('.product.media.product_v4.v4'),
					footerEl = v2MediaBlock.length > 0 ? v2MediaBlock : v4MediaBlock,
					footerOffset = footerEl.length ? parseInt(footerEl.offset().top) + parseInt(footerEl.outerHeight() - 20) : 0,
					galleryHeight = parseInt(jQuery('.gallery-placeholder').outerHeight()),
					scrollDir = 'dwn';

				jQuery('.gallery-placeholder').css('height', 'auto');
				if(scrollTopPos >  lastScrollTop){
					scrollDir = 'dwn';
				} else {
					scrollDir = 'up';
				}

				if(footerEl.hasClass('product_v4')) {
					footerEl.addClass('pp-floating-v4')
				}

				if(jQuery('body').hasClass('product-page-v2')) {
					if(scrollTopPos >= 0 && scrollTopPos <= topOffset){
						productInfoMain.removeClass('pp-fixed').removeAttr('style');
					} else if(scrollTopPos >= topOffset && productInfoMainBottom <= footerOffset) {
						productInfoMain.addClass('pp-fixed').css({
							'left': productInfoMainLeft+'px',
							'width': productInfoMainWidth+'px'});
					} else if(productInfoMainTop > topOffset && scrollDir==='up' && productInfoMainBottom <= footerOffset && topPos <= footerOffset) {
						productInfoMain.addClass('pp-fixed').removeAttr('style').css({
							'left': productInfoMainLeft+'px',
							'width': productInfoMainWidth+'px'});
					} else if(productInfoMainBottom >= footerOffset && topPos >= footerOffset && scrollTopPos >= fixedPos) {
						if(fixedPos == 0) fixedPos = scrollTopPos;
						if(autoScroll || scrollDir === 'dwn'){
							productInfoMain.removeClass('pp-fixed').removeAttr('style').css({
								'margin':'0 !important',
								'padding':'0 !important',
								'bottom': bottomCorrection,
								'right' : '0',
								'position' : 'absolute',
								'width': productInfoMainWidth+'px'});

						} else if(!autoScroll && scrollDir === 'up') {
							productInfoMain.addClass('pp-fixed').removeAttr('style').css({
								'left': productInfoMainLeft+'px',
								'width': productInfoMainWidth+'px'});
						}

					} else if(scrollTopPos <= fixedPos && scrollDir == 'up') {
						fixedPos = 0;
						productInfoMain.addClass('pp-fixed').removeAttr('style').css({
							'left': productInfoMainLeft+'px',
							'width': productInfoMainWidth+'px'});
					} else {
						productInfoMain.removeAttr('style').css({'left': productInfoMainLeft+'px', 'width': productInfoMainWidth+'px'});
					}
				}

				if(jQuery('body').hasClass('product-page-v4')) {
					if(scrollTopPos >= 0 && scrollTopPos <= topOffset){
						productInfoMain.removeClass('pp-fixed').addClass('pp-floating-v4').removeAttr('style').css({
							'left': productInfoMainLeft+'px',
							'width': productInfoMainWidth+'px'});
					} else if(scrollTopPos >= topOffset && productInfoMainBottom <= footerOffset ) {
						productInfoMain.addClass('pp-fixed').removeClass('pp-floating-v4').removeAttr('style').css({
							'width': productInfoMainWidth+'px'});
					} else if(productInfoMainTop > topOffset && scrollDir==='up' && productInfoMainBottom <= footerOffset && topPos <= footerOffset) {
						productInfoMain.addClass('pp-fixed').removeClass('pp-floating-v4').removeAttr('style').css({
							'width': productInfoMainWidth+'px'});
					} else if(productInfoMainBottom >= footerOffset && topPos >= footerOffset && scrollTopPos >= fixedPos) {
						if(fixedPos == 0) fixedPos = scrollTopPos;
						productInfoMain.addClass('pp-floating-v4').removeClass('pp-fixed').css({
							'margin':'0 !important',
							'padding':'0 !important',
							'bottom': bottomCorrection,
							'left': leftCorrection,
							'width': productInfoMainWidth+'px'});
					} else if(scrollTopPos <= fixedPos && scrollDir === 'up') {
						fixedPos = 0;
						productInfoMain.addClass('pp-fixed').removeClass('pp-floating-v4').removeAttr('style').css({'left': productInfoMainLeft+'px', 'width': productInfoMainWidth+'px'});
					} else {
						productInfoMain.addClass('pp-fixed').removeClass('pp-floating-v4').removeAttr('style').css({
							'width': productInfoMainWidth+'px'});
					}
				}

				lastScrollTop = scrollTopPos - 50;
			})
		},

		isMobileCheck: function() {
			var mobileClasses = ['wp-device-xxs','wp-device-xs','wp-device-s','wp-device-m'];
			for (var index = 0; index < mobileClasses.length; ++index) {
				if(jQuery('body').hasClass(mobileClasses[index])) {
					return true;
				}
			}
			return false;
		},

		scrollToUrlHash: function(url) {
			this.scrollTo(url.indexOf('#') !== -1 ? url.substring(url.indexOf('#') + 1) : null);
		},

		scrollTo: function(targetHash) {
			if (targetHash !== null) {
				var that = this;
				that.preLoadProductReviews(function() {
					var selector = jQuery('a[href^="#' + targetHash + '"]');
					that.waitForEl(function() {
						return jQuery(selector).length;
					}, function() {
						var target = jQuery('#' + targetHash);
						target.show();
						setTimeout(function() {
							jQuery('html, body').animate({
								scrollTop: target.offset().top - jQuery('header.page-header').outerHeight()
							}, {
								duration: 600,
								easing: 'easeOutExpo'
							}).promise().then(function() {
								if (!target.parent().hasClass('active')) {
									target.trigger('click');
								}
								selector.parent().addClass('active').attr({'aria-selected': true, 'aria-expanded': true});
								target.attr('aria-hidden', false);
							});
						}, 300);
					}, function() {
						// do nothing
					});
				});
			}
		},

		reviewIsLoaded: function() {
			var reviewsContainer = jQuery('#product-review-container').html();
			return !!reviewsContainer.length;
		},

		preLoadProductReviews: function(callback) {
			var that = this;
			setTimeout(function () {
				if (!that.reviewIsLoaded()) {
					jQuery("#tab-label-reviews-title").click().promise().then(function() {
						callback();
					});
				} else {
					callback();
				}
			}, 1000);
		}
	};

	return productPage;
});