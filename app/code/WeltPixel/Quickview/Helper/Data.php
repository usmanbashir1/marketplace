<?php

namespace WeltPixel\Quickview\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const LOADING_IMAGE_PATH = 'weltpixel/quickview/loading/';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var array
     */
    protected $_quickviewOptions;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->_quickviewOptions = $this->scopeConfig->getValue('weltpixel_quickview', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->_storeManager = $storeManager;
        $this->_filesystem = $filesystem;
    }

    /**
     * @return string
     */
    public function getSkuTemplate()
    {
        $removeSku = $this->_quickviewOptions['general']['remove_sku'];
        if (!$removeSku) {
            return 'Magento_Catalog::product/view/attribute.phtml';
        }

        return '';
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return trim($this->_quickviewOptions['general']['enable_product_listing']);
    }

    /**
     * @return bool
     */
    public function isPrevNextEnabled()
    {
        return trim($this->_quickviewOptions['general']['enable_prevnext_product']);
    }

    /**
     * @return string
     */
    public function getCustomCSS()
    {
        return trim($this->_quickviewOptions['general']['custom_css']);
    }

    /**
     * @return int
     */
    public function getCloseSeconds()
    {
        return trim($this->_quickviewOptions['general']['close_quickview']);
    }

    /**
     * @return boolean
     */
    public function getScrollAndOpenMiniCart()
    {
        return $this->_quickviewOptions['general']['scroll_to_top'];
    }

    /**
     * @return boolean
     */
    public function getShoppingCheckoutButtons()
    {
        return $this->_quickviewOptions['general']['enable_shopping_checkout_product_buttons'];
    }

    /**
     * @return string
     */
    public function getQuickViewType()
    {
        return  $this->_quickviewOptions['general']['quickview_type'];
    }

    /**
     * @return boolean
     */
    public function getCloseOnBgClick()
    {
        return  (boolean)$this->_quickviewOptions['general']['close_on_bgclick'];
    }

    /**
     * @return bool
     */
    public function isCustomMessageEnabled() {
        return  (boolean)$this->_quickviewOptions['custom_message']['enable'];
    }

    /**
     * @return bool
     */
    public function isDynamicCustomMessageEnabled() {
        return  (boolean)$this->_quickviewOptions['custom_message']['enable_dynamic'];
    }


    /**
     * @return string
     */
    public function getCustomMessageContent()
    {
        return  trim($this->_quickviewOptions['custom_message']['content']);
    }

    /**
     * @return string
     */
    public function getCustomMessageBgColor()
    {
        return  trim($this->_quickviewOptions['custom_message']['background_color']);
    }

    /**
     * @return string
     */
    public function getCustomMessageFontColor()
    {
        return  trim($this->_quickviewOptions['custom_message']['font_color']);
    }

    /**
     * @return string
     */
    public function getCustomMessageFontSize()
    {
        return trim($this->_quickviewOptions['custom_message']['font_size']);
    }

    /**
     * @return string
     */
    public function getCustomMessageCustomCss()
    {
        return trim($this->_quickviewOptions['custom_message']['custom_css']);
    }


    /**
     * @return string
     */
    public function getZoomType()
    {
        return  (boolean)isset($this->_quickviewOptions['general']['zoom_eventtype']) ?
            $this->_quickviewOptions['general']['zoom_eventtype'] : false;
    }

    /**
     * @return string
     */
    public function getLoadingOverlayBgColor() {
        return trim($this->_quickviewOptions['general']['loading_overlay_bg_color']);
    }


    /**
     * @return string
     */
    public function getLoadingOverlayOpacity() {
        return trim($this->_quickviewOptions['general']['loading_overlay_opacity']);
    }

    /**
     * @return bool
     */
    public function isLoadingPlaceholderEnabled() {
        return (boolean)$this->_quickviewOptions['general']['loading_placeholder'];
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getLoadingIcon() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) .
        self::LOADING_IMAGE_PATH . $this->_quickviewOptions['general']['loading_icon'];
    }

    /**
     * @return array
     */
    public function getLoadingIconResolutions() {
        $loadingImageSrc = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            ->getAbsolutePath() . self::LOADING_IMAGE_PATH .  $this->_quickviewOptions['general']['loading_icon'];

        $imgPathArr = explode('.', $loadingImageSrc);
        $imgType = end($imgPathArr);

        $width = '';
        $height = '';

        if ($imgType != 'svg') {
            list($width, $height) = @getimagesize($loadingImageSrc);
        } else {
            $xml = simplexml_load_file($loadingImageSrc);
            $attr = $xml->attributes();
            $width = $attr->width;
            $height = $attr->height;
        }

        return [
            'width' => $width,
            'height' => $height
        ];
    }

    /**
     * @return string
     */
    public function getVersionTemplate()
    {
        $template = 'WeltPixel_Quickview::version/simple_popup.phtml';
        $quickViewType = $this->getQuickViewType();
        if ($quickViewType == \WeltPixel\Quickview\Model\Config\Source\QuickviewType::TYPE_RIGHT_SLIDE) {
            $template = 'WeltPixel_Quickview::version/right_fadein_popup.phtml';
        }
        if ($quickViewType == \WeltPixel\Quickview\Model\Config\Source\QuickviewType::TYPE_LEFT_SLIDE) {
            $template = 'WeltPixel_Quickview::version/left_fadein_popup.phtml';
        }

        return $template;
    }
}
