<?php
namespace WeltPixel\ProductLabels\Model\ResourceModel\ProductLabels;

/**
 * Class Collection
 * @package WeltPixel\ProductLabels\Model\ResourceModel\ProductLabels
 */
class Collection extends \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\ProductLabels\Model\ProductLabels', 'WeltPixel\ProductLabels\Model\ResourceModel\ProductLabels');
    }
}
