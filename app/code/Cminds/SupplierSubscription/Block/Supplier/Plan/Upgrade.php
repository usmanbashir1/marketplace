<?php

namespace Cminds\SupplierSubscription\Block\Supplier\Plan;

use Cminds\SupplierSubscription\Block\Supplier\Plan;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Cminds\SupplierSubscription\Model\PlanFactory;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Cminds\SupplierSubscription\Model\ResourceModel\Plan\CollectionFactory as PlanCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Cminds\SupplierSubscription\Model\Plan as PlanObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\ResourceModel\Product\Collection\Interceptor;
use Magento\Store\Model\StoreManagerInterface;
use Cminds\SupplierSubscription\Helper\Data as SubscriptionHelper;

class Upgrade extends Plan
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var PlanCollectionFactory
     */
    protected $planCollectionFactory;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * Object initialization.
     *
     * @param   Context $context
     * @param   CustomerSession $customerSession
     * @param   PlanFactory $planFactory
     * @param   PriceHelper $priceHelper
     * @param   CurrencyFactory $currencyFactory
     * @param   ProductRepository $productRepository
     * @param   CollectionFactory $productCollectionFactory
     * @param   PlanCollectionFactory $planCollectionFactory
     * @param   ResourceConnection $resource
     * @param   StoreManagerInterface $storeManager
     * @param   SubscriptionHelper $subscriptionHelper
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        PlanFactory $planFactory,
        PriceHelper $priceHelper,
        CurrencyFactory $currencyFactory,
        ProductRepository $productRepository,
        CollectionFactory $productCollectionFactory,
        PlanCollectionFactory $planCollectionFactory,
        ResourceConnection $resource,
        StoreManagerInterface $storeManager,
        SubscriptionHelper $subscriptionHelper
    ) {
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->planCollectionFactory = $planCollectionFactory;
        $this->resource = $resource;

        parent::__construct($context, $customerSession, $planFactory, $priceHelper, $currencyFactory, $storeManager, $subscriptionHelper);
    }

    /**
     * Returns filtered and sorted plans as collection of products with plans model included.
     *
     * @return Interceptor
     *
     * @throws LocalizedException
     */
    public function getProductPlanCollection()
    {
        $subscriptionPlans = $this->planCollectionFactory->create();
        $planProductIds = $subscriptionPlans->getColumnValues('product_id');
        $productCollection = $this->productCollectionFactory->create();
        $productCollection
            ->addAttributeToFilter('entity_id', ['in' => $planProductIds])
            ->joinField(
                'plan_id',
                $this->resource->getTableName('cminds_suppliersubscription_plan'),
                'entity_id',
                'product_id=entity_id'
            );

        foreach ($productCollection as $product) {
            /* @var PlanObject $plan */
            $plan = $this->planFactory->create()->load($product->getPlanId());
            if (!$plan || !$plan->getId()) {
                $productCollection->removeItemByKey($product->getId());
                continue;
            }

            $product->load($product->getId());
            $product->setPlan($plan);
        }

        return $productCollection;
    }

    /**
     * Get plan data as array.
     *
     * @return array
     */
    public function getPlansFeatures()
    {
        return [
            'products_number' => __('Number of Products'),
            'images_number' => __('Number of Images Per Products'),
            'price' => '',
        ];
    }

    /**
     * Is current plan selected.
     *
     * @param int $planId
     *
     * @return bool
     */
    public function isSelected($planId)
    {
        $currentPlan = $this->getCurrentPlan();

        if (!$currentPlan || !$currentPlan->getId()) {
            return false;
        }

        if ((int)$currentPlan->getId() !== (int)$planId) {
            return false;
        }

        return true;
    }
}
