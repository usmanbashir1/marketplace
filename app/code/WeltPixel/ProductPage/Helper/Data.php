<?php

namespace WeltPixel\ProductPage\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PAGE_VERSION_NO_GALLERY = [2, 4];
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \WeltPixel\MobileDetect\Helper\Data
     */
    protected $mobileDetectHelper;

    protected $request;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \WeltPixel\MobileDetect\Helper\Data $mobileDetectHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \WeltPixel\MobileDetect\Helper\Data $mobileDetectHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->mobileDetectHelper = $mobileDetectHelper;
        $this->request = $request;
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function productVersion($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/version/version', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function moveDescriptionsTabs($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/move_description_tabs_under_info_area', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function getPositionProductInfo($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/position_product_info', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function removeWishlist($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/remove_wishlist', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function removeCompare($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/remove_compare', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getImageAreaWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/image_area_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getProductInfoAreaWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/product_info_area_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function removeSwatchTooltip($storeId = null) {
        return !$this->scopeConfig->getValue('weltpixel_product_page/general/display_swatch_tooltip', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }


    /**
     * @param int $storeId
     * @return boolean
     */
    public function getTabsLayout($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/tabs_layout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }


    /**
     * @param int $storeId
     * @return boolean
     */
    public function getTabsVersion($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/tabs_version', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function getAccordionVersion($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/accordion_version', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function isAccordionCollapsible($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/accordion_collapsible', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }


    /**
     * @param int $storeId
     * @return boolean
     */
    public function getQtySelectMaxValue($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/qty_select_maxvalue', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getSwatchOptions($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/swatch', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getCssOptions($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/css', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

	/**
	 * @param int $storeId
	 * @return mixed
	 */
	public function getBackgroundArrows($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/gallery/arrows_bg', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
	}


    /**
     * @param int $storeId
     * @return mixed
     */
    public function getGalleryNavDir($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/gallery/navdir', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getMagnifierEnabled($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/magnifier/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
	public function getMagnifierEventType($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/magnifier/eventtype', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->request->getParam('id');
    }
}
