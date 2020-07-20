<?php

namespace Cminds\MarketplaceRma\Controller\SupplierRma;

use Cminds\Supplierfrontendproductuploader\Controller\AbstractController;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class View
 *
 * @package Cminds\MarketplaceRma\Controller\SupplierRma
 */
class View extends AbstractController
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * View constructor.
     *
     * @param Context               $context
     * @param Data                  $helper
     * @param Registry              $registry
     * @param CustomerSession       $customerSession
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface  $scopeConfig
     * @param ModuleConfig          $moduleConfig
     */
    public function __construct(
        Context $context,
        Data $helper,
        Registry $registry,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ModuleConfig $moduleConfig
    ) {
        parent::__construct(
            $context,
            $helper,
            $storeManager,
            $scopeConfig
        );

        $this->helper = $helper;
        $this->registry = $registry;
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Execute method.
     *
     * @return bool|\Magento\Framework\App\ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if (!$this->canAccess()) {
            return $this->customerSession->authenticate();
        }

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

        $rmaId = $this->getRequest()->getParam('id');

        $this->registry->register('rma_view_id', $rmaId);

        $this->_view->loadLayout();
        $this->renderBlocks();
        $this->_view->renderLayout();
    }
}
