<?php
namespace WeltPixel\AdvancedWishlist\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddUpdateHandlesObserver implements ObserverInterface
{
    /**
     * @var \WeltPixel\AdvancedWishlist\Helper\Data
     */
    protected $_wishlistHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param \WeltPixel\AdvancedWishlist\Helper\Data $wishlistHelper
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(\WeltPixel\AdvancedWishlist\Helper\Data $wishlistHelper,
                                \Magento\Framework\App\Request\Http $request)
    {
        $this->request = $request;
        $this->_wishlistHelper = $wishlistHelper;
    }
    
    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $layout = $observer->getData('layout');
        $fullActionName = $observer->getData('full_action_name');

        switch ($fullActionName) {
            case 'wishlist_index_index' :
                $wishlistId = $this->request->getParam('wishlist_id');
                $multipleWishlistEnabled = $this->_wishlistHelper->isMultiWishlistEnabled();

                if ($wishlistId || !$multipleWishlistEnabled) {
                    return $this;
                }
                $layout->getUpdate()->addHandle('wishlist_index_index_wp_multi');

                break;
        }

        return $this;
    }
}
