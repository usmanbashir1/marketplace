<?php

namespace WeltPixel\AdvancedWishlist\Plugin;

use Magento\Wishlist\Block\Customer\Sharing as SharingBlock;
use WeltPixel\AdvancedWishlist\Helper\Data as WishlistHelper;


class CustomerSharingBlock
{

    /**
     * @var WishlistHelper
     */
    protected $_helper;

    /**
     * CustomerSharingBlock constructor.
     * @param WishlistHelper $helper
     */
    public function __construct(
        WishlistHelper $helper
    )
    {
        $this->_helper = $helper;
    }

    /**
     * @param SharingBlock $subject
     * @param string $result
     * @return array
     */
    public function afterGetBlockHtml(
        SharingBlock $subject, $result
    )
    {
        $isMultiWishlistEnabled = $this->_helper->isMultiWishlistEnabled();

        if ($isMultiWishlistEnabled) {
            $wishlistId = $subject->getRequest()->getParam('wishlist_id', null);
            $result .= PHP_EOL . '<input type="hidden" name="wishlist_id" value="'.$wishlistId." />";
        }

        return $result;
    }
}