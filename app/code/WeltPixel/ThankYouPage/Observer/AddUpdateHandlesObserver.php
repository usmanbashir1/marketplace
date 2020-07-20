<?php
namespace WeltPixel\ThankYouPage\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class AddUpdateHandlesObserver implements ObserverInterface
{      
    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \WeltPixel\ThankYouPage\Helper\Data
     */
    protected $_helper;

    const XML_PATH_THANKYOUPAGE_ENABLED = 'weltpixel_thankyoupage/general/enable';

    /**
     * AddUpdateHandlesObserver constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param \WeltPixel\ThankYouPage\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        \WeltPixel\ThankYouPage\Helper\Data $helper
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->_storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->_helper = $helper;
    }
    
    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $layout = $observer->getData('layout');
        $fullActionName = $observer->getData('full_action_name');

        if (!in_array($fullActionName, ['checkout_onepage_success', 'multishipping_checkout_success'])) {
            return $this;
        }

        $isThankYouPageModuleEnabled = $this->scopeConfig->getValue(self::XML_PATH_THANKYOUPAGE_ENABLED,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($isThankYouPageModuleEnabled) {
            switch ($fullActionName) {
                case 'checkout_onepage_success' :
                    $layout->getUpdate()->addHandle('weltpixel_checkout_onepage_success');
                break;
                case 'multishipping_checkout_success' :
                    $layout->getUpdate()->addHandle('weltpixel_multishipping_checkout_success');
                break;
            }

        }

        if ($this->_helper->isWesupplyModuleEnabled()) {
            $layout->getUpdate()->addHandle('weltpixel_wesupply_integration');
        }

        return $this;
    }
}
