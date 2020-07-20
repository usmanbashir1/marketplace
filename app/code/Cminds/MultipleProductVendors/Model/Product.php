<?php

namespace Cminds\MultipleProductVendors\Model;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Api\ProductCustomOptionRepositoryInterface;
use Magento\Catalog\Model\Product as MagentoProduct;

class Product
{
    const DISABLE_PRODUCT = 0;
    const PRODUCT_IS_MAIN = 1;
    const PRODUCT_IS_NOT_MAIN = 0;

    /**
     * Product collection factory.
     *
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Option Repository.
     *
     * @var ProductCustomOptionRepositoryInterface
     */
    private $optionRepository;

    /**
     * Product constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ProductCustomOptionRepositoryInterface $optionRepository
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->optionRepository = $optionRepository;
    }

    /**
     * Get main manufacturer product by manufacturer code.
     *
     * @param string $code
     *
     * @return \Magento\Framework\DataObject|null
     */
    public function getMainProductByCode($code)
    {
        $mainProduct = $this->collectionFactory->create()
            ->addFieldToFilter('manufacturer_code', $code)
            ->addFieldToFilter('main_product', static::PRODUCT_IS_MAIN)
            ->setPageSize(1)
            ->setCurPage(1)
            ->load();

        if (count($mainProduct) === 0) {
            return null;
        }

        return $mainProduct->getFirstItem();
    }

    /**
     * Retrieve customizable options of product.
     *
     * @param MagentoProduct $product
     *
     * @return ProductCustomOptionInterface[]
     */
    public function getCustomizableOptions(MagentoProduct $product)
    {
        return $this->optionRepository->getProductOptions($product);
    }

    /**
     * Delete customizable option option.
     *
     * @param ProductCustomOptionInterface $option
     *
     * @return Product
     */
    public function deleteCustomizableOption(ProductCustomOptionInterface $option)
    {
        $this->optionRepository->delete($option);

        return $this;
    }

    /**
     * Save customizable option.
     *
     * @param ProductCustomOptionInterface $option
     *
     * @return Product
     */
    public function saveCustomizableOption(ProductCustomOptionInterface $option)
    {
        $this->optionRepository->save($option);

        return $this;
    }
}
