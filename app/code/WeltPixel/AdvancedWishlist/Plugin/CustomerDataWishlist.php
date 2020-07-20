<?php

namespace WeltPixel\AdvancedWishlist\Plugin;

use Magento\Wishlist\CustomerData\Wishlist as WishlistCustomerData;
use WeltPixel\AdvancedWishlist\CustomerData\Wishlist as WeltPixelWishlistCustomerData;
use WeltPixel\AdvancedWishlist\Helper\Data as WishlistHelper;

class CustomerDataWishlist
{
    /**
     * @var WeltPixelWishlistCustomerData
     */
    protected $_wpCustomerWishlist;
    /**
     * @var WishlistHelper
     */
    protected $_helper;

    /**
     * CustomerSharingBlock constructor.
     * @param WishlistHelper $helper
     * @param WeltPixelWishlistCustomerData $wpCustomerWishlist
     */
    public function __construct(
        WishlistHelper $helper,
        WeltPixelWishlistCustomerData $wpCustomerWishlist
    )
    {
        $this->_helper = $helper;
        $this->_wpCustomerWishlist = $wpCustomerWishlist;
    }

    /**
     * @param WishlistCustomerData $subject
     * @param array $result
     * @return mixed
     */
    public function afterGetSectionData(
        WishlistCustomerData $subject,
        $result
    ){
        $isMultiWishlistEnabled = $this->_helper->isMultiWishlistEnabled();

        if ($isMultiWishlistEnabled) {
            $counter = $this->_wpCustomerWishlist->getMultiWishlistCounter();
            $result['counter'] = $counter;
            $result['items'] = $counter ? $this->_wpCustomerWishlist->getMultiWishlistItems() : [];
        }

        return $result;
    }
}