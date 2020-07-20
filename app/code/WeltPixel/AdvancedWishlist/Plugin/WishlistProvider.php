<?php

namespace WeltPixel\AdvancedWishlist\Plugin;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use WeltPixel\AdvancedWishlist\Helper\Data as WishlistHelper;


class WishlistProvider
{

    /**
     * @var WishlistHelper
     */
    protected $_helper;

    /**
     * @var  HttpRequest
     */
    protected $_request;

    /**
     * WishlistProvider constructor.
     * @param WishlistHelper $helper
     * @param HttpRequest $request
     */
    public function __construct(
        WishlistHelper $helper,
        HttpRequest $request
    )
    {
        $this->_helper = $helper;
        $this->_request = $request;
    }

    /**
     * @param WishlistProviderInterface $subject
     * @param int $wishlistId
     * @return array
     */
    public function beforeGetWishlist(
        WishlistProviderInterface $subject, $wishlistId = null
    )
    {
        $isMultiWishlistEnabled = $this->_helper->isMultiWishlistEnabled();

        if ($isMultiWishlistEnabled) {
            if ($this->_request->getFullActionName() == 'wishlist_index_send') {
                $wishlistId = $this->_request->getParam('wishlist_id');
            }
        }

        return [$wishlistId];
    }
}