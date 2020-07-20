<?php
namespace WeltPixel\Multistore\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddUpdateHandlesObserver implements ObserverInterface
{
    const XML_PATH_MULTISTORE_ENABLED = 'weltpixel_multistore/general/enable';
    const XML_PATH_MULTISTORE_ONEROW = 'weltpixel_multistore/general/one_row';

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
     * Add Custom QuickCart layout handle
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

        $isEnabled = $this->scopeConfig->getValue(self::XML_PATH_MULTISTORE_ENABLED,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $showInOneRow = $this->scopeConfig->getValue(self::XML_PATH_MULTISTORE_ONEROW,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($isEnabled) {
            $layout->getUpdate()->addHandle('weltpixel_multistore');
        }
        if ($showInOneRow) {
            $layout->getUpdate()->addHandle('weltpixel_multistore_onerow');
        }
        
        return $this;
    }
}
