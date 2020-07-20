<?php

namespace WeltPixel\ProductPage\Model\Config\Source\Gallery;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class NavStyle
 *
 * @package WeltPixel\ProductPage\Model\Config\Source\Gallery
 */
class NavStyle implements ArrayInterface
{

    /**
     * Return list of NavStyle Options
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
                'value' => 'dots',
                'label' => __('Dots')
            ),
            array(
                'value' => 'false',
                'label' => __('None')
            )
        );
    }
}