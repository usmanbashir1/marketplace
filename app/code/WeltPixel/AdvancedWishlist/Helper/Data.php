<?php

namespace WeltPixel\AdvancedWishlist\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $_wishlistOptions;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->_wishlistOptions = $this->scopeConfig->getValue('weltpixel_advancedwishlist', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isMultiWishlistEnabled($websiteId = null) {
        if (!$websiteId) {
            return $this->scopeConfig->getValue('weltpixel_advancedwishlist/general/enable_multiwishlist', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE, $websiteId);
        }
        return trim($this->_wishlistOptions['general']['enable_multiwishlist']);
    }

    public function isAjaxWishlistEnabled() {
        return trim($this->_wishlistOptions['general']['enable_ajaxwishlist']);
    }

}
