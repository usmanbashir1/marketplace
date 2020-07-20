<?php

namespace WeltPixel\CustomHeader\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Headerstyles
 *
 * @package WeltPixel\CustomHeader\Model\Config\Source
 */
class SearchBorderStyle implements ArrayInterface
{

    /**
     * Return list of Custom Header Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'dashed',
                'label' => 'dashed',
            ),
            array(
                'value' => 'dotted',
                'label' => 'dotted',
            ),
            array(
                'value' => 'double',
                'label' => 'double',
            ),
            array(
                'value' => 'groove',
                'label' => 'groove',
            ),
            array(
                'value' => 'hidden',
                'label' => 'hidden'
            ),
            array(
                'value' => 'inset',
                'label' => 'inset'
            ),
            array(
                'value' => 'outset',
                'label' => 'outset'
            )
        );
    }
}