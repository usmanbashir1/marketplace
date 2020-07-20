<?php

namespace WeltPixel\AdvancedWishlist\Plugin;

use Magento\Wishlist\Controller\Index\Add as WishlistAddController;
use WeltPixel\AdvancedWishlist\Helper\Data as WishlistHelper;

class WishlistAddAction
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
     * @param WishlistAddController $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundExecute(
        WishlistAddController $subject,
        \Closure $proceed
    ){
        $result = $proceed();
        if ($subject->getRequest()->getParam('ajax') == 1 && $this->_helper->isAjaxWishlistEnabled()) {
            return $subject->getResponse()->representJson(json_encode([
                'result' => true
            ]));

        }
        return $result;
    }
}