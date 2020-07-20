<?php

namespace WeltPixel\ProductPage\Model\ResourceModel;

/**
 * Class ProductPage
 * @package WeltPixel\ProductPage\Model\ResourceModel
 */
class ProductPage extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

	/**
	 * _construct
	 * @return void
	 */
	public function _construct() {
		$this->_init( 'weltpixel_product_view_values', 'entity_id' );
	}

	/**
	 * @param \WeltPixel\ProductPage\Model\ProductPage $productPage
	 * @param int $version
	 * @param int $store
	 * @return $this
	 */
	public function loadByVersionAndStore(
		\WeltPixel\ProductPage\Model\ProductPage $productPage,
		$version,
		$store
	) {
		$connection = $this->getConnection();
		$bind = [
			'version_id' => $version,
			'store_id'	 => $store
		];

		$select = $connection->select()->from(
			$this->getMainTable(),
			[$this->getIdFieldName()]
		)->where(
			'version_id = :version_id AND store_id = :store_id'
		);

		$productPageId = $connection->fetchOne($select, $bind);
		if ($productPageId) {
			$this->load($productPage, $productPageId);
		} else {
			$productPage->setData([]);
		}
	}
}