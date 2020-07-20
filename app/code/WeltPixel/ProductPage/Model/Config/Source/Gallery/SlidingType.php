<?php

namespace WeltPixel\ProductPage\Model\Config\Source\Gallery;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class SlidingType
 *
 * @package WeltPixel\ProductPage\Model\Config\Source\Gallery
 */
class SlidingType implements ArrayInterface
{

    /**
     * Return list of SlidingType Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'slides',
                'label' => __('Slides')
            ),
            array(
                'value' => 'thumbs',
                'label' => __('Thumbs')
            )
        );
    }
}