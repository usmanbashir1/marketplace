<?php

namespace WeltPixel\SearchAutoComplete\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Headerstyles
 *
 * @package WeltPixel\CustomHeader\Model\Config\Source
 */
class TemplateResults implements ArrayInterface
{

    /**
     * Return list of Search Versions
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '0',
                'label' => 'Vertically',
            ),
            array(
                'value' => '1',
                'label' => 'Horizontally (available only on Search Version 2)'
            )
        );
    }
}