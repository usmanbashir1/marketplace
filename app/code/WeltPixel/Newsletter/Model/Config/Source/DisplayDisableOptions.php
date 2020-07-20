<?php

namespace WeltPixel\Newsletter\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
/**
 * Class DisplayDisableOptions
 *
 * @package WeltPixel\Newsletter\Model\Config\Source
 */
class DisplayDisableOptions implements ArrayInterface
{

    const MODE_CLOSE_BUTTON = 1;
    const MODE_CLOSE_BUTTON_AND_OUTSIDE_CLICK = 2;

    /**
     * Return list of DisplayDisableOptions
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::MODE_CLOSE_BUTTON,
                'label' => __('Only when Close button is clicked')
            ),
            array(
                'value' => self::MODE_CLOSE_BUTTON_AND_OUTSIDE_CLICK,
                'label' => __('When Close button is clicked or when clicked outside the box')
            ),
        );
    }
}