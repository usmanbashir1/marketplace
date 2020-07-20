<?php

namespace WeltPixel\GoogleCards\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ContactType
 *
 * @package WeltPixel\GoogleCards\Model\Config\Source
 */
class ContactType implements ArrayInterface
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
                'value' => 'customer support',
                'label' => __('Customer Support')
            ),
            array(
                'value' => 'technical support',
                'label' => __('Technical Support')
            ),
            array(
                'value' => 'billing support',
                'label' => __('Billing Support')
            ),
            array(
                'value' => 'bill payment',
                'label' => __('Bill Payment')
            ),
            array(
                'value' => 'sales',
                'label' => __('Sales')
            ),
            array(
                'value' => 'reservations',
                'label' => __('Reservations')
            ),
            array(
                'value' => 'credit card support',
                'label' => __('Credit Card Support')
            ),
            array(
                'value' => 'emergency',
                'label' => __('Emergency')
            ),
            array(
                'value' => 'baggage tracking',
                'label' => __('Baggage Tracking')
            ),
            array(
                'value' => 'roadside assistance',
                'label' => __('Roadside Assistance')
            ),
            array(
                'value' => 'package tracking',
                'label' => __('Package Tracking')
            )

        );
    }
}