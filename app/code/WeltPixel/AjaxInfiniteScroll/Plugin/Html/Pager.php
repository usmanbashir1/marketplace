<?php

namespace WeltPixel\AjaxInfiniteScroll\Plugin\Html;

use Magento\Framework\App\Request\Http;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use WeltPixel\AjaxInfiniteScroll\Helper\Data;

class Pager
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    protected $toolbar;

    /**
     * @var \WeltPixel\AjaxInfiniteScroll\Helper\Data
     */
    protected $helper;

    /**
     * Pager constructor.
     * @param Http $request
     * @param Toolbar $toolbar
     * @param Data $helper
     */
    public function __construct(
        Http $request,
        Toolbar $toolbar,
        Data $helper
    )
    {
        $this->request = $request;
        $this->toolbar = $toolbar;
        $this->helper = $helper;
    }

    /**
     * Retrieve page URL
     *
     * @param \Magento\Catalog\Block\Product\Widget\Html\Pager $subject
     * @param \Closure $proceed
     * @param $page
     * @return mixed|string
     */
    public function aroundGetPageUrl(
        \Magento\Catalog\Block\Product\Widget\Html\Pager $subject,
        \Closure $proceed,
        $page
    )
    {
        if ($this->isAjax('pager_url')) {
            $params = $this->getRequestParams();
            $currentUrl = $params['pager_url'];
            $paramSep = $this->defineParamsSeparator($currentUrl);

            return $currentUrl . $paramSep . 'p=' .  $page;
        }

        return $proceed($page);
    }

    /**
     * Retrieve pager limit
     *
     * @param \Magento\Catalog\Block\Product\Widget\Html\Pager $subject
     * @param \Closure $proceed
     * @return array|mixed
     */
    public function aroundGetAvailableLimit(
        \Magento\Catalog\Block\Product\Widget\Html\Pager $subject,
        \Closure $proceed
    )
    {
        if ($this->isAjax()) {
            return $this->toolbar->getAvailableLimit();
        }

        return $proceed();
    }

    /**
     * Retrieve pager url with limit param
     *
     * @param \Magento\Catalog\Block\Product\Widget\Html\Pager $subject
     * @param \Closure $proceed
     * @param $limit
     * @return mixed|string
     */
    public function aroundGetLimitUrl(
        \Magento\Catalog\Block\Product\Widget\Html\Pager $subject,
        \Closure $proceed,
        $limit
    )
    {
        if ($this->isAjax('pager_url')) {
            $params = $this->getRequestParams();
            $currentUrl = $this->helper->removeQueryFromUrl($params['pager_url']);

            return $currentUrl . '?' . 'product_list_' . $subject->getLimitVarName() . '=' . $limit;
        }

        return $proceed($limit);
    }

    /**
     * @param \Magento\Catalog\Block\Product\Widget\Html\Pager $subject
     * @param \Closure $proceed
     * @param $limit
     * @return bool|mixed
     */
    public function aroundIsLimitCurrent(
        \Magento\Catalog\Block\Product\Widget\Html\Pager $subject,
        \Closure $proceed,
        $limit
    )
    {
        if ($this->isAjax()) {
            $currentLimit = $this->getLimitFromUrl($subject) ?
                $this->getLimitFromUrl($subject) :
                $this->toolbar->getLimit();

            return $limit == $currentLimit;
        }

        return $proceed($limit);
    }

    /**
     * @param \Magento\Catalog\Block\Product\Widget\Html\Pager $subject
     * @param \Closure $proceed
     * @return bool|mixed|string
     */
    public function aroundGetLimit(
        \Magento\Catalog\Block\Product\Widget\Html\Pager $subject,
        \Closure $proceed
    )
    {
        if ($this->isAjax('pager_url')) {
            $limit = $this->getLimitFromUrl($subject) ?
                $this->getLimitFromUrl($subject) :
                $this->toolbar->getLimit();

            if ($limit) return $limit;

            /**
             * if limit was not found try to get the limit from url
             * or return the first available
             */
            $params = $this->getRequestParams();
            $limits = $this->toolbar->getAvailableLimit();
            if (strpos($params['pager_url'], 'product_list_' . $subject->getLimitVarName()) !== false) {
                parse_str(parse_url($params['pager_url'], PHP_URL_QUERY), $output);
                if (
                    isset($output['product_list_' . $subject->getLimitVarName()]) &&
                    $limit == $output['product_list_' . $subject->getLimitVarName()] &&
                    isset($limits[$limit])
                ) {
                    return $limit;
                }
            }

            $limits = array_keys($limits);

            return $limits[0];
        }

        return $proceed();
    }

    /**
     * Whether to show first page in pagination or not
     *
     * @return bool
     */
    public function aroundCanShowFirst(
        \Magento\Catalog\Block\Product\Widget\Html\Pager $subject,
        \Closure $proceed
    )
    {
        if ($this->isAjax()) {
            return false;
        }

        return $proceed();
    }

    /**
     * Whether to show last page in pagination or not
     *
     * @return bool
     */
    public function aroundCanShowLast(
        \Magento\Catalog\Block\Product\Widget\Html\Pager $subject,
        \Closure $proceed
    )
    {
        if ($this->isAjax()) {
            return false;
        }

        return $proceed();
    }

    /**
     * @return array
     */
    private function getRequestParams()
    {
        return $this->request->getParams();
    }

    /**
     * @param bool $checkParam
     * @return bool
     */
    private function isAjax($checkParam = false)
    {
        $params = $this->request->getParams();
        if (isset($params['is_ajax'])) {
            if ($checkParam && !isset($params[$checkParam])) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @param $url
     * @return string
     */
    private function defineParamsSeparator($url)
    {
        return strpos($url, '?') !== false ? '&' : '?';
    }

    /**
     * @param $subject`
     * @return bool
     */
    private function getLimitFromUrl($subject)
    {
        if ($this->isAjax('pager_url')) {
            $params = $this->getRequestParams();
            parse_str(parse_url($params['pager_url'], PHP_URL_QUERY), $output);

            if (isset($output['product_list_' . $subject->getLimitVarName()])) {
                return $output['product_list_' . $subject->getLimitVarName()];
            }
        }

        return false;
    }
}