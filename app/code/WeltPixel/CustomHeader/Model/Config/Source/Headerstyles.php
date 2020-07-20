<?php

namespace WeltPixel\CustomHeader\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Headerstyles
 *
 * @package WeltPixel\CustomHeader\Model\Config\Source
 */
class Headerstyles implements ArrayInterface
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
                'value' => 'v1',
                'label' => 'Version 1',
            ),
            array(
                'value' => 'v2',
                'label' => 'Version 2',
            ),
            array(
                'value' => 'v3',
                'label' => 'Version 3',
            ),
            array(
                'value' => 'v4',
                'label' => 'Version 4',
            )
        );
    }
}