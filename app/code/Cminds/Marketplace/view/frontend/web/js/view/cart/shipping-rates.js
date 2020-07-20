/**
 * Cminds Marketplace checkout cart shipping rates js component.
 *
 * @category Cminds
 * @package  Cminds_Marketplace
 * @author   Piotr Pierzak <piotrek.pierzak@gmail.com>
 */
define(
    [
        'jquery',
        'ko',
        'underscore',
        'uiComponent',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/quote',
        'Cminds_Marketplace/js/model/shipping-methods',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/cart/cache'
    ],
    function (
        $,
        ko,
        _,
        Component,
        shippingService,
        priceUtils,
        quote,
        shippingMethods,
        selectShippingMethodAction,
        checkoutData,
        cartCache
    ) {
        'use strict';
        var items  = quote.getItems();
        return Component.extend({
            defaults: {
                template: 'Cminds_Marketplace/cart/shipping-rates'
            },
            isVisible: ko.observable(!quote.isVirtual()),
            isLoading: shippingService.isLoading,
            shippingRates: shippingService.getShippingRates(),
            shippingRateGroups: ko.observableArray([]),
            selectedShippingMethod: ko.computed(function () {
                    return quote.shippingMethod() ?
                        quote.shippingMethod()['carrier_code'] + '_' + quote.shippingMethod()['method_code'] :
                        null;
                }
            ),
            supplierData: ko.observable(),
            supplierShippingRates: ko.observable({}),
            shippingMethodsEnabled: window.checkoutConfig.shippingMethodsEnabled,

            /**
             * @override
             */
            initObservable: function () {
                var self = this;
                var i;
                this._super();

                $(document).on('change', ".form-shipping-address input, [name='country_id'], [name='region']", function () {
                    var shippingAddress = quote.shippingAddress();

                    var countryId = $('[name="country_id"] > option:selected').val();
                    if (countryId) {
                        shippingAddress.countryId = countryId;
                    }
                    var region = $('[name="region"]').val();
                    if (region) {
                        shippingAddress.regionId = region;
                    }
                    var postcode = $('[name="postcode"]').val();
                    if (postcode) {
                        shippingAddress.postcode = postcode;
                    }

                    $.ajax({
                        url: window.checkoutConfig.baseUrl+'/marketplace/checkout/getproductsbyvendors',
                        type: "POST",
                        showLoader: true,
                        data: { json: JSON.stringify(
                            items
                        ),
                            shippingAddress: JSON.stringify(shippingAddress),
                            cid:countryId },
                        dataType: 'json',
                        success: function (data) {
                            if (data.length >= 1) {
                                var methods = [];
                                for (i = 0; i < data.length; ++i) {
                                    var methodstemp = data[i].methods[0];

                                    var shippingratesupplier = methodstemp.price;
                                    var shippingratesupplierfee = methodstemp.table_rate_fee;

                                    methods.push(
                                        {
                                            'id': methodstemp.id,
                                            'name': methodstemp.name,
                                            'supplierId': parseInt(methodstemp.supplier_id),
                                            'price': parseFloat(shippingratesupplier) + parseFloat(shippingratesupplierfee),
                                            'selected': ko.observable(methodstemp.selected ? methodstemp.id : 0)
                                        }
                                    );
                                }

                                self.supplierShippingRates(methods);
                                setTimeout( function() {
                                    $('.supplier_methods').prop("disabled", false);
                                    $('.supplier_methods').removeAttr("disabled");
                                    $('.supplier_methods').click();
                                }, 1000);
                            }
                        }
                    });
                });

                this.shippingRates.subscribe(function (rates) {
                    self.shippingRateGroups([]);
                    _.each(rates, function (rate) {
                        var carrierTitle = rate['carrier_title'];

                        if (self.shippingRateGroups.indexOf(carrierTitle) === -1) {
                            self.shippingRateGroups.push(carrierTitle);
                        }
                    });
                });

                self.supplierData(window.checkoutConfig.supplierData);

                var mappedShippingRates = $.map(
                    window.checkoutConfig.supplierShippingRates,
                    function(data) {
                        return {
                            'id': data.id,
                            'name': data.name,
                            'supplierId': parseInt(data.supplier_id),
                            'price': parseFloat(data.price),
                            'selected': ko.observable(data.selected ? data.id : 0)
                        };
                    }
                );
                self.supplierShippingRates(mappedShippingRates);

                return this;
            },

            /**
             * Get shipping rates for specific group based on title.
             * @returns Array
             */
            getRatesForGroup: function (shippingRateGroupTitle) {
                return _.filter(this.shippingRates(), function (rate) {
                    return shippingRateGroupTitle === rate['carrier_title'];
                });
            },

            /**
             * Format shipping price.
             * @returns {String}
             */
            getFormattedPrice: function (price) {
                return priceUtils.formatPrice(price, quote.getPriceFormat());
            },

            /**
             * Set shipping method.
             * @param {String} methodData
             * @returns bool
             */
            selectShippingMethod: function (methodData) {
                selectShippingMethodAction(methodData);
                checkoutData.setSelectedShippingRate(methodData['carrier_code'] + '_' + methodData['method_code']);

                return true;
            },

            getRatesForSupplier: function (supplierId) {
                return ko.utils.arrayFilter(this.supplierShippingRates(), function(rate) {
                    return rate.supplierId === supplierId;
                });
            },

            selectSupplierShippingMethod: function(rate) {
                $.ajax({
                    url: window.checkoutConfig.baseUrl+'/marketplace/checkout/setshippingprice',
                    type: 'POST',
                    data: {
                        price:  rate.price,
                        method_id: rate.id,
                        supplier_id: rate.supplierId
                    },
                    dataType: 'json',
                    success: function (data) {
                        cartCache.set('totals',null);
                        $('#s_method_supplier')
                            .trigger('click');
                        $('#s_method_supplier')
                            .next('label')
                            .html('Supplier '+priceUtils.formatPrice(data.price_total)+'');
                    }
                });

                return true;
            }
        });
    }
);
