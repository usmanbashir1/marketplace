<?php

namespace WeltPixel\CategoryPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ShowAddToCart
 *
 * @package WeltPixel\CategoryPage\Model\Config\Source
 */
class ShowAddToCart implements ArrayInterface
{

    /**
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '0',
                'label' => 'Hidden',
            ],
            [
                'value' => '1',
                'label' => 'Image Bottom',
            ],
            [
                'value' => '2',
                'label' => 'Image Bottom On Hover',
            ],
            [
                'value' => '3',
                'label' => 'Below Price', //2
            ],
            [
                'value' => '4',
                'label' => 'Below Price On Hover',
            ],
            [
                'value' => '5',
                'label' => 'Below Swatches on Hover', //3
            ]
        ];
    }
}