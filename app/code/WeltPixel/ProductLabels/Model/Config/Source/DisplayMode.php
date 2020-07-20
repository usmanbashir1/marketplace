<?php

namespace WeltPixel\ProductLabels\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class DisplayMode
 * @package WeltPixel\ProductLabels\Model\Config\Source
 */
class DisplayMode implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'always',
                'label' => 'Always',
            ),
            array(
                'value' => 'hover',
                'label' => 'On hover',
            )
        );
    }
}