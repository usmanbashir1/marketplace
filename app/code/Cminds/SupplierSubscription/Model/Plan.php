<?php

namespace Cminds\SupplierSubscription\Model;

use Cminds\SupplierSubscription\Helper\Product as ProductHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Plan extends AbstractModel
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var Product
     */
    protected $productPlan;

    /**
     * @var Product
     */
    protected $productHelper;

    /**
     * Plan constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ProductRepository $productRepository
     * @param ProductHelper $productHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProductRepository $productRepository,
        ProductHelper $productHelper
    ) {
        $this->productRepository = $productRepository;
        $this->productHelper = $productHelper;

        parent::__construct(
            $context,
            $registry
        );
    }

    /**
     * Model construct that should be used for object initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        // Set resource model.
        $this->_init('Cminds\SupplierSubscription\Model\ResourceModel\Plan');
    }

    /**
     * Validate if plan has related virtual product.
     *
     * @return bool
     */
    public function validateVirtualProduct()
    {
        if (!$this->getProductId()) {
            return false;
        }

        $product = $this->getProduct();

        if (!$product->getId()) {
            return false;
        }

        if ($product->getTypeId() !== 'virtual') {
            return false;
        }

        return true;
    }

    /**
     * Get product related to subscription plan.
     *
     * @return Product
     * @throws NoSuchEntityException
     */
    public function getProduct()
    {
        if ($this->productPlan === null) {
            $product = $this->productRepository->getById($this->getProductId());
            $this->productPlan = $product;
        }

        return $this->productPlan;
    }

    /**
     * Validate if customer products reached the limit plan.
     *
     * @param Customer $customer
     * @return bool
     */
    public function validateProductsLimit(Customer $customer)
    {
        $productsLimit = $this->getData('products_number');
        $vendorProductsNumber = $this->productHelper->countVendorProducts($customer);

        if ($vendorProductsNumber >= $productsLimit) {
            return false;
        }

        return true;
    }
}
