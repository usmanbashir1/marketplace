<?php

namespace WeltPixel\ProductPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class QtyType
 *
 * @package WeltPixel\ProductPage\Model\Config\Source
 */
class QtyType implements ArrayInterface
{
    const QTY_DEFAULT = 0;
    const QTY_SELECT = 'select';

    /**
     * Return list of QtyType Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::QTY_DEFAULT,
                'label' => __('Default Input')
            ),
            array(
                'value' => self::QTY_SELECT,
                'label' => __('Dropdown')
            )
        );
    }
}