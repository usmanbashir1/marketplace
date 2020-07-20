<?php

namespace WeltPixel\GoogleCards\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ItemCondition
 *
 * @package WeltPixel\GoogleCards\Model\Config\Source
 */
class ItemCondition implements ArrayInterface
{

    /**
     * Return list of Description Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '',
                'label' => __('Please select')
            ),
            array(
                'value' => 'DamagedCondition',
                'label' => __('Damaged Condition')
            ),
            array(
                'value' => 'NewCondition',
                'label' => __('New Condition')
            ),
            array(
                'value' => 'RefurbishedCondition',
                'label' => __('Refurbished Condition')
            ),
            array(
                'value' => 'UsedCondition',
                'label' => __('Used Condition')
            )
        );
    }
}