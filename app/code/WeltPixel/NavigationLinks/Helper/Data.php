<?php

namespace WeltPixel\NavigationLinks\Helper;

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
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null) {
        return $this->scopeConfig->getValue(
            'weltpixel_megamenu/megamenu/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );


    }

    /**
     * @param null $storeId
     * @return int|mixed
     */
    public function getWidthThreshold($storeId = null)
    {
        if (!$storeId) $storeId = $this->getStoreId();

        $widthThreshold = (int) $this->scopeConfig->getValue(
            'weltpixel_megamenu/megamenu/width_threshold',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $widthThreshold = trim($widthThreshold) ? (int) $widthThreshold : 767;
        $mobileThreshold = $this->getMobileThreshold() - 1;

        if ($mobileThreshold > $widthThreshold) {
            return $mobileThreshold;
        }

        return $widthThreshold;
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getMobileThreshold($storeId = null)
    {
        if (
            $this->isModuleEnabled('WeltPixel_FrontendOptions') &&
            $this->isOutputEnabled('WeltPixel_FrontendOptions')
        ) {
            if (!$storeId) $storeId = $this->getStoreId();

            return (int) $this->scopeConfig->getValue(
                'weltpixel_frontend_options/breakpoints/screen__m',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        return 768;
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
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}