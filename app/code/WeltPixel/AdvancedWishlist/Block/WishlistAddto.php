<?php

namespace WeltPixel\AdvancedWishlist\Block;


class WishlistAddto extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \WeltPixel\AdvancedWishlist\Helper\Data
     */
    protected $_helper;

    /**
     * @var \WeltPixel\AdvancedWishlist\Model\MultipleWishlistProvider
     */
    protected $_multipleWishlistProvider;
    /**
     * @param \WeltPixel\AdvancedWishlist\Helper\Data $helper
     * @param \WeltPixel\AdvancedWishlist\Model\MultipleWishlistProvider $multipleWishlistProvider
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(\WeltPixel\AdvancedWishlist\Helper\Data $helper,
                                \WeltPixel\AdvancedWishlist\Model\MultipleWishlistProvider $multipleWishlistProvider,
                                \Magento\Framework\View\Element\Template\Context $context,
                                array $data = [])
    {
        $this->_helper = $helper;
        $this->_multipleWishlistProvider = $multipleWishlistProvider;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isMultiWishlistEnabled() {
        return $this->_helper->isMultiWishlistEnabled();
    }

    /**
     * @return bool
     */
    public function isAjaxWishlistEnabled() {
        return $this->_helper->isAjaxWishlistEnabled();
    }

    /**
     * @return array
     */
    public function getMultipleWishlists() {
        return $this->_multipleWishlistProvider->getWishlists();
    }


}
