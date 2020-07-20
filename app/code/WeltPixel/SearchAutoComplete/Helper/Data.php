<?php

namespace WeltPixel\SearchAutoComplete\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isEnabled($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isEnablePopularSuggestions($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/popularSuggestions/enablePopularSuggestions', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isEnableCategorySearch($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/categorySearch/enableCategorySearch', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getMaxNumberOfCategoriesDisplayed($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/categorySearch/maxItems', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }


    /**
     * @param int $storeId
     * @return mixed
     */
    public function isEnableProductDivider($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/frontendSettings/enableProductDivider', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isEnableAutoComplete($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/productSearch/enableAutoComplete', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Whether a module is enabled in the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isModuleEnabled($moduleName)
    {
        return $this->_moduleManager->isEnabled($moduleName);
    }

    /**
     * Whether a module output is permitted by the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isOutputEnabled($moduleName)
    {
        return $this->_moduleManager->isOutputEnabled($moduleName);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSearchVersion($storeId = null) {
        if ($this->isModuleEnabled('WeltPixel_CustomHeader') && $this->isOutputEnabled('WeltPixel_CustomHeader') ){
            return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/version', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        }
        return false;
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function getMinNumberOfCharacters($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/productSearch/minimalChar', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getTextForNoSearchResult($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/productSearch/noResult', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function getWidthOfResultsContainer($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/frontendSettings/widthResult', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getMaxNumberItemsDisplayed($storeId = null) {
        $config = $this->scopeConfig->getValue('weltpixel_searchautocomplete/productSearch/maxItems', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        return empty(trim($config)) ?  3 : (int) $config;
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function getMaxWordsProductDescription($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/productSearch/maxWordsProdDescr', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isShowImageThumbnail($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/productSearch/showImg', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isShowDescription($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/productSearch/showDescr', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isShowPrice($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/productSearch/showPrice', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function getWidthOfTheImage($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/productSearch/widthImg', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTemplateResultsContainer($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/frontendSettings/templateResultsContainer', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getDividerColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/frontendSettings/colorProductDivider', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }


    /**
     * @param int $storeId
     * @return mixed
     */
    public function getContainerBackgroundColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/frontendSettings/containerBackgroundColor', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTitleBackgroundColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/frontendSettings/titleBackgroundColor', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTitleColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/frontendSettings/titleColor', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getContainerTextColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_searchautocomplete/frontendSettings/containerTextColor', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
    /* @param int $storeId
     * @return string
     */
    public function getSearchResultHeaderText($storeId = null){
        return trim($this->scopeConfig->getValue('weltpixel_searchautocomplete/productSearch/resultHeader', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId));
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getSearchResultFooterText($storeId = null){
        return trim($this->scopeConfig->getValue('weltpixel_searchautocomplete/productSearch/resultFooter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId));
    }

    /**
     * @param string $text
     * @param int $limit
     * @return string
     */
    public function limitText($text, $limit) {
        if (str_word_count($text, 0) > $limit ) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }

    /**
     * @param $itemCollection
     * @return bool
     */
    public function isEmptyCollection($itemCollection) {
        if(empty($itemCollection)) {
            return true;
        }
        foreach($itemCollection as $item) {
            if($item->getNumResults() === 0) {
                return true;
            };
        }
        return false;
    }

    public function getStoreId(){
        return $this->_storeManager->getStore()->getId();
    }
}
