<?php

namespace WeltPixel\ProductPage\Model\Config\Source\Gallery;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class NavType
 *
 * @package WeltPixel\ProductPage\Model\Config\Source\Gallery
 */
class NavType implements ArrayInterface
{

    /**
     * Return list of NavType Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'thumbs',
                'label' => __('Thumbs')
            ),
            array(
                'value' => 'slides',
                'label' => __('Slides')
            )
        );
    }
}