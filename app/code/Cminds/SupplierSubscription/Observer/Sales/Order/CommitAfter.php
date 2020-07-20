<?php

namespace Cminds\SupplierSubscription\Observer\Sales\Order;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Sales\Model\Order;
use Cminds\Supplierfrontendproductuploader\Helper\Data as SupplierHelper;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Cminds\SupplierSubscription\Model\ResourceModel\Plan\CollectionFactory as PlanCollectionFactory;
use Magento\Customer\Model\Data\Customer;
use Cminds\SupplierSubscription\Helper\Product as ProductHelper;

class CommitAfter implements ObserverInterface
{
    /**
     * @var SupplierHelper
     */
    protected $supplierHelper;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var PlanCollectionFactory
     */
    protected $planCollectionFactory;

    /**
     * @var ProductHelper
     */
    protected $productHelper;

    /**
     * CommitAfter constructor.
     *
     * @param SupplierHelper $supplierHelper
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param PlanCollectionFactory $planCollectionFactory
     * @param ProductHelper $productHelper
     */
    public function __construct(
        SupplierHelper $supplierHelper,
        CustomerRepositoryInterface $customerRepositoryInterface,
        PlanCollectionFactory $planCollectionFactory,
        ProductHelper $productHelper
    ) {
        $this->supplierHelper = $supplierHelper;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->planCollectionFactory = $planCollectionFactory;
        $this->productHelper = $productHelper;
    }

    /**
     * Upgrade/renew customer's subscription plan.
     *
     * @param Observer $observer
     *
     * @return CommitAfter
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws InputException
     * @throws InputMismatchException
     */
    public function execute(Observer $observer)
    {
        if ($this->supplierHelper->isEnabled() === false) {
            return $this;
        }

        $order = $observer->getOrder();
        $customerId = $order->getCustomerId();
        if (!$customerId) {
            return $this;
        }

        if ($order->getState() !== Order::STATE_COMPLETE) {
            return $this;
        }

        $plans = $this->getPlansCollection();
        $orderItems = $order->getAllItems();
        foreach ($orderItems as $orderItem) {
            if (!isset($plans[$orderItem->getProductId()])) {
                continue;
            }

            /** @var Customer $customer */
            $customer = $this->customerRepositoryInterface->getById($customerId);
            $customerPlanId = $customer->getCustomAttribute('current_plan_id') ? $customer->getCustomAttribute('current_plan_id')->getValue() : 0;
            $currentPlan = $plans[$orderItem->getProductId()];
            $qty = (int) $orderItem->getQtyOrdered();

            $time = time();
            if ((int)$customerPlanId === (int)$currentPlan->getId()) {
                $time = strtotime($customer->getPlanToDate());
            }

            $planToDate = date('Y-m-d H:i:s', strtotime("+ {$qty} month", $time));
            $customer
                ->setCustomAttribute('plan_from_date', date('Y-m-d H:i:s'))
                ->setCustomAttribute('plan_to_date', $planToDate)
                ->setCustomAttribute('current_plan_id', $currentPlan->getId());

            $this->customerRepositoryInterface->save($customer);

            $this->productHelper->enableProductsFromExpiredVendor($customer);
        }

        return $this;
    }

    /**
     * Get all subscription plans collection.
     *
     * @return array
     */
    public function getPlansCollection()
    {
        $plans = [];

        $subscriptionPlans = $this->planCollectionFactory->create();
        foreach ($subscriptionPlans as $plan) {
            $plans[$plan->getProductId()] = $plan;
        }

        return $plans;
    }
}
