<?php

namespace WeltPixel\ProductPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class QtyType
 *
 * @package WeltPixel\ProductPage\Model\Config\Source
 */
class Version implements ArrayInterface
{

    /**
     * Return list of QtyType Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 1,
                'label' => __('Version 1')
            ),
            array(
                'value' => 2,
                'label' => __('Version 2')
            ),
            array(
                'value' => 3,
                'label' => __('Version 3')
            ),
            array(
                'value' => 4,
                'label' => __('Version 4')
            )
        );
    }
}