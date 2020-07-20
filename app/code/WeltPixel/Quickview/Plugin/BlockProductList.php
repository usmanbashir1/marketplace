<?php

namespace WeltPixel\Quickview\Plugin;

class BlockProductList
{
    const XML_PATH_QUICKVIEW_ENABLED = 'weltpixel_quickview/general/enable_product_listing';
    const XML_PATH_QUICKVIEW_ENABLED_ON_SEARCH = 'weltpixel_quickview/general/enable_product_search';
    const XML_PATH_QUICKVIEW_BUTTONSTYLE = 'weltpixel_quickview/general/button_style';
    const XML_PATH_QUICKVIEW_NOFOLLOW = 'weltpixel_quickview/seo/no_follow';


    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \WeltPixel\Quickview\Helper\Data $helper
     */
    protected $helper;

    /**
     *
     * @var  \Magento\Framework\App\Request\Http
     */
    protected $request;


    /**
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param  \Magento\Framework\App\Request\Http $request
     * @param \WeltPixel\Quickview\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $request,
        \WeltPixel\Quickview\Helper\Data $helper
        ) {
        $this->urlInterface = $urlInterface;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->helper = $helper;
    }

    public function aroundGetProductDetailsHtml(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
        )
    {
        $noFollow = '';
        $result = $proceed($product);
        $isEnabled = $this->scopeConfig->getValue(self::XML_PATH_QUICKVIEW_ENABLED,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $isEnabledOnSearch = $this->scopeConfig->getValue(self::XML_PATH_QUICKVIEW_ENABLED_ON_SEARCH,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($isEnabled) {
            $requestAction = $this->request->getFullActionName();
            if (in_array($requestAction, ['catalogsearch_result_index', 'catalogsearch_advanced_result']) && !$isEnabledOnSearch) {
                return $result;
            }

            $buttonStyle =  'weltpixel_quickview_button_' . $this->scopeConfig->getValue(self::XML_PATH_QUICKVIEW_BUTTONSTYLE,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $noFollowEnabled = $this->scopeConfig->getValue(self::XML_PATH_QUICKVIEW_NOFOLLOW,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($noFollowEnabled) {
                $noFollow = ' rel="nofollow"';
            }
            $productUrl = $this->urlInterface->getUrl('weltpixel_quickview/catalog_product/view', array('id' => $product->getId()));
            return $result . '<a ' . $noFollow . ' class="weltpixel-quickview '.$buttonStyle.'" data-quickview-url="' . $productUrl . '" href="javascript:void(0);"><span>' . __("Quickview") . '</span></a>';
        }

        return $result;
    }

    /**
     *
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param bool $result
     * @return bool
     */
    public function afterGetAdditionalHtml(
        \Magento\Catalog\Block\Product\ListProduct $subject, $result)
    {
        if ($this->helper->isEnabled() && $this->helper->isPrevNextEnabled()) {
            $result .= $subject->getChildHtml('categorysearch.additional');
        }

        return $result;
    }
}
