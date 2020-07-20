<?php

namespace Cminds\MarketplacePaypal\Model;

/**
 * Payment Interface
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
interface PaymentInterface
{
    /**
     * Pay
     *
     * @param int $supplierId
     * @param float $amount
     * @param int $orderId
     * @return mixed
     */
    public function pay(int $supplierId, float $amount, int $orderId);
}
