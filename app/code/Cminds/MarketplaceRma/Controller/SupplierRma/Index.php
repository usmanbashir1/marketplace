<?php

namespace Cminds\MarketplaceRma\Controller\SupplierRma;

use Cminds\Supplierfrontendproductuploader\Controller\AbstractController;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Index
 *
 * @package Cminds\MarketplaceRma\Controller\SupplierRma
 */
class Index extends AbstractController
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * Index constructor.
     *
     * @param Context               $context
     * @param Data                  $helper
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface  $scopeConfig
     * @param CustomerSession       $customerSession
     * @param ModuleConfig          $moduleConfig
     */
    public function __construct(
        Context $context,
        Data $helper,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        CustomerSession $customerSession,
        ModuleConfig $moduleConfig
    ) {
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;

        parent::__construct(
            $context,
            $helper,
            $storeManager,
            $scopeConfig
        );
    }

    /**
     * Execute method.
     *
     * @return \Magento\Framework\App\ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if ($this->helper->isSupplier($this->customerSession->getCustomerId()) === false) {
            $this->messageManager->addErrorMessage(__('Something went wrong.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }

        if ($this->moduleConfig->isActive() === false) {
            $this->messageManager->addErrorMessage(__('MarketplaceRma is currently disabled in configuration'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }

        return parent::execute();
    }
}
