<?php

namespace WeltPixel\ProductPage\Plugin;

class BlockProductViewGalleryOptions
{
    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Catalog\Block\Product\View\GalleryOptions $subject
     * @param \Closure $proceed
     * @param string $name
     * @param string|null $module
     * @return string|false
     */
    public function aroundGetVar(
        \Magento\Catalog\Block\Product\View\GalleryOptions $subject,
        \Closure $proceed,
        $name,
        $module = null
    ) {
        $result = $proceed($name, $module);

        switch ($name) {
            case "gallery/nav":
                $result = $this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_NAV, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/loop":
                $result = filter_var($this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_LOOP, \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/keyboard":
                $result = filter_var($this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_KEYBOARD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/arrows":
                $result = filter_var($this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_ARROWS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/arrows_bg":
                $result = $this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_ARROWS_BG, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/caption":
                $result = filter_var($this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_CAPTION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/transition/duration":
                $result = $this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_TRANSITION_DURATION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/transition/effect":
                $result = $this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_TRANSITION_EFFECT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/navarrows":
                $result = filter_var($this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_NAVARROWS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/navtype":
                $result = $this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_NAVTYPE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/navdir":
                $result = $this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_NAVDIR, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/allowfullscreen":
                $result = filter_var($this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_ALLOWFULLSCREEN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/fullscreen/nav":
                $result = $this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_NAV, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/fullscreen/loop":
                $result = filter_var($this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_LOOP, \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/fullscreen/navdir":
                $result = $this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_NAVDIR, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/fullscreen/arrows":
                $result = filter_var($this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_ARROWS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/fullscreen/caption":
                $result = filter_var($this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_CAPTION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/fullscreen/transition/duration":
                $result = $this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_TRANSITION_DURATION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/fullscreen/transition/effect":
                $result = $this->scopeConfig->getValue(BlockProductViewGallery::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_TRANSITION_EFFECT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;

        }

        return $result;
    }
}
