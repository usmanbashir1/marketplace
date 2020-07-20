<?php

namespace WeltPixel\FrontendOptions\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * @package WeltPixel\FrontendOptions\Model\Config\Source
 */
class BlockCollection implements ArrayInterface
{

	/**
	 * @var \Magento\Cms\Model\ResourceModel\Block\CollectionFactory
	 */
	private $_blockCollectionFactory;

	/**
	 * @param  \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockCollectionFactory
	 */
	public function __construct(\Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockCollectionFactory)
	{
		$this->_blockCollectionFactory = $blockCollectionFactory;
	}

	/**
	 * Return list of Attributes
	 *
	 * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
	 */
	public function toOptionArray()
	{
		$arr = [];
		$blockCollection = $this->_blockCollectionFactory->create();
		foreach ($blockCollection as $block) {
			$arr[] = array(
				'value' => $block->getId(),
				'label' => $block->getTitle()
			);
		}
		return $arr;
	}
}
