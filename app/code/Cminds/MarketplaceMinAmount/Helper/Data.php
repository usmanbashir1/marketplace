<?php

namespace Cminds\MarketplaceMinAmount\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Data Helper
 *
 * @category Cminds
 * @package  Cminds_MarketplaceMinAmount
 * @author   Cminds Core Team <info@cminds.com>
 */
class Data extends AbstractHelper
{
    /**
     * Rest constructor.
     * @param Context $context
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Is module enabled
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        $value = (int)$this->scopeConfig->getValue(
            'cminds_marketplaceminamount/general/module_enabled'
        );

        return $value === 1;
    }

    /**
     * Convert decimal qty to int
     *
     * @param $qty
     * @return bool|string
     */
    public function convertMinOrderQty($qty)
    {
        $delimiters = [",","."];
        $ready = str_replace($delimiters, $delimiters[0], $qty);
        $launch = explode($delimiters[0], $ready);
        if (!isset($launch[1]) || $launch[1] == 0) {
            $qty = number_format((float)$qty, 0);
        }

        return $qty;
    }
}
