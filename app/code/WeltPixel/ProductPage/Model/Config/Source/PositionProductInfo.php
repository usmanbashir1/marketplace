<?php

namespace WeltPixel\ProductPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class PositionProductInfo
 *
 * @package WeltPixel\PositionProductInfo\Model\Config\Source
 */
class PositionProductInfo implements ArrayInterface
{

    /**
     * Return list of PositionProductInfo Version
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '0',
                'label' => 'Fixed',
            ),
            array(
                'value' => '1',
                'label' => 'Vertical Sliding with Scroll',
            )
        );
    }
}