define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/url'
], function ($, modal, urlBuilder) {
    "use strict";

    var wpWishlistAddTo = {
        getWishlistsUrl: null,
        customerWishlists: null,
        stopEventPropagation: false,
        ajaxWishlist: false,
        wishlistElm: null
    };

    return {
        initMultiWishlist: function(params) {
            wpWishlistAddTo.getWishlistsUrl = params.getWishlistsUrl;
            wpWishlistAddTo.ajaxWishlist = params.ajaxWishlist;

            var that = this;
            var modalOptions = {
                type: 'popup',
                modalClass: 'wishlist-add-popup-modal',
                responsive: true,
                innerScroll: true,
                title: $.mage.__('Select Wishlist'),
                buttons: [
                    {
                        text: $.mage.__('Add'),
                        class: '',
                        click: function() {
                            var params = wpWishlistAddTo.wishlistElm.data('post');
                            params.data.wishlist_id = $('.wp-wishlist-selector').val();
                            wpWishlistAddTo.wishlistElm.data('post', params).trigger('click');
                        }
                    }
                ]
            };

            var wpWishlistPopup = modal(modalOptions, $('.multiple-wishlists-selector-container'));

            if (wpWishlistAddTo.customerWishlists == null) {
                $.ajax({
                    url: wpWishlistAddTo.getWishlistsUrl,
                    method: 'GET',
                    cache: false,
                    global: false,
                    data: {},
                    success: function (response) {
                        if (response.result) {
                            wpWishlistAddTo.customerWishlists = response.wishlists;
                            $.each(wpWishlistAddTo.customerWishlists, function (index, item) {
                                $('.wp-wishlist-selector').append($('<option>', {
                                    value: item.id,
                                    text : item.name
                                }));
                            });
                        } else {
                            wpWishlistAddTo.stopEventPropagation = true;
                        }
                    }
                });
            }

            $('body').on('click', 'a.action.towishlist, button.action.towishlist', function() {
                if (!wpWishlistAddTo.stopEventPropagation) {
                    var params = $(this).data('post');
                    if (params.data.wishlist_id) {
                        if (wpWishlistAddTo.ajaxWishlist) {
                            //if from cart move to wishlist, no ajax
                            if ($(this).hasClass('action-towishlist')) {
                                wpWishlistPopup.closeModal();
                                return true;
                            }
                            that._addtoAjax($(this));
                            params.data.wishlist_id = null;
                            $(this).data('post', params);
                            wpWishlistPopup.closeModal();
                            return false;
                        } else {
                            return true;
                        }
                    }

                    wpWishlistAddTo.wishlistElm = $(this);
                    wpWishlistPopup.openModal();
                    return false;
                }
            });
        },
        initAjaxWishlist :function(params) {
            wpWishlistAddTo.ajaxWishlist = params.ajaxWishlist;
            var that = this;
            if (wpWishlistAddTo.ajaxWishlist) {
                $('body').on('click', 'a.action.towishlist, button.action.towishlist', function() {
                    //if from cart move to wishlist, no ajax
                    if ($(this).hasClass('action-towishlist')) {
                        return true;
                    }
                    that._addtoAjax($(this));
                    return false;
                });
            }
        },
        showOverlay: function() {
            $('body').trigger('processStart');
        },
        removeOverlay: function() {
            $('body').trigger('processStop');
        },
        _addtoAjax: function(element) {
            var that = this;

            var formKey = $('input[name="form_key"]').val();
            var params = element.data('post');
            params.data.ajax = 1;

            if (formKey) {
                params.data['form_key'] = formKey;
            }

            that.showOverlay();

            $.ajax({
                url: params.action,
                method: 'POST',
                data: params.data,
                success: function (response) {
                    if(typeof response !='object' || response.result != true)
                    {
                        var url = urlBuilder.build("wishlist");
                        window.location.href = url;
                    }
                    that.removeOverlay();
                }
            });
        }
    };

});