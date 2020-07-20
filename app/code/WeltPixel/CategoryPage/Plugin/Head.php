<?php
namespace WeltPixel\CategoryPage\Plugin;

class Head {
    
    /**
    * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $_storeManager;

    /**
     * @var \WeltPixel\Backend\Helper\Utility;
     */
    protected $utilityHelper;

    /**
     * Head constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \WeltPixel\Backend\Helper\Utility $utilityHelper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \WeltPixel\Backend\Helper\Utility $utilityHelper
        ) {
        $this->_storeManager = $storeManager;
        $this->utilityHelper = $utilityHelper;
    }

    /**
     * @param \Magento\Framework\View\Page\Config\Reader\Head $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\View\Layout\Reader\Context $readerContext
     * @param \Magento\Framework\View\Layout\Element $headElement
     * @return mixed
     */
    public function aroundInterpret(
        \Magento\Framework\View\Page\Config\Reader\Head $subject, 
        \Closure $proceed, 
        \Magento\Framework\View\Layout\Reader\Context $readerContext,
        \Magento\Framework\View\Layout\Element $headElement)
    {
        
        $result = $proceed($readerContext, $headElement);
        /** Add the store specific css just for stores where theme is used */
        if ($this->utilityHelper->isPearlThemeUsed()) {
            $pageConfigStructure = $readerContext->getPageConfigStructure();

            $store = $this->_storeManager->getStore();
            $categoryStoreCss = 'weltpixel_category_store_' . $store->getData('code') . '.css';

            $node = new \Magento\Framework\View\Layout\Element('<css src="WeltPixel_CategoryPage::css/' . $categoryStoreCss . '" />');
            $node->addAttribute('content_type', 'css');
            $pageConfigStructure->addAssets($node->getAttribute('src'), $this->getAttributes($node));
        }
        
        return $result;
    }

    /**
     * @param $element
     * @return array
     */
    protected function getAttributes($element)
    {
        $attributes = [];
        foreach ($element->attributes() as $attrName => $attrValue) {
            $attributes[$attrName] = (string)$attrValue;
        }
        return $attributes;
    }
}