<?php

namespace Cminds\MarketplacePaypal\Controller\Adminhtml\Billing;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Cminds\Marketplace\Model\ResourceModel\Payment as PaymentResource;
use Magento\Framework\Registry as CoreRegistry;

/**
 * Cminds MarketplacePaypal billing report paypal pay action.
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Pay extends Action
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MarketplacePaypal::billing_report_pay_paypal';

    /**
     * Page factory object.
     *
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * Payment resource object.
     *
     * @var PaymentResource
     */
    private $paymentResource;

    /**
     * Core registry object.
     *
     * @var CoreRegistry
     */
    private $coreRegistry;

    /**
     * @param Context         $context
     * @param PageFactory     $resultPageFactory
     * @param PaymentResource $paymentResource
     * @param CoreRegistry    $coreRegistry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PaymentResource $paymentResource,
        CoreRegistry $coreRegistry
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->paymentResource = $paymentResource;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Billing paypal pay action.
     *
     * @return Page
     * @throws \RuntimeException
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Cminds_Marketplace::billing')
            ->addBreadcrumb(__('Billing Report'), __('Billing Report'))
            ->addBreadcrumb(__('Create Paypal Payment'), __('Create Paypal Payment'))
            ->getConfig()->getTitle()->prepend(__('Create Paypal Payment'));

        $orderId = $this->getRequest()->getParam('order_id');
        $supplierId = $this->getRequest()->getParam('supplier_id');

        $payment = $this->paymentResource
            ->getSupplierPaymentByOrderId($supplierId, $orderId);

        $this->coreRegistry->register(
            'marketplacepaypal_billing_payment',
            $payment
        );

        return $resultPage;
    }
}
