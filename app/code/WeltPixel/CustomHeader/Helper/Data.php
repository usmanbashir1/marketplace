<?php

namespace WeltPixel\CustomHeader\Helper;

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

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
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
     * @return mixed
     */
    public function getTopHeaderWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderLinkColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/link_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderHoverLinkColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/hover_link_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderActiveLinkColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/active_link_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderSubmenuLinkColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/submenu_link_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderSubmenuHoverLinkColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/submenu_hover_link_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderTextColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/text_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderBackgroundColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/background_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function stickyHeaderIsEnabled($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/sticky_header/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function customLogoSizeOnStickyHeaderEnabled($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/sticky_header/custom_logo_size_on_sticky', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function stickyHeaderLogoWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/sticky_header/sticky_logo_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function stickyHeaderLogoHeight($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/sticky_header/sticky_logo_height', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function stickyHeaderLogoTopPosition($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/sticky_header/sticky_logo_top_position', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function stickyHeaderMobileIsEnabled($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/sticky_header/enable_mobile', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function advancedColorsIsEnabled($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/sticky_header/advanced_colors', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getStickyHeaderBackgroundColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/sticky_header/sticky_background_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getStickyHeaderElementsColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/sticky_header/sticky_elements_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getStickyHeaderElementsHoverColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/sticky_header/sticky_elements_hover_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderBorderBottomColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/top_header/border_bottom_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getMiddleHeaderWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/middle_header/width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getMiddleHeaderBackgroundColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/middle_header/background', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBottomHeaderWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/bottom_header/width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBottomHeaderBackgroundColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/bottom_header/background', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getBottomHeaderMenuFontSizeL0($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/bottom_header/font_size_l0', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getBottomHeaderMenuFontSizeL1($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/bottom_header/font_size_l1', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getBottomHeaderMenuFontSizeL2($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/bottom_header/font_size_l2', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBottomHeaderLinkColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/bottom_header/link_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBottomHeaderHoverLinkColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/bottom_header/hover_link_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBottomNavigationShadow($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/bottom_header/shadow', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsVersion($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/version', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsHeight($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/height', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsBorderWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/border_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsBorderStyle($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/border_style', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsBorderColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/border_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsBackground($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/background', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsPlaceHolderColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/placeholder_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSerachOptionsFontSize($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/font_size', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getHeaderIconSize($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/icons/icon_size', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBackgroundColorSearchv2($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/background_v2', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBackgroundOpacitySearchv2($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/background_opacity', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getInputBackgroundColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/background_input', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getMainSearchElementsColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/main_elements_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function magnifierIconIsEnabled($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/display_magnifier', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }


    /**
     * @param int $storeId
     * @return mixed
     */
    public function getMagnifierBackgroundColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/magnifier_background_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getInputBorders($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/input_borders', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSearchInputFontSize($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/input_font_size', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBorderWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/border_widthv2', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getPlaceholderText($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/search_options/placeholder_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
    /* @param int $storeId
     * @return string
     */
    public function getCustomHtml($storeId = null){
        return trim($this->scopeConfig->getValue('weltpixel_custom_header/search_options/customHtml', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId));
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getHeaderIconColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/icons/icon_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getHeaderIconHoverColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/icons/icon_color_hover', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isGlobalPromoEnabled($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/global_promo/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getGlobalPromoTextColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/global_promo/text_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getGlobalPromoBackgroundColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/global_promo/background_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
}
