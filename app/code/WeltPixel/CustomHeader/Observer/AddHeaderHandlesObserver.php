<?php
namespace WeltPixel\CustomHeader\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddHeaderHandlesObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \WeltPixel\Backend\Helper\Utility
     */
    protected $utilityHelper;

    const XML_PATH_CUSTOMHEADER_STYLE = 'weltpixel_custom_header/general/header_style';

    /**
     * AddHeaderHandlesObserver constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \WeltPixel\Backend\Helper\Utility $utilityHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \WeltPixel\Backend\Helper\Utility $utilityHelper
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->utilityHelper = $utilityHelper;
    }

    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** If not pearl theme is used ignore the header version setting and don't load the custom layouts */
        if (!$this->utilityHelper->isPearlThemeUsed()) {
            return $this;
        }

        $customHeaderStyle = $this->scopeConfig->getValue(self::XML_PATH_CUSTOMHEADER_STYLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $layout = $observer->getData('layout');

        /** Apply only on pages where page is rendered */
        $currentHandles = $layout->getUpdate()->getHandles();
        if (!in_array('default', $currentHandles)) {
            return $this;
        }

        switch ($customHeaderStyle) {
            case 'v1':
                $layout->getUpdate()->addHandle('weltpixel_custom_header_v1');
                break;
            case 'v2':
                $layout->getUpdate()->addHandle('weltpixel_custom_header_v2');
                break;
            case 'v3':
                $layout->getUpdate()->addHandle('weltpixel_custom_header_v3');
                break;
            case 'v4':
                $layout->getUpdate()->addHandle('weltpixel_custom_header_v4');
                break;
            default :
                break;
        }

        return $this;
    }
}
