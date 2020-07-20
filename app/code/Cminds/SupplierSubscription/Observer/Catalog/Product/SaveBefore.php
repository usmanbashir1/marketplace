<?php

namespace Cminds\SupplierSubscription\Observer\Catalog\Product;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Catalog\Model\Product\Interceptor;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\Customer;
use Cminds\Supplierfrontendproductuploader\Helper\Data as SupplierHelper;
use Cminds\SupplierSubscription\Model\PlanFactory;
use Cminds\SupplierSubscription\Model\Plan;
use Cminds\SupplierSubscription\Helper\Data as SubscriptionHelper;

class SaveBefore implements ObserverInterface
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var SubscriptionHelper
     */
    public $subscriptionHelper;

    /**
     * @var SupplierHelper
     */
    public $supplierHelper;

    /**
     * @var Customer
     */
    protected $currentCustomer;

    /**
     * @var Plan
     */
    protected $currentPlan;

    /**
     * Subscription plan model factory.
     *
     * @var PlanFactory
     */
    protected $planFactory;

    /**
     * SaveBefore constructor.
     *
     * @param SubscriptionHelper $subscriptionHelper
     * @param SupplierHelper $supplierHelper
     * @param CustomerSession $customerSession
     * @param PlanFactory $planFactory
     */
    public function __construct(
        SubscriptionHelper $subscriptionHelper,
        SupplierHelper $supplierHelper,
        CustomerSession $customerSession,
        PlanFactory $planFactory
    ) {
        $this->subscriptionHelper = $subscriptionHelper;
        $this->supplierHelper = $supplierHelper;
        $this->customerSession = $customerSession;
        $this->planFactory = $planFactory;
    }

    /**
     * Validate subscription plan limit.
     *
     * @param Observer $observer
     *
     * @return $this
     *
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Interceptor $product */
        $product = $observer->getProduct();

        if ($product->isObjectNew() === false) {
            return $this;
        }

        if ($this->subscriptionHelper->isEnabled() === false) {
            return $this;
        }

        $customer = $this->getCustomer();
        if (!$customer || !$customer->getId()) {
            return $this;
        }

        if (!$this->supplierHelper->isSupplier($customer->getId())) {
            return $this;
        }

        $this->subscriptionHelper->checkCustomerDefaultPlan($this->getCustomer());
        $currentPlan = $this->getCurrentPlan();
        $validateProductsLimit = $currentPlan->validateProductsLimit($this->getCustomer());
        if (!$validateProductsLimit) {
            throw new \Exception('Subscription plan products limit has been reached.');
        }

        return $this;
    }

    /**
     * Get customer from session.
     *
     * @return Customer
     */
    public function getCustomer()
    {
        if ($this->currentCustomer === null) {
            $this->currentCustomer = $this->customerSession->getCustomer();
        }

        return $this->currentCustomer;
    }

    /**
     * Get current plan of customer.
     *
     * @return Plan
     */
    public function getCurrentPlan()
    {
        if ($this->currentPlan === null) {
            $customer = $this->getCustomer();
            $planId = $customer->getCurrentPlanId();
            $this->currentPlan = $this->planFactory->create()->load($planId);
        }

        return $this->currentPlan;
    }
}
