<?php

namespace WeltPixel\AjaxInfiniteScroll\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class AjaxCatalog
 *
 * @package WeltPixel\AjaxInfiniteScroll\Model\Config\Source
 */
class AjaxCatalog implements ArrayInterface
{

    /**
     * Return list of Ajax Catalog Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'infinite_scroll',
                'label' => 'Ajax Infinite Scroll',
            ),
            array(
                'value' => 'next_page',
                'label' => 'Ajax Next Page',
            )
        );
    }
}