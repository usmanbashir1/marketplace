<?php

namespace Cminds\SupplierInventoryUpdate\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Cminds SupplierInventoryUpdate Config Source class.
 *
 * @category Cminds
 * @package  Cminds_SupplierInventoryUpdate
 * @author   Mateusz Niziolek
 */
class Action extends AbstractSource
{
    const DO_NOTHING = 0;
    const SET_OUT_OF_STOCK = 1;

    public function getAllOptions()
    {
        $options = [
            ['value' => self::DO_NOTHING, 'label' => __('Do nothing')],
            [
                'value' => self::SET_OUT_OF_STOCK,
                'label' => __('Set out of Stock'),
            ],
        ];

        foreach ($options as $option) {
            $this->_options[] = [
                'label' => $option['label'],
                'value' => $option['value'],
            ];
        }

        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
