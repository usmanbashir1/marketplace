<?php

namespace WeltPixel\AjaxInfiniteScroll\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Registry;
use Magento\Theme\Block\Html\Pager;
use Magento\Catalog\Block\Product\ProductList\Toolbar;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Theme\Block\Html\Pager
     */
    protected $_pager;

    /**
     * @var \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    protected $_toolbar;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Repository $assetRepo
     * @param Registry $registry
     * @param Pager $pager
     * @param Toolbar $_toolbar
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Repository $assetRepo,
        Registry $registry,
        Pager $pager,
        Toolbar $_toolbar
    )
    {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $storeManager;
        $this->_urlInterface = $context->getUrlBuilder();
        $this->_assetRepo = $assetRepo;
        $this->_registry = $registry;
        $this->_request = $context->getRequest();
        $this->_pager = $pager;
        $this->_toolbar = $_toolbar;

        parent::__construct($context);
    }

    /**
     * @param $group
     * @param $field
     * @return mixed
     */
    public function getConfigValue($group, $field)
    {
        return $this->_scopeConfig->getValue(
            'weltpixel_infinite_scroll/' . $group . '/' . $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function isAjaxCatalogEnabled()
    {
        return $this->getConfigValue('general', 'ajax_catalog');
    }

    /**
     * @return mixed
     */
    public function getAjaxCatalogMode()
    {
        return $this->getConfigValue('general', 'ajax_catalog_mode');
    }

    /**
     * @return mixed
     */
    public function getLoadMore()
    {
        return $this->getConfigValue('advanced', 'load_more');
    }

    /**
     * @return mixed
     */
    public function getTextLoading()
    {
        return $this->replaceQuote($this->getConfigValue('advanced', 'text_loading'));
    }

    /**
     * @return mixed
     */
    public function getTextLoadNext()
    {
        return $this->replaceQuote($this->getConfigValue('advanced', 'text_loadnext'));
    }

    /**
     * @return mixed
     */
    public function getTextLoadPrevious()
    {
        return $this->replaceQuote($this->getConfigValue('advanced', 'text_loadprevious'));
    }

    /**
     * @return mixed
     */
    public function getTextNoMore()
    {
        return $this->replaceQuote($this->getConfigValue('advanced', 'text_nomore'));
    }

    /**
     * @return mixed
     */
    public function getLoadingEarly()
    {
        return $this->getConfigValue('advanced', 'loading_early');
    }

    /**
     * @return mixed
     */
    public function getNegativeMargin()
    {
        return $this->getConfigValue('advanced', 'negative_margin');
    }

    /**
     * @return string
     */
    public function isEnabledCanonicalPrevNext()
    {
        return $this->getConfigValue('advanced', 'prev_next');
    }

    /**
     * @return string
     */
    public function getLoadingImage()
    {
        $params = [
            '_secure' => $this->_request->isSecure()
        ];

        return $this->_assetRepo->getUrlWithParams('WeltPixel_AjaxInfiniteScroll::images/ias-spinner.gif', $params);
    }

    /**
     * @return mixed
     */
    public function getCurrentCategoryId()
    {
        $category = $this->_registry->registry('current_category');

        if ($category) {
            return $category->getId();
        }

        return $this->_getRootCategoryId();
    }

    /**
     * @return mixed
     */
    protected function _getRootCategoryId() {
        $currentStoreId = $this->_storeManager->getStore()->getId();
        return $this->_storeManager->getStore($currentStoreId)->getRootCategoryId();
    }

    public function getAjaxReloadPaginationUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl() . 'ajaxcatalog/nextpage/reloadpagination/';
    }

    public function getAjaxRefreshCanonicalUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl() . 'ajaxcatalog/canonical/refresh/';
    }

    /**
     * @return string
     */
    public function isEnabledcategoryCanonicalTag()
    {
        // catalog_seo_category_canonical_tag
        return $this->_scopeConfig->getValue(
            'catalog/seo_category/canonical_tag',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlInterface->getCurrentUrl();
    }

    /**
     * @param bool $currentUrl
     * @return int
     */
    public function getCurrentPageNo($currentUrl = false)
    {
        if (!$currentUrl) {
            $currentUrl = $this->getCurrentUrl();
        }
        $currentPageNo = 1;
        $parseUrl = $this->parseUrl($currentUrl);

        if (isset($parseUrl['query'])) {
            parse_str($parseUrl['query'], $output);
            if (isset($output['p'])) {
                $currentPageNo = (int)$output['p'];
            }
        }

        return $currentPageNo;
    }

    /**
     * @param bool $currentPageNo
     * @param bool $currentUrl
     * @return bool|string
     */
    public function getPrevPageUrl($currentPageNo = false, $currentUrl = false)
    {
        if (!$currentPageNo) $currentPageNo = $this->getCurrentPageNo();
        if (!$currentUrl) $currentUrl = $this->getCurrentUrl();

        if ($currentPageNo == 1) {
            return false;
        }

        $prevPageNo = $currentPageNo - 1;
        $pageUrl = $this->removeQueryFromUrl($currentUrl);
        $parseUrl = $this->parseUrl($pageUrl);
        $parseUrl['query'] = 'p=' . $prevPageNo;

        return $this->buildUrl($parseUrl);
    }

    /**
     * @param bool $currentPageNo
     * @param bool $currentUrl
     * @return bool|string
     */
    public function getNextPageUrl($currentPageNo = false, $currentUrl = false)
    {
        if (!$currentPageNo) $currentPageNo = $this->getCurrentPageNo();
        if (!$currentUrl) $currentUrl = $this->getCurrentUrl();

        if ($currentPageNo == $this->getLastPageNo()) {
            return false;
        }

        $nextPageNo = $currentPageNo + 1;
        $pageUrl = $this->removeQueryFromUrl($currentUrl);
        $parseUrl = $this->parseUrl($pageUrl);
        $parseUrl['query'] = 'p=' . $nextPageNo;

        return $this->buildUrl($parseUrl);
    }

    /**
     * @return bool|int
     */
    public function getLastPageNo()
    {
        $currentCategory = $this->_registry->registry('current_category');

        if ($currentCategory) {
            $collection = $currentCategory->getProductCollection();
            $this->_pager
                ->setAvailableLimit($this->_toolbar->getAvailableLimit())
                ->setCollection($collection);

            return $this->_pager->getLastPageNum();
        }

        return false;
    }

    /**
     * @param $url
     * @return mixed
     */
    public function parseUrl($url)
    {
        return parse_url($url);
    }

    /**
     * @param $url
     * @return string
     */
    public function removeQueryFromUrl($url)
    {
        $parseUrl = $this->parseUrl($url);
        if (isset($parseUrl['query'])) {
            unset($parseUrl['query']);
        }

        return $this->buildUrl($parseUrl);
    }

    /**
     * @param array $parts
     * @return string
     */
    function buildUrl(array $parts)
    {
        $url = '';

        if (!empty($parts['scheme'])) $url .= $parts['scheme'] . ':';

        if (isset($parts['host'])) {
            $url .= '//';

            if (preg_match('!^[\da-f]*:[\da-f.:]+$!ui', $parts['host'])) {
                $url .= '[' . $parts['host'] . ']'; // IPv6
            } else {
                $url .= $parts['host'];             // IPv4 or name
            }

            if (isset($parts['port'])) $url .= ':' . $parts['port'];
            if (!empty($parts['path']) && $parts['path'][0] != '/') $url .= '/';
        }

        if (!empty($parts['path'])) $url .= $parts['path'];
        if (isset($parts['query'])) $url .= '?' . $parts['query'];
        if (isset($parts['fragment'])) $url .= '#' . $parts['fragment'];

        return $url;
    }

    /**
     * @param $str
     * @return mixed
     */
    private function replaceQuote($str)
    {
        return str_replace(['\'', '‘', '"'],['&#039;', '&lsquo;', '&#034;'], $str);
    }
}
