<?php

namespace WeltPixel\ProductPage\Model\ResourceModel\ProductPage;

/**
 * Class Collection
 * @package WeltPixel\ProductPage\Model\ResourceModel
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

	/**
	 * @var string
	 */
	protected $_idFieldName = 'entity_id';

	/**
	 * _construct
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('WeltPixel\ProductPage\Model\ProductPage',
			'WeltPixel\ProductPage\Model\ResourceModel\ProductPage');
	}
}