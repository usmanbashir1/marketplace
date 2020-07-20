<?php

namespace WeltPixel\CategoryPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class DisplaySwatche
 *
 * @package WeltPixel\CategoryPage\Model\Config\Source
 */
class DisplaySwatche implements ArrayInterface
{

    /**
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '0',
                'label' => 'No',
            ],
            [
                'value' => '1',
                'label' => 'Yes',
            ],
            [
                'value' => '2',
                'label' => 'On Hover',
            ]
        ];
    }
}