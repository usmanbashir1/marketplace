<?php

namespace Cminds\SupplierSubscription\Block\Supplier\Plan;

use Cminds\SupplierSubscription\Block\Supplier\Plan;

class Renew extends Plan
{
    /**
     * Get available months of plan subscriptions.
     *
     * @return array
     */
    public function getAvailableMonths()
    {
        return [1, 3, 6, 12];
    }

    /**
     * Get hashed sku.
     *
     * @param string $sku
     * @param int $size
     *
     * @return string
     */
    public function getHash($sku, $size = 5)
    {
        return substr(md5($sku), 0, $size);
    }
}
