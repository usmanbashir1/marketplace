<?php

namespace WeltPixel\DesignElements\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * @var array
	 */
	protected $_elementsOptions;
	
	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\App\Helper\Context $context
	 */
	public function __construct(
			\Magento\Framework\App\Helper\Context $context
	) {
		parent::__construct($context);
		
		$this->_elementsOptions = $this->scopeConfig->getValue('weltpixel_design_elements', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	/**
	 * @return string
	 */
	public function getBttOptionsJson()
	{
		$options = [];
		foreach ($this->getBttOptions() as $key => $value) {
			$value = (int) trim($value);
			$options[$key] = $value;
		}
		
		return json_encode($options);
	}
	
	/**
	 * @return array
	 */
	public function getBttOptions() {
		return array(
			'offset' => $this->_elementsOptions['general']['btt_offset'],
			'offsetOpacity' => $this->_elementsOptions['general']['btt_offset_opacity'],
			'scrollTopDuration' => $this->_elementsOptions['general']['btt_duration'],
		);
	}

    /**
     * @return bool
     */
	public function isResponsiveHelpersEnabled() {
        return $this->_elementsOptions['general']['responsive_helpers'];
    }

    /**
     * @return bool
     */
    public function getCollapsibleWidgetTopJump() {
        return $this->_elementsOptions['general']['collapsible_top_jump'];
    }
}
