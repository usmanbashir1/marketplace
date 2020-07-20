<?php

declare(strict_types=1);

namespace Cminds\MarketplaceMinAmount\Model\Config\Source;

/**
 * Minimum Amount options model
 *
 * @category Cminds
 * @package  MarketplaceMinAmount
 * @author   Cminds Core Team <info@cminds.com>
 */
class MinimumAmount extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const NONE = 0;
    const ORDER = 1;
    const DAY = 2;

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $arr = [
            ['value' => self::NONE, 'label' => __('None')],
            ['value' => self::ORDER, 'label' => __('Order')],
            ['value' => self::DAY, 'label' => __('Day')]
        ];
        $ret = [];

        foreach ($arr as $a) {
            $ret[] = [
                'value' => $a['value'],
                'label' => $a['label'],
            ];
        }

        return $ret;
    }

    public function toOptionArray()
    {
        $options = [
            ['value' => self::NONE, 'label' => __('None')],
            ['value' => self::ORDER, 'label' => __('Order')],
            ['value' => self::DAY, 'label' => __('Day')]
        ];

        return $options;
    }
}
