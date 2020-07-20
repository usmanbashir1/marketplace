<?php

namespace WeltPixel\CustomHeader\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Headerstyles
 *
 * @package WeltPixel\CustomHeader\Model\Config\Source
 */
class SearchVersions implements ArrayInterface
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
                'label' => 'Version 1',
            ),
            array(
                'value' => '1',
                'label' => 'Version 2',
            )
        );
    }
}