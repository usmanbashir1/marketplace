<?php

namespace Cminds\MarketplacePaypal\Model\Config\Source\Transfer;

use Magento\Framework\Option\ArrayInterface;

/**
 * Type
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class Type implements ArrayInterface
{
    const MANUAL = 1;
    const ORDER_PLACE = 2;
    const ORDER_STATUS_COMPLETE = 3;

    /**
     * Return all transfer types.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => self::MANUAL, 'label' => __('Manual')],
            ['value' => self::ORDER_PLACE, 'label' =>__('After order place')],
            ['value' => self::ORDER_STATUS_COMPLETE, 'label' =>__('When order status is changed to complete')]
        ];

        return $options;
    }
}
