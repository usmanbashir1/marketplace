<?php

namespace WeltPixel\AdvancedWishlist\Helper;
use Magento\Wishlist\Helper\Data as WishlistHelperData;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WishlistData extends WishlistHelperData
{
    /**
     * Create wishlist item collection
     *
     * @return \Magento\Wishlist\Model\ResourceModel\Item\Collection
     */
    protected function _createWishlistItemCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  \WeltPixel\AdvancedWishlist\Model\MultipleWishlistProvider $multiWishlistProvider */
        $multiWishlistProvider = $objectManager->create('\WeltPixel\AdvancedWishlist\Model\MultipleWishlistProvider');
        return $multiWishlistProvider->getWishlistItemCollection();
    }

}
