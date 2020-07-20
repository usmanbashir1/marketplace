<?php

namespace Cminds\MarketplacePaypal\Model\Service\Order;

use Cminds\MarketplacePaypal\Model\Payout;
use Cminds\Marketplace\Model\PaymentFactory;
use Cminds\Marketplace\Model\ResourceModel\Payment as PaymentResource;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;

/**
 * Order Payment
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class Payment
{
    /**
     * @var Payout
     */
    private $payout;

    /**
     * @var PaymentFactory
     */
    private $paymentFactory;

    /**
     * @var PaymentResource
     */
    private $paymentResource;

    /**
     * Payment constructor.
     * @param Payout $payout
     * @param PaymentFactory $paymentFactory
     * @param PaymentResource $paymentResource
     */
    public function __construct(
        Payout $payout,
        PaymentFactory $paymentFactory,
        PaymentResource $paymentResource
    ) {
        $this->payout = $payout;
        $this->paymentFactory = $paymentFactory;
        $this->paymentResource = $paymentResource;
    }

    /**
     * Process Order Payments
     *
     * @param Order $order
     * @return $this
     * @throws LocalizedException
     */
    public function processOrderPayments($order)
    {
        $orderId = $order->getId();
        $payments = $this->paymentResource->getPaymentsByOrderId($orderId);

        foreach ($payments as $payment) {
            $supplierId = $payment->getSupplierId();
            $vendorIncodeAmount = (float)$payment->getTotalVendorIncome();
            $paidAmount = (float)$payment->getTotalPaidAmount();
            $amount = $vendorIncodeAmount - $paidAmount;

            if ($amount <= 0.0) {
                continue;
            }

            $this->payout->pay(
                $supplierId,
                $amount,
                $orderId
            );
        }

        return $this;
    }
}
