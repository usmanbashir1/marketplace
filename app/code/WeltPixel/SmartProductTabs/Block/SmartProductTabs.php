<?php

namespace WeltPixel\SmartProductTabs\Block;

use Magento\Catalog\Block\Product\View\Attributes;
use Magento\Catalog\Model\Product;

/**
 * Class SmartProductTabs
 * @package WeltPixel\SmartProductTabs\Block
 */
class SmartProductTabs extends Attributes
{
	/**
	 * @var Product
	 */
	protected $_product = null;

	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @var string
	 */
	protected $_scopeValue = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

	/**
	 * @var string
	 */
	protected $_moduleAttributeSet = [
		'weltpixel_smartproducttabs/general/attribute_smartproducttabs_1',
		'weltpixel_smartproducttabs/general/attribute_smartproducttabs_2',
		'weltpixel_smartproducttabs/general/attribute_smartproducttabs_3'
	];

	/**
	 * @var string
	 */
	protected $_moduleEnable = 'weltpixel_smartproducttabs/general/enable_smartproducttabs';

    /**
     * @var string
     */
    protected $_blockMatchingLogic = 'weltpixel_smartproducttabs/general/block_matching_logic';

	/**
	 * protected $eavConfig;
	 *
	 * /**
	 * @return $this
	 */
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	/**
	 * @return string
	 */
	private function getProductAttributeOptionA()
	{
		if (!$this->_product) {
			$this->_product = $this->_coreRegistry->registry('product');
		}

        $option = '';

        if ($this->_scopeConfig->getValue($this->_blockMatchingLogic, $this->_scopeValue)) {
            $option = $this->_product->getAttributeText($this->getProductAttributeA());
        } else {
            $attr = $this->_product->getResource()->getAttribute($this->getProductAttributeA());
            $option = $attr->setStoreId(0)->getSource()->getOptionText($this->_product->getData($this->getProductAttributeA()));
        }

        return $option;
	}

	/**
	 * @return string
	 */
	private function getProductAttributeOptionB()
	{
		if (!$this->_product) {
			$this->_product = $this->_coreRegistry->registry('product');
		}

		$option = '';

		if ($this->_scopeConfig->getValue($this->_blockMatchingLogic, $this->_scopeValue)) {
            $option = $this->_product->getAttributeText($this->getProductAttributeB());
        } else {
            $attr = $this->_product->getResource()->getAttribute($this->getProductAttributeB());
            $option = $attr->setStoreId(0)->getSource()->getOptionText($this->_product->getData($this->getProductAttributeB()));
        }

        return $option;
	}

	/**
	 * @return string
	 */
	private function getProductAttributeOptionC()
	{
		if (!$this->_product) {
			$this->_product = $this->_coreRegistry->registry('product');
		}

        $option = '';

        if ($this->_scopeConfig->getValue($this->_blockMatchingLogic, $this->_scopeValue)) {
            $option = $this->_product->getAttributeText($this->getProductAttributeC());
        } else {
            $attr = $this->_product->getResource()->getAttribute($this->getProductAttributeC());
            $option = $attr->setStoreId(0)->getSource()->getOptionText($this->_product->getData($this->getProductAttributeC()));
        }

        return $option;
	}

	/**
	 * @return mixed
	 */
	private function getProductAttributeA()
	{
		if (empty($this->_data['attribute_smartproducttabs'])) {
			$this->_data['attribute_smartproducttabs'] = $this->_scopeConfig->getValue(
				$this->_moduleAttributeSet[0],
				$this->_scopeValue
			);
		}
		return $this->_data['attribute_smartproducttabs'];
	}

	/**
	 * @return mixed
	 */
	private function getProductAttributeB()
	{
		if (empty($this->_data['attribute_smartproducttabsb'])) {
			$this->_data['attribute_smartproducttabsb'] = $this->_scopeConfig->getValue(
				$this->_moduleAttributeSet[1],
				$this->_scopeValue
			);
		}
		return $this->_data['attribute_smartproducttabsb'];
	}

	/**
	 * @return mixed
	 */
	private function getProductAttributeC()
	{
		if (empty($this->_data['attribute_smartproducttabsc'])) {
			$this->_data['attribute_smartproducttabsc'] = $this->_scopeConfig->getValue(
				$this->_moduleAttributeSet[2],
				$this->_scopeValue
			);
		}
		return $this->_data['attribute_smartproducttabsc'];
	}

	/**
	 * @return bool
	 */
	public function getSmartProductTabsA()
	{
		$productAttribute = $this->getProductAttributeA();
		if (!isset($productAttribute)) {
			return false;
		}
		$productAttributeOption = str_replace(' ', '-', strtolower($this->getProductAttributeOptionA()));
		$productAttributeOption = preg_replace('/[^A-Za-z0-9\-]/', '', $productAttributeOption);
		$staticBlockIdentifier = $this->getLayout()
			->createBlock('Magento\Cms\Block\Block')
			->setBlockId('smartproducttabs_' . $productAttribute . '_' . $productAttributeOption);
		$moduleEnable = $this->_scopeConfig->getValue($this->_moduleEnable, $this->_scopeValue);
		if ($staticBlockIdentifier->getBlockId() && $moduleEnable) {
			return $staticBlockIdentifier->toHtml();
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function getSmartProductTabsB()
	{
		$productAttribute = $this->getProductAttributeB();
		if (!isset($productAttribute)) {
			return false;
		}
		$productAttributeOption = str_replace(' ', '-', strtolower($this->getProductAttributeOptionB()));
		$productAttributeOption = preg_replace('/[^A-Za-z0-9\-]/', '', $productAttributeOption);
		$staticBlockIdentifier = $this->getLayout()
			->createBlock('Magento\Cms\Block\Block')
			->setBlockId('smartproducttabs_' . $productAttribute . '_' . $productAttributeOption);
		$moduleEnable = $this->_scopeConfig->getValue($this->_moduleEnable, $this->_scopeValue);
		if ($staticBlockIdentifier->getBlockId() && $moduleEnable) {
			return $staticBlockIdentifier->toHtml();
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function getSmartProductTabsC()
	{
		$productAttribute = $this->getProductAttributeC();
		if (!isset($productAttribute)) {
			return false;
		}
		$productAttributeOption = str_replace(' ', '-', strtolower($this->getProductAttributeOptionC()));
		$productAttributeOption = preg_replace('/[^A-Za-z0-9\-]/', '', $productAttributeOption);
		$staticBlockIdentifier = $this->getLayout()
			->createBlock('Magento\Cms\Block\Block')
			->setBlockId('smartproducttabs_' . $productAttribute . '_' . $productAttributeOption);
		$moduleEnable = $this->_scopeConfig->getValue($this->_moduleEnable, $this->_scopeValue);
		if ($staticBlockIdentifier->getBlockId() && $moduleEnable) {
			return $staticBlockIdentifier->toHtml();
		}
		return false;
	}
}