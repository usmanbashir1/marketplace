define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/confirm',
    ], function ($, modal, confirmation) {
    "use strict";

    var wpWishlistNameElm = null;
    var wpWishlistPopup = null;
    var wpErrorsContainer = null;
    var wpDeleteWishlistBind = false;
    var wpWishlistDeleteUrl = '';
    var wpWishlistModalPopup = $('#wishlist-popup-modal')

    return {
        init: function(params) {
            wpErrorsContainer = params.errorsContainer;
            wpWishlistDeleteUrl = params.deleteUrl;
        },
        editWishlist: function(params) {
            var wishlistId = params.wishlistId;
            wpWishlistNameElm = params.wishlistNameElm;
            window.szabi = wpWishlistNameElm;

            var editModalOptions = {
                type: 'popup',
                modalClass: 'wishlist-popup-modal',
                responsive: true,
                innerScroll: true,
                title: $.mage.__('Edit Wishlist'),
                buttons: []
            };

            wpWishlistPopup = modal(editModalOptions, wpWishlistModalPopup);
            wpErrorsContainer.html('');
            wpWishlistPopup.openModal();
            $('#wishlist-name').val(wpWishlistNameElm.html());
            $('#wishlist-id').val(wishlistId);
            $('#deletewishlist').show();
            if (!wpDeleteWishlistBind) {
                $('#deletewishlist').bind('click', function () {

                    confirmation({
                        title: $.mage.__('Delete Wishlist'),
                        content: $.mage.__('Are you sure you want to delete'),
                        actions: {
                            confirm: function(){
                                $.ajax({
                                    url: wpWishlistDeleteUrl,
                                    method: 'POST',
                                    cache: false,
                                    global: false,
                                    data: {wishlistId: $('#wishlist-id').val() },
                                    success: function (response) {
                                        if (response.result) {
                                            wpWishlistPopup.closeModal();
                                            wpWishlistNameElm.parentsUntil('.multiple-wishlist-element').remove();
                                        } else {
                                            wpWishlistModalPopup.find('.wp-errors').html(response.msg).show();
                                        }
                                    }
                                });
                            }
                        }
                    });
                });
                wpDeleteWishlistBind = true;
            }
        },
        addWishlist: function() {
            var addModalOptions = {
                type: 'popup',
                modalClass: 'wishlist-popup-modal',
                responsive: true,
                innerScroll: true,
                title: $.mage.__('Add Wishlist'),
                buttons: []
            };

            wpWishlistPopup = modal(addModalOptions, wpWishlistModalPopup);
            wpErrorsContainer.html('');
            wpWishlistPopup.openModal();
            $('#wishlist-name').val('');
            $('#wishlist-id').val('');
            $('#deletewishlist').hide();
        },
        submitWishlist: function(form) {
            if (form.valid()) {
                var url = form.attr('action');
                $.ajax({
                    url: url,
                    method: 'POST',
                    cache: false,
                    global: false,
                    data: form.serialize(),
                    success: function (response) {
                        if (response.reload) {
                            window.location.reload();
                        } else if (response.result) {
                            wpWishlistPopup.closeModal();
                            wpWishlistNameElm.html(form.find('[name="wishlist-name"]').val());
                        } else {
                            form.find('.wp-errors').html(response.msg).show();
                        }
                    }
                });
            }
        }
    };

});