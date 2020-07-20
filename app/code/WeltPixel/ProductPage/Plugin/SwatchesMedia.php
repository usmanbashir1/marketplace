<?php
namespace WeltPixel\ProductPage\Plugin;

class SwatchesMedia
{

    const XML_PATH_WELTPIXEL_PRODUCTPAGE_SWATCH_WIDTH = 'weltpixel_product_page/swatch/width';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_SWATCH_HEIGHT = 'weltpixel_product_page/swatch/height';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /** @var  \Magento\Framework\App\Request\Http */
    protected $request;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $request)
    {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    /**
     * @param \Magento\Swatches\Helper\Media $subject
     * @param array $result
     * @return array
     */
    public function afterGetImageConfig(\Magento\Swatches\Helper\Media $subject, $result)
    {
        $fullActionName = $this->request->getFullActionName();

        if (in_array($fullActionName, ['checkout_cart_configure', 'catalog_product_view', 'weltpixel_quickview_catalog_product_view'])) {
            $layeredImageWidth = trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_SWATCH_WIDTH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            $layeredImageHeight = trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_SWATCH_HEIGHT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));

            $swatchIds = ['swatch_image', 'swatch_thumb'];
            foreach ($swatchIds as $id) {
                $result[$id]['width'] = intval($layeredImageWidth);
                $result[$id]['height'] = intval($layeredImageHeight);
            }
        }

        return $result;
    }
}