<?php

namespace Cminds\MultipleProductVendors\Block\Catalog\Product;

use Magento\Catalog\Block\Product\View as ProductView;

use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Url\EncoderInterface as UrlEncoderInterface;
use Magento\Framework\Json\EncoderInterface as JsonEncoderInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

use Cminds\MultipleProductVendors\Helper\Data as MultiVendorHelper;
use Cminds\Supplierfrontendproductuploader\Helper\Data as SupplierFrontHelper;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Customer\Model\CustomerFactory;

class View extends ProductView
{
    /**
     * Product collection.
     */
    protected $productCollection = null;

    /**
     * Multi Vendor module helper.
     *
     * @var MultiVendorHelper
     */
    protected $multiVendorHelper;

    /**
     * Product collection factory.
     *
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Product visibility model.
     *
     * @var ProductVisibility
     */
    protected $productVisibility;

    /**
     * Product list block.
     *
     * @var ListProduct
     */
    protected $listProductBlock;
    
    /**
     * Supplierfrontendproductuploader helper.
     *
     * @var SupplierFrontHelper
     */
    protected $supplierFrontHelper;

    /**
     * Customer Factory.
     *
     * @var CustomerFactory
     */
    protected $customerFactory;


    /**
     * @param Context $context
     * @param UrlEncoderInterface $urlEncoder
     * @param JsonEncoderInterface $jsonEncoder
     * @param StringUtils $string
     * @param ProductHelper $productHelper
     * @param ConfigInterface $productTypeConfig
     * @param FormatInterface $localeFormat
     * @param CustomerSession $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param MultiVendorHelper $multiVendorHelper
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductVisibility $productVisibility
     * @param ListProduct $listBlock
     * @param SupplierFrontHelper $supplierFrontHelper
     * @param CustomerFactory $customerFactory
     * @param array $data
     * @codingStandardsIgnoreStart
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        UrlEncoderInterface $urlEncoder,
        JsonEncoderInterface $jsonEncoder,
        StringUtils $string,
        ProductHelper $productHelper,
        ConfigInterface $productTypeConfig,
        FormatInterface $localeFormat,
        CustomerSession $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        MultiVendorHelper $multiVendorHelper,
        ProductCollectionFactory $productCollectionFactory,
        ProductVisibility $productVisibility,
        ListProduct $listBlock,
        SupplierFrontHelper $supplierFrontHelper,
        CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->multiVendorHelper = $multiVendorHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->listProductBlock = $listBlock;
        $this->supplierFrontHelper = $supplierFrontHelper;
        $this->customerFactory = $customerFactory;

        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }

    /**
     * Check config status
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->multiVendorHelper->isEnabled();
    }

    /**
     * check if creation block should be shown
     *
     * @return bool
     */
    public function showBlock()
    {
        $result = $this->isEnabled();
        // if module enabled, check product availability

        if(true === $result
            && $this->getProductCollection()->count() === 0
        ){
            $result = false;
        }
        return $result;
    }

    /**
     * Get product collection ( sku, name, manufacturer code).
     *
     * @return Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        $product = $this->getProduct();

        if( ( $manufacturerCode = $product->getData('manufacturer_code') )
            && null === $this->productCollection
        ){
            $this->productCollection = $this->productCollectionFactory->create();
            $this->productCollection
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('manufacturer_code', $manufacturerCode)
                ->addAttributeToFilter('main_product', 0);

            // filter current website products
            $this->productCollection->addWebsiteFilter();

            // filter current store products
            $this->productCollection->addStoreFilter();
        }
        return $this->productCollection;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        return $this->listProductBlock->getAddToCartUrl($product);
    }
    
    /**
     * Check if vendor page config enabled
     * @return bool
     */
    public function isSupplierPageEnabled()
    {
        return $this->multiVendorHelper->isSupplierPageEnabled();
    }
    
    /**
     * Get supplier name
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @return string|bool
     */
    public function getSupplierData($product)
    {
        $result = null;
        // if( $this->multiVendorHelper->canShowSoldBy() ){
            $supplierId = $product->getCreatorId();
            if (!$supplierId) {
                return false;
            }
            
            $customer = $this->customerFactory->create()->load($supplierId);
            if (!$customer->getId()) {
                return false;
            }

            if ($customer->getSupplierName()) {
                $result['name'] = $customer->getSupplierName();
            } else {
                $result['name'] = sprintf(
                    '%s %s',
                    $customer->getFirstname(),
                    $customer->getLastname()
                );
            }
            if ($this->isSupplierPageEnabled()) {
                $result['link'] = $this->supplierFrontHelper->getSupplierRawPageUrl($supplierId);
            }
        // }
        return $result;
        
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams($product)
    {
        return $this->listProductBlock->getAddToCartPostParams($product);
    }

}