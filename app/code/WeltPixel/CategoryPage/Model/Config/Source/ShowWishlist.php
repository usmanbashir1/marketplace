<?php

namespace WeltPixel\CategoryPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ShowWishlist
 *
 * @package WeltPixel\CategoryPage\Model\Config\Source
 */
class ShowWishlist implements ArrayInterface
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
                'label' => 'Top - Left', // 1
            ],
            [
                'value' => '2',
                'label' => 'Top - Left On Hover',
            ],
            [
                'value' => '3',
                'label' => 'Top - Right', // 2
            ],
            [
                'value' => '4',
                'label' => 'Top - Right On Hover',
            ],
            [
                'value' => '5',
                'label' => 'Below Swatches On Hover', // 3
            ]
        ];
    }
}