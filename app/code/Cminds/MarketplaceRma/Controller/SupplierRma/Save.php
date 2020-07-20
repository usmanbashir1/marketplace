<?php

namespace Cminds\MarketplaceRma\Controller\SupplierRma;

use Cminds\Supplierfrontendproductuploader\Controller\AbstractController;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Cminds\MarketplaceRma\Model\Rma;
use Cminds\MarketplaceRma\Model\NoteFactory;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Helper\Email as EmailHelper;
use Cminds\MarketplaceRma\Helper\Data as RmaHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Save
 *
 * @package Cminds\MarketplaceRma\Controller\SupplierRma
 */
class Save extends AbstractController
{
    const PATH_MARKETPLACERMA_SUPPLIERRMA_INDEX = 'marketplacerma/supplierrma/index';
    /**
     * @var Product
     */
    private $product;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var Rma
     */
    private $rma;

    /**
     * @var RmaHelper
     */
    private $rmaHelper;

    /**
     * @var EmailHelper
     */
    private $emailHelper;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var NoteFactory
     */
    private $noteFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * Save constructor.
     *
     * @param Context                $context
     * @param Data                   $helper
     * @param Product                $product
     * @param Registry               $registry
     * @param Rma                    $rma
     * @param EmailHelper            $emailHelper
     * @param RmaHelper              $rmaHelper
     * @param CustomerSession        $customerSession
     * @param StoreManagerInterface  $storeManager
     * @param ScopeConfigInterface   $scopeConfig
     * @param ModuleConfig           $moduleConfig
     * @param NoteFactory            $noteFactory
     * @param DateTime               $dateTime
     */
    public function __construct(
        Context $context,
        Data $helper,
        Product $product,
        Registry $registry,
        Rma $rma,
        EmailHelper $emailHelper,
        RmaHelper $rmaHelper,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ModuleConfig $moduleConfig,
        NoteFactory $noteFactory,
        DateTime $dateTime
    ) {
        parent::__construct(
            $context,
            $helper,
            $storeManager,
            $scopeConfig
        );

        $this->emailHelper = $emailHelper;
        $this->product = $product;
        $this->rma = $rma;
        $this->rmaHelper = $rmaHelper;
        $this->registry = $registry;
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
        $this->noteFactory = $noteFactory;
        $this->dateTime = $dateTime;
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
            $this->messageManager->addErrorMessage(__('MarketplaceRma is currently disabled in configuration'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }

        $data = $this->getRequest()->getParams();
        if (isset($data['id'])) {
            $model = $this->rma->load($data['id']);
        } else {
            return $this->customerSession->authenticate();
        }

        $model->setData('status', $data['status']);

        if (isset($data['notify_customer']) && $data['notify_customer'] == 'on') {
            $data['notify_customer'] = 1;
        } else {
            $data['notify_customer'] = 0;
        }

        $currentStatus = $data['status'];
        $orderId = $model->getData('order_id');
        $isOrderInvoiced = $this->rmaHelper->checkIsOrderInvoiced($orderId);
        $isOrderReadyForCreditmemo = $this->rmaHelper->checkIsOrderReadyForCreditmemo($orderId);
        $isOrderHasCreditmemo = $this->rmaHelper->checkIsOrderHasCreditmemos($orderId);

        if ($isOrderInvoiced === false && $currentStatus == Rma::RMA_APPROVED) {
            $this->messageManager->addErrorMessage(__('To make Returns approved, order must be invoiced first'));

            return $this->_redirect(self::PATH_MARKETPLACERMA_SUPPLIERRMA_INDEX);
        }

        if ($isOrderInvoiced === true
            && $data['status'] == Rma::RMA_APPROVED
            && $isOrderReadyForCreditmemo === true
            && $isOrderHasCreditmemo === false
        ) {
            $this->rmaHelper->proceedRefund($model->getData('order_id'));
        }

        if (($model->getOrigData('status') == Rma::RMA_APPROVED) && ($data['status'] != Rma::RMA_CLOSED)) {
            $this->messageManager->addErrorMessage(__('This Returns is already approved. You can only close this Returns.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }

        if ($model->getOrigData('status') == Rma::RMA_CLOSED) {
            $this->messageManager->addErrorMessage(__('This Returns is closed. You can only view the details.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }

        $model->setData('status', $data['status']);

        try {
            if ($data['note'] != null && $data['note'] != '') {
                $note = $this->noteFactory->create();
                $note
                    ->addData([
                        'rma_id' => $model->getId(),
                        'notify_customer' => $data['notify_customer'],
                        'note' => $data['note'],
                        'created_at' => $this->dateTime->timestamp()
                    ])
                    ->save();

                $this->_eventManager->dispatch(
                    'cminds_marketplacerma_new_rma_note',
                    ['rma_model' => $model, 'notify_customer' => $data['notify_customer']]
                );
            }

            $model->save();

            if ($model->isObjectNew() === true) {
                $this->messageManager->addSuccessMessage(__('Returns successfully submitted'));
            } else {
                if ($model->getData('status') != $model->getOrigData('status')) {
                    if ($model->getData('status') == Rma::RMA_APPROVED) {
                        $this->_eventManager->dispatch(
                            'cminds_marketplacerma_rma_approval',
                            ['rma_model' => $model]
                        );
                    } else {
                        $this->_eventManager->dispatch(
                            'cminds_marketplacerma_status_changed',
                            ['rma_model' => $model]
                        );
                    }
                }

                $this->messageManager->addSuccessMessage(__('Returns successfully updated.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong.'));
        }

        $url = $this->storeManager->getStore()->getUrl(self::PATH_MARKETPLACERMA_SUPPLIERRMA_INDEX);
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($url);

        return $resultRedirect;
    }
}
