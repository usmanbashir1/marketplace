<?php

namespace WeltPixel\CategoryPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class NameAlign
 *
 * @package WeltPixel\CategoryPage\Model\Config\Source
 */
class TextAlign implements ArrayInterface
{

    /**
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'left',
                'label' => 'Left',
            ],
            [
                'value' => 'center',
                'label' => 'Center',
            ]
        ];
    }
}