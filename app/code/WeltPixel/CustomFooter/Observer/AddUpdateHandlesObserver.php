<?php
namespace WeltPixel\CustomFooter\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddUpdateHandlesObserver implements ObserverInterface
{
    const XML_PATH_CUSTOMFOOTER_COPYRIGHT_ENABLED = 'weltpixel_custom_footer/copyright/enable';

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
     * Add Custom layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $layout = $observer->getData('layout');

        /** Apply only on pages where page is rendered */
        $currentHandles = $layout->getUpdate()->getHandles();
        if (!in_array('default', $currentHandles)) {
            return $this;
        }

        $isCopyrightEnabled = $this->scopeConfig->getValue(self::XML_PATH_CUSTOMFOOTER_COPYRIGHT_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$isCopyrightEnabled) {
            $layout->getUpdate()->addHandle('weltpixel_customfooter_removecopyright');
        }

        return $this;
    }
}