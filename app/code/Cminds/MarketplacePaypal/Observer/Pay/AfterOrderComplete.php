<?php

namespace Cminds\MarketplacePaypal\Observer\Pay;

use Cminds\MarketplacePaypal\Model\Config as ModuleConfig;
use Cminds\MarketplacePaypal\Model\Config\Source\Transfer\Type;
use Cminds\MarketplacePaypal\Model\Service\Order\Payment as PaypalOrderPayment;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/**
 * Cminds MarketplacePaypal after order save observer.
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class AfterOrderComplete implements ObserverInterface
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var PaypalOrderPayment
     */
    private $paypalOrderPayment;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AfterOrderPlace constructor.
     *
     * @param ModuleConfig       $moduleConfig
     * @param PaypalOrderPayment $paypalOrderPayment
     * @param LoggerInterface    $logger
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        PaypalOrderPayment $paypalOrderPayment,
        LoggerInterface $logger
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->paypalOrderPayment = $paypalOrderPayment;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     *
     * @return AfterOrderComplete
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isActive() === false) {
            return $this;
        }

        $transferType = $this->moduleConfig->getTransferType();
        if ($transferType !== Type::ORDER_STATUS_COMPLETE) {
            return $this;
        }

        $order = $observer->getEvent()->getOrder();
        if ($order === null) {
            return $this;
        }
        if ($order->getState() !== Order::STATE_COMPLETE) {
            return $this;
        }

        $this->processOrderPayments($order);

        return $this;
    }

    /**
     * @param $order
     *
     * @return AfterOrderComplete
     */
    private function processOrderPayments($order)
    {
        try {
            $this->paypalOrderPayment->processOrderPayments($order);
        } catch (\Exception $e) {
            $this->logger->info($e);
        }

        return $this;
    }
}
