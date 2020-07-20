<?php

namespace WeltPixel\ProductPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ProductPage
 *
 * @package WeltPixel\ProductPage\Model\Config\Source
 */
class TabsVersion implements ArrayInterface
{

    /**
     * Return list of Accordion Version
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '0',
                'label' => __('Version 1 - aligned left with border'),
            ),
            array(
                'value' => '1',
                'label' => __('Version 2 - centered without border'),
            )
        );
    }
}