<?php

namespace Cminds\MarketplacePaypal\Observer\Pay;

use Cminds\MarketplacePaypal\Model\Config as ModuleConfig;
use Cminds\MarketplacePaypal\Model\Config\Source\Transfer\Type;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Cminds\MarketplacePaypal\Model\Service\Order\Payment as PaypalOrderPayment;

/**
 * Cminds MarketplacePaypal after order place observer.
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class AfterOrderPlace implements ObserverInterface
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
     * @return AfterOrderPlace
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isActive() === false) {
            return $this;
        }

        $transferType = $this->moduleConfig->getTransferType();
        if ($transferType !== Type::ORDER_PLACE) {
            return $this;
        }

        $order = $observer->getEvent()->getOrder();
        if ($order === null) {
            return $this;
        }

        $this->processOrderPayments($order);
        return $this;
    }

    /**
     * @param $order
     *
     * @return AfterOrderPlace
     */
    private function processOrderPayments($order)
    {
        try {
            $this->paypalOrderPayment->processOrderPayments($order);
        } catch(\Exception $e) {
            $this->logger->info($e);
        }

        return $this;
    }
}
