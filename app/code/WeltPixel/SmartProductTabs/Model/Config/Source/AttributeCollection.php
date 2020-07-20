<?php

namespace WeltPixel\SmartProductTabs\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;

/**
 * Class SmartProductTabs
 *
 * @package WeltPixel\SmartProductTabs\Model\Config\Source
 */
class AttributeCollection implements ArrayInterface
{

	/**
	 * @var AttributeFactory
	 */
	private $_attributeFactory;

	/**
	 * @var TypeFactory
	 */
	protected $eavTypeFactory;

	/**
	 * @param  $attributeFactory
	 * @param TypeFactory $typeFactory
	 */
	public function __construct(AttributeFactory $attributeFactory, TypeFactory $typeFactory)
	{
		$this->_attributeFactory = $attributeFactory;
		$this->eavTypeFactory = $typeFactory;
	}

	/**
	 * Return list of Attributes
	 *
	 * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
	 */
	public function toOptionArray()
	{
		$arr = [];
        $arr[] = [
            'value' => false,
            'label' => __('-- Please Select Attribute --')
        ];
		$entityType = $this->eavTypeFactory->create()->loadByCode('catalog_product');
		$attributesCollection = $this->_attributeFactory->create()->getCollection();
		$attributesCollection
			->addFieldToFilter('entity_type_id', $entityType->getId())
			->addFieldToFilter('frontend_input', 'select');
		foreach ($attributesCollection as $attribute) {
			$arr[] = array(
				'value' => $attribute->getData('attribute_code'),
				'label' => $attribute->getData('frontend_label')
			);
		}
		return $arr;
	}
}
