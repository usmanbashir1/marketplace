define(['jquery'], function ($) {
	Header = {

		headerLinks_1: $('.header.panel >.header.links'),
		headerRightMiniCart: $('.header_right > .minicart-wrapper'),

		headerLinks: function () {
			if (($('body').hasClass('mobile-nav')) || ($('body').hasClass('wp-device-l')) || $('body').hasClass('wp-device-xl') ) {
				if (!this.headerLinks_1.hasClass('moved-header')) {
					this.headerLinks_1.insertBefore(this.headerRightMiniCart);
					$('.header_right > .header.links .authorization-link a[data-post]').hide();
					$('.header_right .customer-menu .header.links .authorization-link a[data-post]').show();
					this.headerLinks_1.addClass('moved-header');
					this.headerLinks_1.removeClass('moved-header-mobile');
				}
			} else {
				if (($('body').hasClass('wp-device-m')) || $('body').hasClass('wp-device-s') || $('body').hasClass('wp-device-xs')) {
					if (!$('#store\\.links .header.links').length && !this.headerLinks_1.hasClass('moved-header-mobile')) {
						$('#store\\.links').append(this.headerLinks_1);
						this.headerLinks_1.removeClass('moved-header');
						this.headerLinks_1.addClass('moved-header-mobile');
					}
				}
			}
            $(".page-header-v2 .minisearch-v2").detach().insertAfter('.minicart-wrapper').show();
		},

		resizeActions: function () {
			this.headerLinks();
		},

		action: function () {
			this.resizeActions();
		}

	};
	return Header;
});