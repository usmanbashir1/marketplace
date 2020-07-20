<?php

namespace Cminds\MarketplaceQa\Controller\Questions;

use Cminds\Marketplace\Helper\Data as MarketplaceHelper;
use Cminds\MarketplaceQa\Helper\Data as MarketplaceQaHelper;
use Cminds\MarketplaceQa\Helper\EmailSender;
use Cminds\MarketplaceQa\Model\Qa;
use Cminds\Supplierfrontendproductuploader\Controller\AbstractController;
use Cminds\Supplierfrontendproductuploader\Helper\Data as SupplierHelper;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Event\Manager;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;

class Save extends AbstractController
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @var MarketplaceHelper
     */
    private $marketplaceHelper;

    /**
     * @var InvoiceSender
     */
    private $invoiceSender;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var Qa
     */
    private $qa;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var MarketplaceQaHelper
     */
    private $marketplaceQaHelper;

    /**
     * @var EmailSender
     */
    private $emailSender;

    /**
     * @var Manager
     */
    private $eventManager;

    /**
     * Save constructor.
     *
     * @param Context               $context
     * @param Transaction           $transaction
     * @param MarketplaceHelper     $marketplaceHelper
     * @param SupplierHelper        $supplierHelper
     * @param CustomerSession       $customerSession
     * @param OrderFactory          $orderFactory
     * @param InvoiceSender         $invoiceSender
     * @param ItemFactory           $itemFactory
     * @param StoreManagerInterface $storeManager
     * @param Qa                    $qa
     * @param DateTime              $dateTime
     * @param MarketplaceQaHelper   $marketplaceQaHelper
     * @param EmailSender           $emailSender
     * @param ScopeConfigInterface  $scopeConfig
     * @param Manager               $eventManager
     */
    public function __construct(
        Context $context,
        Transaction $transaction,
        MarketplaceHelper $marketplaceHelper,
        SupplierHelper $supplierHelper,
        CustomerSession $customerSession,
        OrderFactory $orderFactory,
        InvoiceSender $invoiceSender,
        ItemFactory $itemFactory,
        StoreManagerInterface $storeManager,
        Qa $qa,
        DateTime $dateTime,
        MarketplaceQaHelper $marketplaceQaHelper,
        EmailSender $emailSender,
        ScopeConfigInterface $scopeConfig,
        Manager $eventManager,
        RequestInterface $request
    ) {
        parent::__construct(
            $context,
            $supplierHelper,
            $storeManager,
            $scopeConfig
        );

        $this->transaction = $transaction;
        $this->orderFactory = $orderFactory;
        $this->itemFactory = $itemFactory;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->invoiceSender = $invoiceSender;
        $this->customerSession = $customerSession;
        $this->qa = $qa;
        $this->dateTime = $dateTime;
        $this->marketplaceQaHelper = $marketplaceQaHelper;
        $this->emailSender = $emailSender;
        $this->eventManager = $eventManager;
        $this->request = $request;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $post = $this->request->getParams();

        if ($post) {
            try {
                if (isset($post['id']) && $post['id'] != '') {
                    $model = $this->qa->load($post['id']);

                    if (isset($post['visible_on_frontend'])) {
                        $post['visible_on_frontend'] = true;
                    } else {
                        $post['visible_on_frontend'] = false;
                    }

                    if ($model->getId()) {
                        $model
                            ->setData($post)
                            ->save();
                    }

                    if ($model->isObjectNew() === true) {
                        $this->eventManager->dispatch(
                            'cminds_marketplaceqa_new_product_question',
                            ['qa_model' => $model]
                        );
                    }

                    $this->messageManager->addSuccessMessage('Your question has been submitted.');
                    $this->sendEmail($post, $model);

                    return $this->_redirect('marketplaceqa/questions/index/');
                } else {
                    $model = $this->qa;

                    if ($this->marketplaceQaHelper->adminApprovalRequired()) {
                        $post['visible_on_frontend'] = false;
                    } else {
                        $post['visible_on_frontend'] = true;
                    }

                    if ($post['customer_id'] == '') {
                        $post['customer_id'] = null;
                    }

                    $post['approved'] = false;
                    $post['created_at'] = $this->dateTime->date('Y-m-d H:i:s');

                    $model
                        ->setData($post)
                        ->save();

                    if ($model->isObjectNew() === true) {
                        $this->eventManager->dispatch(
                            'cminds_marketplaceqa_new_product_question',
                            ['qa_model' => $model]
                        );
                    }

                    $this->messageManager->addSuccessMessage('Your question has been submitted.');
                    $this->sendEmail($post, $model);

                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                    return $resultRedirect;
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            return $this->_redirect('*/*/');
        }
    }

    /**
     * Send email.
     *
     * @param $post
     * @param $model
     */
    private function sendEmail($post, $model)
    {
        $canSendEmailAboutQuestion = $this->marketplaceQaHelper->notifyCustomerWhenQuestionWasSent();
        if ($canSendEmailAboutQuestion === true && $model->isObjectNew() === true) {
            if (isset($post['question'])) {
                $question = $post['question'];
                $this->emailSender->prepareEmail($question, $model);
            }
        }

        $canSendEmailAboutAnswer = $this->marketplaceQaHelper->notifyCustomerWhenAnswerWasPlaced();
        if ($canSendEmailAboutAnswer === true && isset($post['answer']) === true) {
            $answer = $post['answer'];
            $question = $model->getOrigData('question');
            $this->emailSender->prepareEmail($question, $model, false, false, $answer);
        }
    }
}
