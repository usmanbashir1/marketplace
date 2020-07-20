<?php
namespace WeltPixel\CustomFooter\Helper;

/**
 * Class Data
 * @package WeltPixel\CustomFooter\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const FOOTER_VERSION_PATH = 'weltpixel_custom_footer/footer/version';
    const FOOTER_PREFIX = 'weltpixel_footer_';


    /**
     * @return string
     */
    public function getFooterVersion()
    {
        $footerVersion = $this->scopeConfig->getValue(self::FOOTER_VERSION_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	    return self::FOOTER_PREFIX . $footerVersion;
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getPreFooterBackgroundColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_footer/prefooter/background_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getPreFooterTextColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_footer/prefooter/text_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getPreFooterIconColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_footer/prefooter/icon_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getFooterBackgroundColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_footer/footer/background_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getFooterTextColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_footer/footer/text_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getFooterIconColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_footer/footer/icon_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
}
