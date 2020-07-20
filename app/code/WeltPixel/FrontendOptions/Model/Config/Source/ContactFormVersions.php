<?php

namespace WeltPixel\FrontendOptions\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ContactFormVersions
 *
 * @package WeltPixel\FrontendOptions\Model\Config\Source
 */
class ContactFormVersions implements ArrayInterface
{

    /**
     * Return list of Contact Versions
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'v1',
                'label' => 'Version 1',
            ),
            array(
                'value' => 'v2',
                'label' => 'Version 2',
            )
        );
    }
}