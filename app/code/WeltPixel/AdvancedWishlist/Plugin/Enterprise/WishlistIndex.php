<?php

namespace WeltPixel\AdvancedWishlist\Plugin\Enterprise;

use Magento\MultipleWishlist\Controller\Index\Index as MutipleWishlistIndexController;
use Magento\Wishlist\Controller\Index\Index as WishlistIndexController;
use WeltPixel\AdvancedWishlist\Helper\Data as WishlistHelper;

class WishlistIndex
{

    /**
     * @var WishlistHelper
     */
    protected $_helper;

    /**
     * @var WishlistIndexController
     */
    protected $wishistIndexController;

    /**
     * WishlistIndex constructor.
     * @param WishlistHelper $helper
     * @param WishlistIndexController $wishistIndexController
     */
    public function __construct(
        WishlistHelper $helper,
        WishlistIndexController $wishistIndexController
    )
    {
        $this->_helper = $helper;
        $this->wishistIndexController = $wishistIndexController;
    }

    /**
     * @param MutipleWishlistIndexController $subject
     * @param $result
     * @return \Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function afterExecute(
        MutipleWishlistIndexController $subject,
        $result
    ){
        if ($this->_helper->isMultiWishlistEnabled()) {
            return $this->wishistIndexController->execute();
        }
        return $result;
    }
}