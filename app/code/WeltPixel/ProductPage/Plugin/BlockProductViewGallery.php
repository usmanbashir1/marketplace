<?php

namespace WeltPixel\ProductPage\Plugin;

class BlockProductViewGallery
{

    const XML_PATH_WELTPIXEL_PRODUCTPAGE_MAGNIFIER_ENABLED = 'weltpixel_product_page/magnifier/enabled';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_MAGNIFIER_FULLSCREENZOOM = 'weltpixel_product_page/magnifier/fullscreenzoom';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_MAGNIFIER_TOP = 'weltpixel_product_page/magnifier/top';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_MAGNIFIER_LEFT = 'weltpixel_product_page/magnifier/left';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_MAGNIFIER_WIDTH = 'weltpixel_product_page/magnifier/width';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_MAGNIFIER_HEIGHT = 'weltpixel_product_page/magnifier/height';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_MAGNIFIER_EVENTTYPE = 'weltpixel_product_page/magnifier/eventtype';

    const XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_NAV = 'weltpixel_product_page/gallery/nav';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_LOOP = 'weltpixel_product_page/gallery/loop';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_KEYBOARD = 'weltpixel_product_page/gallery/keyboard';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_ARROWS = 'weltpixel_product_page/gallery/arrows';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_ARROWS_BG = 'weltpixel_product_page/gallery/arrows_bg';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_CAPTION = 'weltpixel_product_page/gallery/caption';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_NAVDIR = 'weltpixel_product_page/gallery/navdir';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_NAVARROWS = 'weltpixel_product_page/gallery/navarrows';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_NAVTYPE = 'weltpixel_product_page/gallery/navtype';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_TRANSITION_EFFECT = 'weltpixel_product_page/gallery/transition_effect';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_TRANSITION_DURATION = 'weltpixel_product_page/gallery/transition_duration';

    const XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_ALLOWFULLSCREEN = 'weltpixel_product_page/fullscreen/allowfullscreen';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_NAV = 'weltpixel_product_page/fullscreen/nav';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_LOOP = 'weltpixel_product_page/fullscreen/loop';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_ARROWS = 'weltpixel_product_page/fullscreen/arrows';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_CAPTION = 'weltpixel_product_page/fullscreen/caption';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_NAVDIR = 'weltpixel_product_page/fullscreen/navdir';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_TRANSITION_EFFECT = 'weltpixel_product_page/fullscreen/transition_effect';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_TRANSITION_DURATION = 'weltpixel_product_page/fullscreen/transition_duration';



    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;
    
    /**
     *
     * @var  \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     *
     * @var  \Magento\Framework\Json\DecoderInterface
     */
    protected $jsonDecoder;
    
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     */
    public function __construct(
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Framework\Json\EncoderInterface $jsonEncoder,
            \Magento\Framework\Json\DecoderInterface $jsonDecoder)
    {
        $this->scopeConfig = $scopeConfig;
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
    }

    /**
     * @param \Magento\Catalog\Block\Product\View\Gallery $subject
     * @param $result
     * @return mixed
     */
    public function afterGetMagnifier(
        \Magento\Catalog\Block\Product\View\Gallery $subject, $result
    )
    {
        $result = $this->jsonDecoder->decode($result);
        $magnifierEnabled = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_MAGNIFIER_ENABLED,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $magnifierFullscreenzoom = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_MAGNIFIER_FULLSCREENZOOM,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $magnifierTop = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_MAGNIFIER_TOP,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $magnifierLeft = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_MAGNIFIER_LEFT,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $magnifierWidth = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_MAGNIFIER_WIDTH,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $magnifierHeight = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_MAGNIFIER_HEIGHT,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $magnifierEventtype = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_MAGNIFIER_EVENTTYPE,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $result['enabled'] = $magnifierEnabled;
        $result['fullscreenzoom'] = $magnifierFullscreenzoom;
        $result['top'] = $magnifierTop;
        $result['left'] = $magnifierLeft;
        $result['width'] = $magnifierWidth;
        $result['height'] = $magnifierHeight;
        $result['eventType'] = $magnifierEventtype;

        return $this->jsonEncoder->encode($result);
    }


    /**
     * @param \Magento\Catalog\Block\Product\View\Gallery $subject
     * @param \Closure $proceed
     * @param string $name
     * @param string|null $module
     * @return string|false
     */
    public function aroundGetVar(
        \Magento\Catalog\Block\Product\View\Gallery $subject,
        \Closure $proceed,
        $name,
        $module = null
    )
    {
        $result = $proceed($name, $module);

        switch ($name) {
            case "gallery/nav" :
                $result = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_NAV,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/loop" :
                $result = filter_var($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_LOOP,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/keyboard" :
                $result = filter_var($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_KEYBOARD,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/arrows" :
                $result = filter_var($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_ARROWS,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/arrows_bg" :
                $result = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_ARROWS_BG,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/caption" :
                $result = filter_var($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_CAPTION,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/transition/duration" :
                $result = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_TRANSITION_DURATION,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/transition/effect" :
                $result = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_TRANSITION_EFFECT,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/navarrows" :
                $result = filter_var($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_NAVARROWS,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/navtype" :
                $result = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_NAVTYPE,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/navdir" :
                $result = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_NAVDIR,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/allowfullscreen" :
                $result = filter_var($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_ALLOWFULLSCREEN,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/fullscreen/nav" :
                $result = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_NAV,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/fullscreen/loop" :
                $result = filter_var($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_LOOP,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/fullscreen/navdir" :
                $result = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_NAVDIR,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/fullscreen/arrows" :
                $result = filter_var($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_ARROWS,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/fullscreen/caption" :
                $result = filter_var($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_CAPTION,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
                break;
            case "gallery/fullscreen/transition/duration" :
                $result = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_TRANSITION_DURATION,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case "gallery/fullscreen/transition/effect" :
                $result = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_FULLSCREEN_TRANSITION_EFFECT,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;

        }


        return $result;
    }
}
