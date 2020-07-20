<?php

namespace WeltPixel\ProductPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class TabLayout
 *
 * @package WeltPixel\ProductPage\Model\Config\Source
 */
class TabsLayout implements ArrayInterface
{
    const TAB_TAB = 'tab';
    const TAB_ACCORDION = 'accordion';
    const TAB_LIST = 'list';

    /**
     * Return list of TabLayout Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::TAB_TAB,
                'label' => __('Tab')
            ),
            array(
                'value' => self::TAB_ACCORDION,
                'label' => __('Accordion')
            ),
            array(
                'value' => self::TAB_LIST,
                'label' => __('List')
            )
        );
    }
}