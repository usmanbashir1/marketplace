<?php
namespace WeltPixel\AdvancedWishlist\Model;

class MultipleWishlistProvider
{
    /**
     * @var array
     */
    protected $wishlists = null;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory
     */
    protected $wishlistItemCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Item\Collection|null
     */
    protected $wishlistItemsCollection = null;


    /**
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $wishlistItemCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $wishlistItemCollectionFactory,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistItemCollectionFactory = $wishlistItemCollectionFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * @return array
     */
    public function getWishlists()
    {
        if (isset($this->wishlists)) {
            return $this->wishlists;
        }
        try {
            $customerId = $this->customerSession->getCustomerId();

            if (!$customerId) {
                return [];
            }

            $wishlists = [];
            $wishlistModel = $this->wishlistFactory->create();
            $wishlistCollection = $wishlistModel->getCollection()->filterByCustomerId($customerId);

            foreach ($wishlistCollection->getItems() as $item) {
                $wishlists[] = $item;
            }

            $this->wishlists = $wishlists;

        } catch (\Exception $e) {
            return [];
        }

        return $this->wishlists;
    }

    /**
     * @return \Magento\Wishlist\Model\ResourceModel\Item\Collection|null
     */
    public function getWishlistItemCollection()
    {
        if ($this->wishlistItemsCollection === null) {
            try {
                $customerId = $this->customerSession->getCustomerId();
                if (!$customerId) {
                    return null;
                }
                $this->wishlistItemsCollection = $this->getWishlistItemsForCustomer($customerId);
            } catch (\Exception $e) {}
        }

        return $this->wishlistItemsCollection;
    }

    /**
     * @param $customerId
     * @return \Magento\Wishlist\Model\ResourceModel\Item\Collection
     */
    public function getWishlistItemsForCustomer($customerId) {
        $wishlistModel = $this->wishlistFactory->create();
        $customerWishlistsIds = $wishlistModel->getCollection()->filterByCustomerId($customerId)->getAllIds();
        $itemsCollection = $this->wishlistItemCollectionFactory->create()
            ->addFieldToFilter('wishlist_id', array('in' => $customerWishlistsIds))
            ->addStoreFilter($wishlistModel->getSharedStoreIds())
            ->setVisibilityFilter();
        return $itemsCollection;
    }
}
