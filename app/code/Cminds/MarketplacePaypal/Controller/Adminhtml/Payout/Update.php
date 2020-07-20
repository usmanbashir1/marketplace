<?php

namespace Cminds\MarketplacePaypal\Controller\Adminhtml\Payout;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Cminds\MarketplacePaypal\Model\PaymentStatusFactory;
use Cminds\MarketplacePaypal\Model\Payout;
use Cminds\MarketplacePaypal\Model\PaymentStatusRepository;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;

/**
 * Update controller
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class Update extends Action
{
    const ADMIN_RESOURCE = 'MarketplacePaypal::billing_report_pay_paypal';

    /**
     * @var PaymentStatusFactory
     */
    private $paymentStatusFactory;

    /**
     * @var Payout
     */
    private $payout;

    /**
     * @var PaymentStatusRepository
     */
    private $paymentStatusRepository;

    /**
     * Update constructor.
     * @param Context $context
     * @param PaymentStatusFactory $paymentStatusFactory
     * @param Payout $payout
     * @param PaymentStatusRepository $paymentStatusRepository
     */
    public function __construct(
        Context $context,
        PaymentStatusFactory $paymentStatusFactory,
        Payout $payout,
        PaymentStatusRepository $paymentStatusRepository
    ) {
        parent::__construct($context);
        $this->paymentStatusFactory = $paymentStatusFactory;
        $this->paymentStatusFactory = $paymentStatusFactory;
        $this->payout = $payout;
        $this->paymentStatusRepository = $paymentStatusRepository;
    }

    /**
     * Update
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/grid');

        try {
            $payoutId = $this->getRequest()->getParam('entity_id');
            $payoutStatus = $this->paymentStatusRepository->getById($payoutId);
            $payoutStatus = $this->payout->updateStatus($payoutStatus);
            $this->paymentStatusRepository->save($payoutStatus);
            $this->messageManager->addSuccessMessage(__('Payout payment status was updated.'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(
                __('There was an error during paypal payment processing: "%1".',
                $e->getMessage())
            );
        }

        return $resultRedirect;
    }
}
