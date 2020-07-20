<?php

namespace WeltPixel\ProductPage\Model\Config\Source\Gallery;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class SlidingDirection
 *
 * @package WeltPixel\ProductPage\Model\Config\Source\Gallery
 */
class SlidingDirection implements ArrayInterface
{

    /**
     * Return list of SlidingDirection Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'horizontal',
                'label' => __('Horizontal')
            ),
            array(
                'value' => 'vertical',
                'label' => __('Vertical')
            )
        );
    }
}