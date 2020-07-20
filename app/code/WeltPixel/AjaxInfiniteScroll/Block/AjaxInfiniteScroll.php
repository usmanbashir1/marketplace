<?php

namespace WeltPixel\AjaxInfiniteScroll\Block;

use Magento\Framework\View\Element\Template\Context;
use WeltPixel\AjaxInfiniteScroll\Helper\Data;

class AjaxInfiniteScroll extends \Magento\Framework\View\Element\Template
{
    protected $_scopeConfig;
    protected $_helper;

    /**
     * AjaxInfiniteScroll constructor.
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function isEnabledCanonicalPrevNext() {
        return $this->_helper->getConfigValue('advanced', 'prev_next');
    }

    /**
     * @return string
     */
    public function isEnabledcategoryCanonicalTag() {
        // catalog_seo_category_canonical_tag
        return $this->_scopeConfig->getValue(
            'catalog/seo_category/canonical_tag',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    protected function _prepareLayout()
    {
        if ($this->isEnabledCanonicalPrevNext()) {
            // insert canonical link
            if (!$this->isEnabledcategoryCanonicalTag()) {
                $this->pageConfig->addRemotePageAsset(
                    $this->_helper->removeQueryFromUrl($this->_helper->getCurrentUrl()),
                    'canonical',
                    ['attributes' => ['rel' => 'canonical']]
                );
            }

            $currentPageNo = $this->_helper->getCurrentPageNo();

            // insert next link
            if ($currentPageNo == 1) {
                $this->pageConfig->addRemotePageAsset(
                    $this->_helper->getNextPageUrl(),
                    'next',
                    ['attributes' => ['rel' => 'next']]
                );
            }
            // insert next and prev link
            if ($currentPageNo > 1 && $currentPageNo < $this->_helper->getLastPageNo()) {
                $this->pageConfig->addRemotePageAsset(
                    $this->_helper->getPrevPageUrl(),
                    'prev',
                    ['attributes' => ['rel' => 'prev']]
                );
                $this->pageConfig->addRemotePageAsset(
                    $this->_helper->getNextPageUrl(),
                    'next',
                    ['attributes' => ['rel' => 'next']]
                );
            }
            // insert prev link
            if ($currentPageNo == $this->_helper->getLastPageNo()) {
                $this->pageConfig->addRemotePageAsset(
                    $this->_helper->getPrevPageUrl(),
                    'prev',
                    ['attributes' => ['rel' => 'prev']]
                );
            }
        }
    }
}