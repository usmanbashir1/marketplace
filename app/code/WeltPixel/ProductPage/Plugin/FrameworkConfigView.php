<?php

namespace WeltPixel\ProductPage\Plugin;

use Magento\Catalog\Helper\Image;

class FrameworkConfigView
{

    const XML_PATH_WELTPIXEL_PRODUCTPAGE_IMAGE_MAIN_WIDTH = 'weltpixel_product_page/images/main_image_width';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_IMAGE_MAIN_HEIGHT = 'weltpixel_product_page/images/main_image_height';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_IMAGE_THUMB_WIDTH = 'weltpixel_product_page/images/thumb_image_width';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_IMAGE_THUMB_HEIGHT = 'weltpixel_product_page/images/thumb_image_height';


    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve array of media attributes
     *
     * @param \Magento\Framework\Config\View $subject
     * @param \Closure $proceed
     * @param string $module
     * @param string $mediaType
     * @param string $mediaId
     * @return array
     */
    public function aroundGetMediaAttributes(
        \Magento\Framework\Config\View $subject,
        \Closure $proceed,
        $module,
        $mediaType,
        $mediaId)
    {
        $result = $proceed($module, $mediaType, $mediaId);

        if (($module == 'Magento_Catalog') && ($mediaType == Image::MEDIA_TYPE_CONFIG_NODE)) {
            /** Big image size overwrite */
            if (($mediaId == 'product_page_image_medium') || ($mediaId == 'product_page_image_medium_no_frame')) {
                $width = trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_IMAGE_MAIN_WIDTH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
                $height = trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_IMAGE_MAIN_HEIGHT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));

                if ($width) {
                    $result['width'] = $width;
                }
                if ($height) {
                    $result['height'] = $height;
                }
            }

            /** Thumb image size overwrite */
            if ($mediaId == 'product_page_image_small') {
                $width = trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_IMAGE_THUMB_WIDTH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
                $height = trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_IMAGE_THUMB_HEIGHT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));

                if ($width) {
                    $result['width'] = $width;
                }
                if ($height) {
                    $result['height'] = $height;
                }
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Framework\Config\View $subject
     * @param array $result
     * @param string $module
     * @param string $mediaType
     * @return array
     */
    public function afterGetMediaEntities(\Magento\Framework\Config\View $subject, array $result, $module, $mediaType)
    {
        if (($module == 'Magento_Catalog')) {
            foreach ($result as $mediaId => &$options) {
                /** Big image size overwrite */
                if (($mediaId == 'product_page_image_medium') || ($mediaId == 'product_page_image_medium_no_frame')) {
                    $width = trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_IMAGE_MAIN_WIDTH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
                    $height = trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_IMAGE_MAIN_HEIGHT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));

                    if ($width) {
                        $options['width'] = $width;
                    }
                    if ($height) {
                        $options['height'] = $height;
                    }
                }

                /** Thumb image size overwrite */
                if ($mediaId == 'product_page_image_small') {
                    $width = trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_IMAGE_THUMB_WIDTH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
                    $height = trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_IMAGE_THUMB_HEIGHT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));

                    if ($width) {
                        $options['width'] = $width;
                    }
                    if ($height) {
                        $options['height'] = $height;
                    }
                }
            }
        }
        return $result;
    }
}
