<?php
namespace WeltPixel\DesignElements\Block\Cms;

class Page extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_page;
    /**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;
    /**
     * FrontendOptions Helper Data
     *
     * @var \WeltPixel\FrontendOptions\Helper\Data
     */
    protected $_frontendOptionsHelperData;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Model\Page $page,
        \WeltPixel\FrontendOptions\Helper\Data $frontendOptionsHelperData,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->_page = $page;
        $this->_pageFactory = $pageFactory;
        $this->_frontendOptionsHelperData = $frontendOptionsHelperData;
    }

    /**
     * Retrieve Page instance
     *
     * @return \Magento\Cms\Model\Page
     */
    public function getPage()
    {
        if (!$this->hasData('page')) {
            if ($this->getPageId()) {
                /** @var \Magento\Cms\Model\Page $page */
                $page = $this->_pageFactory->create();
                $page->setStoreId($this->_storeManager->getStore()->getId())->load($this->getPageId(), 'page_id');
            } else {
                $page = $this->_page;
            }
            $this->setData('page', $page);
        }
        return $this->getData('page');
    }

    public function getPageId(){
        return $this->_request->getParam('page_id');
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