<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_EnhancedEmail
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Nagy Attila @ Weltpixel TEAM
 */

namespace WeltPixel\FrontendOptions\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class FontWeight
 * @package WeltPixel\EnhancedEmail\Model\Config\Source
 */
class FontWeight implements ArrayInterface
{
    protected $_weights = array(
        'normal' => 'Normal (default)',
        'inherit' => 'Inherit (from its parent)',
        'initial' => 'Initial (default value)',
        'bold' => 'Bold',
        'bolder' => 'Bolder',
        'lighter' => 'Lighter',
        '300' => '300',
        '400' => '400',
        '600' => '600',
        '700' => '700',
        '800' => '800',
        '900' => '900',

    );

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach ($this->_weights as $id => $weight) :
            $options[] = array(
                'value' => $id,
                'label' => $weight
            );
        endforeach;
        return $options;
    }
}