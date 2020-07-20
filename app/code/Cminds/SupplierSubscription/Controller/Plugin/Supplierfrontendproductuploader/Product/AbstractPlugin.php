<?php

namespace Cminds\SupplierSubscription\Controller\Plugin\Supplierfrontendproductuploader\Product;

use Cminds\SupplierSubscription\Model\Plan;
use Cminds\SupplierSubscription\Model\PlanFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session\Proxy as CustomerSession;

abstract class AbstractPlugin
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Customer
     */
    protected $currentCustomer;

    /**
     * Subscription plan model factory.
     *
     * @var PlanFactory
     */
    protected $planFactory;

    /**
     * Subscription plan object of current customer.
     *
     * @var Plan
     */
    protected $currentPlan;

    /**
     * Object initialization.
     *
     * @param   CustomerSession $customerSession
     * @param   PlanFactory     $planFactory
     */
    public function __construct(
        CustomerSession $customerSession,
        PlanFactory $planFactory
    ) {
        $this->customerSession = $customerSession;
        $this->planFactory = $planFactory;
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
