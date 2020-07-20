<?php

namespace Cminds\MarketplaceRma\Controller\SupplierRma;

use Cminds\Supplierfrontendproductuploader\Controller\AbstractController;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Cminds\MarketplaceRma\Model\Rma;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Model\ResourceModel\Note\Collection as NoteCollection;
use Cminds\MarketplaceRma\Model\ResourceModel\CustomerAddress\Collection as CustomerAddressCollection;
use Cminds\MarketplaceRma\Model\ResourceModel\ReturnProduct\Collection as ReturnProductCollection;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\ResultInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Delete
 *
 * @package Cminds\MarketplaceRma\Controller\SupplierRma
 */
class Delete extends AbstractController
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var Rma
     */
    private $rma;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var CustomerAddressCollection
     */
    private $customerAddressCollection;

    /**
     * @var NoteCollection
     */
    private $noteCollection;

    /**
     * @var ReturnProductCollection
     */
    private $returnProductCollection;

    /**
     * Delete constructor.
     *
     * @param Context                   $context
     * @param Data                      $helper
     * @param Rma                       $rma
     * @param CustomerSession           $customerSession
     * @param StoreManagerInterface     $storeManager
     * @param ScopeConfigInterface      $scopeConfig
     * @param ModuleConfig              $moduleConfig
     * @param CustomerAddressCollection $customerAddressCollection
     * @param NoteCollection            $noteCollection
     * @param ReturnProductCollection   $returnProductCollection
     */
    public function __construct(
        Context $context,
        Data $helper,
        Rma $rma,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ModuleConfig $moduleConfig,
        CustomerAddressCollection $customerAddressCollection,
        NoteCollection $noteCollection,
        ReturnProductCollection $returnProductCollection
    ) {
        parent::__construct(
            $context,
            $helper,
            $storeManager,
            $scopeConfig
        );

        $this->rma = $rma;
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
        $this->customerAddressCollection = $customerAddressCollection;
        $this->noteCollection = $noteCollection;
        $this->returnProductCollection = $returnProductCollection;
    }

    /**
     * Execute method.
     *
     * @return bool|\Magento\Framework\App\ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $this->customerSession->authenticate();
        }

        if ($this->helper->isSupplier($this->customerSession->getCustomerId()) === false) {
            $this->messageManager->addErrorMessage(__('Something went wrong.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }

        if ($this->moduleConfig->isActive() === false) {
            $this->messageManager->addErrorMessage(__('MarketplaceRma is currently disabled in configuration.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }

        if ($this->moduleConfig->getCanVendorDeleteRma() === false) {
            $this->messageManager->addErrorMessage(__('Deleting by vendor is currently disabled in configuration.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }

        $data = $this->getRequest()->getParams();

        if (isset($data['id']) == false) {
            $this->messageManager->addErrorMessage(__('Something went wrong.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        } elseif (isset($data['id']) === true) {
            try {
                $model = $this->rma->load($data['id']);

                if ($model->getData('status') == Rma::RMA_APPROVED
                    || $model->getData('status') == Rma::RMA_CLOSED
                ) {
                    $this->messageManager->addErrorMessage(
                        __('This Returns is already processed. You can not delete this request.')
                    );

                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                    return $resultRedirect;
                }

                // delete related customer address
                $customerAddressCollection = $this->customerAddressCollection
                    ->addFieldToFilter('rma_id', $model->getData('id'))
                    ->getItems();

                foreach ($customerAddressCollection as $item) {
                    $item->delete();
                }

                // deleted related notes
                $noteCollection = $this->noteCollection
                    ->addFieldToFilter('rma_id', $model->getData('id'))
                    ->getItems();

                foreach ($noteCollection as $item) {
                    $item->delete();
                }

                // delete related products
                $returnProductCollection = $this->returnProductCollection
                    ->addFieldToFilter('order_id', $model->getData('order_id'))
                    ->getItems();

                foreach ($returnProductCollection as $item) {
                    $item->delete();
                }

                // delete main model
                $model->delete();

                $this->messageManager->addSuccessMessage(__('Returns deleted successful.'));
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                return $resultRedirect;

            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage(__('Something went wrong.'));
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                return $resultRedirect;
            }
        }

        $this->messageManager->addErrorMessage(__('Something went wrong.'));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}
