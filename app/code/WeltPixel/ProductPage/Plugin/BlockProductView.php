<?php

namespace WeltPixel\ProductPage\Plugin;

class BlockProductView
{

    const XML_PATH_WELTPIXEL_PRODUCTPAGE_REMOVE_QTY = 'weltpixel_product_page/general/remove_qty_box';
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_REMOVE_EMAIL = 'weltpixel_product_page/general/remove_email';


    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $productTypeConfig;
    
    /**
     *
     * @var  \Magento\Framework\App\Request\Http 
     */
    protected $request;
    
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
            \Magento\Framework\App\Request\Http $request)
    {
        $this->scopeConfig = $scopeConfig;
        $this->productTypeConfig = $productTypeConfig;
        $this->request = $request;
    }

   /**
    * 
    * @param \Magento\Catalog\Block\Product\View $subject
    * @param bool $result
    * @return bool
    */
    public function afterShouldRenderQuantity(
        \Magento\Catalog\Block\Product\View $subject, $result)
    {
        $productType = $subject->getProduct()->getTypeId();
        $isProductSet = $this->productTypeConfig->isProductSet($productType);

        if (!$isProductSet && $this->request->getFullActionName() == 'catalog_product_view') {
            $removeQtySelector = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_REMOVE_QTY,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            return !$removeQtySelector;
        }
        
        return $result;
    }

    /**
     * @param \Magento\Catalog\Block\Product\View $subject
     * @param bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCanEmailToFriend(\Magento\Catalog\Block\Product\View $subject, $result)
    {
        if ($result && $this->request->getFullActionName() == 'catalog_product_view') {
            $removeEmail = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_REMOVE_EMAIL,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($removeEmail) {
                $result = false;
            }
        }

        return $result;
    }

}
