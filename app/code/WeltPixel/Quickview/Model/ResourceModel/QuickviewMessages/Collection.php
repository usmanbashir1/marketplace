<?php
namespace WeltPixel\Quickview\Model\ResourceModel\QuickviewMessages;

/**
 * Class Collection
 * @package WeltPixel\Quickview\Model\ResourceModel\QuickviewMessages
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
        $this->_init('WeltPixel\Quickview\Model\QuickviewMessages', 'WeltPixel\Quickview\Model\ResourceModel\QuickviewMessages');
    }
}
