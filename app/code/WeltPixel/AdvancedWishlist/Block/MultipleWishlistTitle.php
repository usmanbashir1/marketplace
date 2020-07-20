<?php

namespace WeltPixel\AdvancedWishlist\Block;

use Magento\Wishlist\Model\WishlistFactory;
use PHPUnit\Runner\Exception;

class MultipleWishlistTitle extends \Magento\Framework\View\Element\Template
{
    /**
     * @var WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var bool
     */
    protected $shouldDisplay = false;

    /**
     * @param WishlistFactory $wishlistFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(WishlistFactory $wishlistFactory,
                                \Magento\Framework\View\Element\Template\Context $context,
                                array $data = [])
    {
        $this->wishlistFactory = $wishlistFactory;
        parent::__construct($context, $data);
    }

    /**
     * Preparing global layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        if (!$wishlistId) {
            return;
        }

        try {
            $wishlist = $this->wishlistFactory->create();
            $wishlist->load($wishlistId);
            $pageTitle = $wishlist->getWishlistName();
            $this->pageConfig->getTitle()->set($pageTitle);
            $this->shouldDisplay = true;
        } catch (\Exception $ex) {}
    }

    /**
     * @return bool
     */
    public function displayBackLink() {
        return $this->shouldDisplay;
    }
}
