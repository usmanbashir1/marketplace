<?php

namespace Cminds\MarketplacePaypal\Controller\Adminhtml\Billing;

use Cminds\MarketplacePaypal\Model\Config as ModuleConfig;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Cminds\MarketplacePaypal\Model\Payout;
use \Cminds\MarketplacePaypal\Helper\Rest;

/**
 * Cminds MarketplacePaypal billing report paypal pay post action.
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class PayPost extends Action
{
    const ADMIN_RESOURCE = 'Cminds_MarketplacePaypal::billing_pay';

    private $moduleConfig;
    private $payoutPayment;
    private $helper;

    public function __construct(
        Context $context,
        ModuleConfig $moduleConfig,
        Payout $payoutPayment,
        Rest $helper
    ) {
        parent::__construct($context);

        $this->moduleConfig = $moduleConfig;
        $this->payoutPayment = $payoutPayment;
        $this->helper = $helper;
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('marketplace/billing/index');

        $post = $this->getRequest()->getParams();
        if (empty($post)) {
            $this->messageManager->addErrorMessage(
                __('There was an error during paypal payment creation.')
            );

            return $resultRedirect->setPath('*/*/index');
        }

        $orderId = $post['order_id'];
        $supplierId = $post['supplier_id'];

        if ($this->moduleConfig->isActive() === false) {
            $this->messageManager->addErrorMessage(
                __('Can not create paypal payment, module is disabled.')
            );

            return $resultRedirect->setPath(
                '*/*/pay',
                ['order_id' => $orderId, 'supplier_id' => $supplierId]
            );
        }

        $amount = $post['amount'];

        try {
            $this->payoutPayment->pay($supplierId, $amount, $orderId);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                __(
                    'There was an error during paypal payment processing: "%1".',
                    $e->getMessage()
                )
            );

            return $resultRedirect->setPath(
                '*/*/pay',
                ['order_id' => $orderId, 'supplier_id' => $supplierId]
            );
        }

        $this->messageManager->addSuccessMessage(
            __('Paypal payout has been successfully created. You can monitor it status now')
        );

        return $resultRedirect;
    }
}
