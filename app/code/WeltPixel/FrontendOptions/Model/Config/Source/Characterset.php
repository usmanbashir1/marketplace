<?php

namespace WeltPixel\FrontendOptions\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Characterset
 *
 * @package WeltPixel\FrontendOptions\Model\Config\Source
 */
class Characterset implements ArrayInterface {

    protected $_charsets = array(
        'cyrillic' => 'Cyrillic',
        'cyrillic-ext' => 'Cyrillic Extended',
        'greek' => 'Greek',
        'greek-ext' => 'Greek Extended',
        'khmer' => 'Khmer',
        'latin' => 'Latin',
        'latin-ext' => 'Latin Extende',
        'vietnamese' => 'Vietnamese',
    );

    /**
     * Return list of Google CharSets
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray() {

        $options = array();

        foreach ($this->_charsets as $id => $charset) :
            $options[] = array(
                'value' => $id,
                'label' => $charset
            );
        endforeach;

        return $options;
    }

}
