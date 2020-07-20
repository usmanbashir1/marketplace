/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'ko',
        'jquery'
    ],
    function (
        ko,
        $
    ) {
        'use strict';
        var data = [];
        var viewModel = {
            vendorsProducts: ko.observableArray(data)
        };
        return {
            getPruductsByVendors: function(items, shippingAddress, cid) {
                $.ajax({
                    url: window.checkoutConfig.baseUrl+'/marketplace/checkout/getproductsbyvendors',
                    type: "POST",
                    showLoader: true,
                    data: { json: JSON.stringify(
                        items,
                    ),
                        shippingAddress: JSON.stringify(shippingAddress),
                        cid:cid },
                    dataType: 'json',
                    success: function (data) {
                        viewModel.vendorsProducts(data);
                    }
                });
            },

            getViewModel: function() {
                return viewModel.vendorsProducts;
            }
        }

    }
);
