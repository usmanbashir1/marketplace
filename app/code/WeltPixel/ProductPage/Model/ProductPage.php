<?php

namespace WeltPixel\ProductPage\Model;

/**
 * Class ProductPage
 * @package WeltPixel\ProductPage\Model
 */
class ProductPage extends \Magento\Framework\Model\AbstractModel
{
	/**
	 * _construct
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('WeltPixel\ProductPage\Model\ResourceModel\ProductPage');
	}

	/**
	 * @param int $version
	 * @param int $store
	 * @return $this
	 */
	public function loadByVersionAndStore($version, $store) {
		$this->_getResource()->loadByVersionAndStore($this, $version, $store);
		return $this;
	}
}