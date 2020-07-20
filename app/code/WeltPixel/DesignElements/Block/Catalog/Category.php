<?php
namespace WeltPixel\DesignElements\Block\Catalog;

class Category extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * FrontendOptions Helper Data
     *
     * @var \WeltPixel\FrontendOptions\Helper\Data
     */
    protected $_frontendOptionsHelperData;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \WeltPixel\FrontendOptions\Helper\Data $frontendOptionsHelperData,
        \Magento\Framework\Registry $registry,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_frontendOptionsHelperData = $frontendOptionsHelperData;
    }

    /**
     * Retrieve current category model object
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        if (!$this->hasData('current_category')) {
            $this->setData('current_category', $this->_coreRegistry->registry('current_category'));
        }
        return $this->getData('current_category');
    }

    public function getSmallMobileBreakpoint(){
        $default = '320px';
        if($bp = $this->_frontendOptionsHelperData->getBreakpointXXS()){
            return $bp;
        }
        return $default;
    }

    public function getMobileBreakpoint(){
        $default = '480px';
        if($bp = $this->_frontendOptionsHelperData->getBreakpointXS()){
            return $bp;
        }
        return $default;
    }

    public function getSmallTabletBreakpoint(){
        $default = '640px';
        if($bp = $this->_frontendOptionsHelperData->getBreakpointS()){
            return $bp;
        }
        return $default;
    }

    public function getTabletBreakpoint(){
        $default = '800px';
        if($bp = $this->_frontendOptionsHelperData->getBreakpointM()){
            return $bp;
        }
        return $default;
    }

    public function getDeskBreakpoint(){
        $default = '1200px';
        if($bp = $this->_frontendOptionsHelperData->getBreakpointL()){
            return $bp;
        }
        return $default;
    }
	
	public function getLargeDeskBreakpoint(){
		$default = '1440px';
		if($bp = $this->_frontendOptionsHelperData->getBreakpointXL()){
			return $bp;
		}
		return $default;
	}
}