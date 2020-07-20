<?php

namespace WeltPixel\GoogleCards\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Description
 *
 * @package WeltPixel\GoogleCards\Model\Config\Source
 */
class Reviews implements ArrayInterface
{

    /**
     * Return list of Number of Reviews Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 0,
                'label' => __('Please Select')
            ),
            array(
                'value' => 1,
                'label' => __('Agregate Ratings')
            ),
            array(
                'value' => 2,
                'label' => __('Agregate Ratings + Reviews')
            )


        );
    }
}