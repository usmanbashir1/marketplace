<?php
namespace WeltPixel\SmartProductTabs\Helper;

/**
 * Class Data
 * @package WeltPixel\SmartProductTabs\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * @var string
	 */
	protected $_scopeValue = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

	/**
	 * @var string
	 */
	protected $_tabName = [
		'weltpixel_smartproducttabs/general/attribute_smartproducttabs_tab_1',
		'weltpixel_smartproducttabs/general/attribute_smartproducttabs_tab_2',
		'weltpixel_smartproducttabs/general/attribute_smartproducttabs_tab_3'
	];

	/**
	 * @return string
	 */
	public function getTabNameA()
	{
		$tabName = $this->scopeConfig->getValue($this->_tabName[0], $this->_scopeValue);
		if(empty($tabName)){
			return 'Smart Product Tab';
		}
		return $tabName;
	}

	/**
	 * @return string
	 */
	public function getTabNameB()
	{
		$tabName = $this->scopeConfig->getValue($this->_tabName[1], $this->_scopeValue);
		if(empty($tabName)){
			return 'Smart Product Tab';
		}
		return $tabName;
	}

	/**
	 * @return string
	 */
	public function getTabNameC()
	{
		$tabName = $this->scopeConfig->getValue($this->_tabName[2], $this->_scopeValue);
		if(empty($tabName)){
			return 'Smart Product Tab';
		}
		return $tabName;
	}
}
