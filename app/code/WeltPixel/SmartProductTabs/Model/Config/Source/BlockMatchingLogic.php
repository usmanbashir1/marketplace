<?php

namespace WeltPixel\SmartProductTabs\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class BlockMatchingLogic
 * @package WeltPixel\SmartProductTabs\Model\Config\Source
 */
class BlockMatchingLogic implements ArrayInterface
{
    /**
     * Return list of Attributes
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $arr = [
            [
                'value' => 0,
                'label' => __('Default Admin')
            ],
            [
                'value' => 1,
                'label' => __('Store View')
            ]
        ];

        return $arr;
    }
}
