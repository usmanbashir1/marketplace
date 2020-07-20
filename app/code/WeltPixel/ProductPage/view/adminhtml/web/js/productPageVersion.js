define([
	'WeltPixel_ProductPage/js/event.simulate',
	'jquery',
	'Magento_Ui/js/modal/alert'
], function (eventSimulate, jQuery, alert) {
	"use strict";

	return {
		action: function () {
			var wpp_vv = '#weltpixel_product_page_version_version',
				wpp_vv_O = jQuery(wpp_vv), // object
				alertDisplayed = false,
				store = jQuery('input#store_switcher').val(),
				param = 'ajax=1';

			jQuery.ajax({
				showLoader: true,
				url: window.weltpixel_productPageVersionAdminUrl,
				data: param,
				type: "GET",
				dataType: 'json'
			}).done(function (data) {
				wpp_vv_O.on('change', function () {
					var el = jQuery(this),
						elVal = el.val();

					if (store == 0 && !alertDisplayed) {
						alert({
							content: 'Please select a store view to be able to save / preload different setups for each version.'
						});
						alertDisplayed = true;
					}

					jQuery.each(data.items, function () {
						if (this.version_id == elVal && this.store_id == store) {

							jQuery.each(eval(this.values), function () {
								var el = this,
									elId = el.id,
									elValue = el.value,
									elm = jQuery('#' + elId),
									elmVal = elm.val(),
									elInherit = jQuery('#' + elId + '_inherit');

								if (elValue == null) {
									elValue = '';
								}
								if (elm.length && (elmVal != elValue)) {
									if (elInherit.prop('checked', true)) {
										// unchecked + add value
										elInherit.prop('checked', false);
										elm.prop('disabled', false).val(elValue).change();
										document.getElementById(elId).simulate('change');
									} else {
										// add value
										elm.val(elValue).change();
										document.getElementById(elId).simulate('change');
									}
								}
							});
						}
					});
				});
				wpp_vv_O.trigger('change');
			});
		}
	};
});