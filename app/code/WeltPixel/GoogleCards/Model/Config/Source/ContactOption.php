<?php

namespace WeltPixel\GoogleCards\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ContactType
 *
 * @package WeltPixel\GoogleCards\Model\Config\Source
 */
class ContactOption implements ArrayInterface
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
                'label' => __('Select Contact Option')
            ),
            array(
                'value' => 'TollFree',
                'label' => __('Toll Free')
            ),
            array(
                'value' => 'HearingImpairedSupported',
                'label' => __('Hearing Impaired Supported')
            )
        );
    }
}