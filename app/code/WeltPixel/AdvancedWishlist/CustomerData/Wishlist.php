<?php
namespace WeltPixel\AdvancedWishlist\CustomerData;

use Magento\Wishlist\CustomerData\Wishlist as WishlistCustomerData;

/**
 * Class Wishlist
 * @package WeltPixel\AdvancedWishlist\CustomerData
 */
class Wishlist extends WishlistCustomerData
{
    /**
     * @return string
     */
    public function getMultiWishlistCounter() {
        return $this->getCounter();
    }

    /**
     * @return array
     */
    public function  getMultiWishlistItems() {
        $collection = $this->wishlistHelper->getWishlistItemCollection();
        $collection->clear()->setPageSize(self::SIDEBAR_ITEMS_NUMBER)
            ->setInStockFilter(true)->setOrder('added_at');

        $items = [];
        foreach ($collection as $wishlistItem) {
            $items[] = $this->getItemData($wishlistItem);
        }
        return $items;
    }
}
