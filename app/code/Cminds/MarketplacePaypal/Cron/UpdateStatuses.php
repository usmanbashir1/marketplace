<?php

namespace Cminds\MarketplacePaypal\Cron;

use Cminds\MarketplacePaypal\Model\PaymentStatusRepository;
use Cminds\MarketplacePaypal\Model\Source\PayoutStatus;
use Cminds\MarketplacePaypal\Model\Payout;

/**
 * Update Status Cron
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class UpdateStatuses
{
    /**
     * @var PaymentStatusRepository
     */
    private $paymentStatusRepository;

    /**
     * @var Payout
     */
    private $payout;

    /**
     * UpdateStatuses constructor.
     * @param PaymentStatusRepository $paymentStatusRepository
     * @param Payout $payout
     */
    public function __construct(
        PaymentStatusRepository $paymentStatusRepository,
        Payout $payout
    ) {
        $this->paymentStatusRepository = $paymentStatusRepository;
        $this->payout = $payout;
    }

    /**
     * Execute cron
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function execute()
    {
        $pendingPayments = $this->paymentStatusRepository->getList(PayoutStatus::PENDING_STATUSES);
        foreach ($pendingPayments as $paymentStatus) {
            $this->payout->updateStatus($paymentStatus);
            $this->paymentStatusRepository->save($paymentStatus);
        }
    }
}