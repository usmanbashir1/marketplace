<?php

namespace WeltPixel\ProductPage\Model\Config\Source\Gallery;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class TransitionEffect
 *
 * @package WeltPixel\ProductPage\Model\Config\Source\Gallery
 */
class TransitionEffect implements ArrayInterface
{

    /**
     * Return list of TransitionEffect Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'slide',
                'label' => __('Slide')
            ),
            array(
                'value' => 'crossfade',
                'label' => __('Crossfade')
            ),
            array(
                'value' => 'dissolve',
                'label' => __('Dissolve')
            )
        );
    }
}