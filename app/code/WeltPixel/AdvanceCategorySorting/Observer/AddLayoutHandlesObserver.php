<?php
namespace WeltPixel\AdvanceCategorySorting\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddLayoutHandlesObserver implements ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * AddLayoutHandlesObserver constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $isEnabled = $this->scopeConfig->getValue(
            'weltpixel_advance_category_sorting/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );

        $layout = $observer->getData('layout');
        $currentHandles = $layout->getUpdate()->getHandles();

        $needle = [
            'catalog_category_view',
            'catalog_category_view_type_layered',
            'catalogsearch_result_index'
        ];

        if ($isEnabled && array_intersect($needle, $currentHandles)) {
            $layout->getUpdate()->addHandle('weltpixel_acs');
        }

        return $this;
    }
}
