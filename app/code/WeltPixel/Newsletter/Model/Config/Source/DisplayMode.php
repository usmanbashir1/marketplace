<?php

namespace WeltPixel\Newsletter\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
/**
 * Class DisplayMode
 *
 * @package WeltPixel\Newsletter\Model\Config\Source
 */
class DisplayMode implements ArrayInterface
{
    const MODE_HOME_PAGE = 0;
    const MODE_ALL_PAGES = 1;

    /**
     * Return list of DisplayOptions
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::MODE_HOME_PAGE,
                'label' => __('Just Home Page')
            ),
            array(
                'value' => self::MODE_ALL_PAGES,
                'label' => __('All Pages')
            ),
        );
    }
}