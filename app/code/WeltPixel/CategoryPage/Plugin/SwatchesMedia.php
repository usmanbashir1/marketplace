<?php
namespace WeltPixel\CategoryPage\Plugin;

class SwatchesMedia
{

    const XML_PATH_WELTPIXEL_CATEGORYPAGE_LAYERED_SWATCH_WIDTH = 'weltpixel_category_page/swatch_layerednavigation/width';
    const XML_PATH_WELTPIXEL_CATEGORYPAGE_LAYERED_SWATCH_HEIGHT = 'weltpixel_category_page/swatch_layerednavigation/height';

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

        if (in_array($fullActionName, ['catalog_category_view','catalogsearch_result_index', 'catalogsearch_advanced_result'])) {
            $layeredImageWidth = trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_CATEGORYPAGE_LAYERED_SWATCH_WIDTH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            $layeredImageHeight = trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_CATEGORYPAGE_LAYERED_SWATCH_HEIGHT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));

            $swatchIds = ['swatch_image','swatch_image_base'];
            foreach ($swatchIds as $id) {
                $result[$id]['width'] = intval($layeredImageWidth);
                $result[$id]['height'] = intval($layeredImageHeight);
            }
        }

        return $result;
    }
}