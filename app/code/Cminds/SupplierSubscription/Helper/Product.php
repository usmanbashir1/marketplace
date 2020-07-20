<?php

namespace Cminds\SupplierSubscription\Helper;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\Customer;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\FilterBuilder;

class Product extends AbstractHelper
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var SearchCriteriaInterface
     */
    protected $searchCriteria;

    /**
     * @var FilterGroup
     */
    protected $filterGroup;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;


    /**
     * Object initialization.
     *
     * @param   Context                 $context
     * @param   ProductRepository       $productRepository
     * @param   SearchCriteriaInterface $searchCriteria
     * @param   FilterGroup             $filterGroup
     * @param   FilterBuilder           $filterBuilder
     */
    public function __construct(
        Context $context,
        ProductRepository $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        SearchCriteriaInterface $searchCriteria,
        FilterGroup $filterGroup,
        FilterBuilder $filterBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteria = $searchCriteria;
        $this->filterGroup = $filterGroup;
        $this->filterBuilder = $filterBuilder;
        $this->productCollectionFactory = $productCollectionFactory;

        parent::__construct($context);
    }

    /**
     * Get count of products belongs to vendor.
     *
     * @param Customer $customer
     *
     * @return int
     */
    public function countVendorProducts(Customer $customer)
    {
        $this->filterGroup->setFilters([
            $this->filterBuilder
                ->setField('creator_id')
                ->setConditionType('eq')
                ->setValue($customer->getId())
                ->create(),
        ]);

        $this->searchCriteria->setFilterGroups([$this->filterGroup]);
        $products = $this->productRepository->getList($this->searchCriteria);

        return $products->getTotalCount();
    }

    /**
     * @param $customer
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function disableProductsFromExpiredVendor($customer)
    {
        $products = $this->productCollectionFactory->create()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToFilter('creator_id', ['eq' => $customer->getId()])
            ->load();

        foreach ($products as $product) {
            $product = $this->productRepository->getById($product->getId());
            $product->setCustomAttribute('disabled_supplier_subscription_expired', 1);
            $product->setStatus(Status::STATUS_DISABLED);
            $this->productRepository->save($product);
        }
    }

    /**
     * @param $customer
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function enableProductsFromExpiredVendor($customer)
    {
        $products = $this->productCollectionFactory->create()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToFilter('creator_id', ['eq' => $customer->getId()])
            ->addAttributeToFilter('disabled_supplier_subscription_expired', ['eq' => 1])
            ->load();

        foreach ($products as $product) {
            $product = $this->productRepository->getById($product->getId());
            $product->setCustomAttribute('disabled_supplier_subscription_expired', 0);
            $product->setStatus(Status::STATUS_ENABLED);
            $this->productRepository->save($product);
        }
    }
}
