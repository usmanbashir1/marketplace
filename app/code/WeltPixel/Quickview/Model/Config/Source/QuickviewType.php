<?php

namespace WeltPixel\Quickview\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class QuickviewType
 *
 * @package WeltPixel\Quickview\Model\Config\Source
 */
class QuickviewType implements ArrayInterface
{

    const TYPE_DEFAULT = 'default';
    const TYPE_RIGHT_SLIDE = 'right_slide';
    const TYPE_LEFT_SLIDE = 'left_slide';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::TYPE_DEFAULT,
                'label' => __('Default ( Quickview dispalyed in the center of the page )'),
            ),
            array(
                'value' => self::TYPE_RIGHT_SLIDE,
                'label' => __('Right Side ( Quickview Slides in the right side of the page )'),
            ),
            array(
                'value' => self::TYPE_LEFT_SLIDE,
                'label' => __('Left Side ( Quickview Slides in the left side of the page )'),
            )
        );
    }
}