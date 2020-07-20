<?php

namespace Cminds\MultipleProductVendors\Block\Product;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Eav\Model\Entity\AttributeFactory;
use Cminds\Supplierfrontendproductuploader\Model\Labels as SupplierLabels;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Registry;
use Magento\CatalogInventory\Api\StockStateInterface;

use Cminds\MultipleProductVendors\Helper\Data as MultiVendorHelper;

class Create extends Template
{
    /**
     * Product collection.
     */
    protected $productCollection = null;

    /**
     * Current product object.
     */
    protected $currentProduct = null;

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
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var SupplierLabels
     */
    protected $supplierLabels;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var StockStateInterface
     */
    protected $stockState;

    /**
     * Object initialization.
     *
     * @param   Context $context
     * @param   ProductCollectionFactory $productCollectionFactory
     * @param   ProductVisibility $productVisibility
     * @param   MultiVendorHelper $multiVendorHelper
     * @param   AttributeFactory $attributeFactory
     * @param   SupplierLabels $supplierLabels
     * @param   Registry $registry
     * @param   ProductFactory $productFactory
     * @param   StockStateInterface $stockState
     */
    public function __construct(
        Context $context,
        ProductCollectionFactory $productCollectionFactory,
        ProductVisibility $productVisibility,
        MultiVendorHelper $multiVendorHelper,
        AttributeFactory $attributeFactory,
        SupplierLabels $supplierLabels,
        Registry $registry,
        ProductFactory $productFactory,
        StockStateInterface $stockState
    ) {
        $this->multiVendorHelper = $multiVendorHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->attributeFactory = $attributeFactory;
        $this->supplierLabels = $supplierLabels;
        $this->productFactory = $productFactory;
        $this->registry = $registry;
        $this->stockState = $stockState;

        parent::__construct($context);
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
        if( null === $this->productCollection ){
            $this->productCollection = $this->productCollectionFactory->create();
            $this->productCollection
                ->addAttributeToSelect(['sku', 'name', 'manufacturer_code', 'main_product'])
                ->addAttributeToFilter('main_product', 1);

            // filter current website products
            $this->productCollection->addWebsiteFilter();

            // filter current store products
            $this->productCollection->addStoreFilter();

            // set visibility filter
            $this->productCollection
                ->setVisibility($this->productVisibility->getVisibleInSiteIds());

        }
        return $this->productCollection;
    }

    /**
     * Get product create link by master product id.
     *
     * @return string
     */

    public function getCreateLink()
    {
        return $this->getUrl('mpvendors/product/create');
    }

     /**
     * Get label for attribute.
     *
     * @param $attribute
     * @param $force
     * @param $loaded
     *
     * @return Phrase|string
     */
    public function getLabel($attribute, $force = '', $loaded = true)
    {
        if (!$loaded) {
            $attribute = $this->attributeFactory->create()
                ->loadByCode(4, $attribute);
        }

        if (!is_object($attribute)) {
            return $force;
        }

        $label = $this->supplierLabels
            ->load($attribute->getAttributeCode(), 'attribute_code');

        if ($label->getId() === null) {
            if ($force != '' && $force != null) {
                return __($force);
            } else {
                return $attribute->getFrontend()->getLabel();
            }
        } else {
            if ($label->getLabel() === '' || $label->getLabel() === null) {
                if ($force != '' && $force != null) {
                    return __($force);
                } else {
                    return $attribute->getFrontend()->getLabel();
                }
            } else {
                return $label->getLabel();
            }
        }
    }

    /**
     * Get current loaded product.
     *
     * @return bool|\Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if( null === $this->currentProduct ){
            $id = $this->registry->registry('supplier_product_id');
            if($id)
                $this->currentProduct = $this->productFactory->create()->load($id);
        }
        return $this->currentProduct;
    }

    /**
     * Check if it's product edit page.
     *
     * @return bool
     */
    public function isProductEdit()
    {
        return (bool) $this->registry->registry('supplier_product_id');
    }

    /**
     * Check if it's product edit page.
     *
     * @return string
     */
    public function getQty()
    {
        $product = $this->getProduct();

        $result = '';
        if($product){
            $result = $this->stockState->getStockQty(
                $product->getId(),
                $product->getStore()->getWebsiteId()
            );
        }
        return $result;
    }

    /**
     * Check if it's product edit page.
     *
     * @return int|string
     */
    public function getMainProductId()
    {
        $product = $this->getProduct();
        $result = '';
        if($product){
            $possibleProducts = $this->productCollectionFactory->create();
            $possibleProducts
                ->addAttributeToSelect(['sku', 'manufacturer_code', 'main_product' ])
                ->addAttributeToFilter('manufacturer_code', $product->getData('manufacturer_code'))
                ->addAttributeToFilter('main_product', 1);
            // product found
            if($possibleProducts->count()){
                $parentProduct = $possibleProducts->getFirstItem();
                $result = $parentProduct->getId();
            }

        }
        return $result;
    }
}
