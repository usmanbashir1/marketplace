<?php

namespace Cminds\SupplierSubscription\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Cminds\SupplierSubscription\Helper\Data as SubscriptionHelper;
use Cminds\Supplierfrontendproductuploader\Helper\Data as SupplierHelper;

class Login implements ObserverInterface
{
    /**
     * SupplierSubscription data helper.
     *
     * @var SubscriptionHelper
     */
    public $subscriptionHelper;

    /**
     * Supplierfrontendproductuploader data helper.
     *
     * @var SupplierHelper
     */
    public $supplierHelper;

    /**
     * Login constructor.
     *
     * @param SubscriptionHelper $subscriptionHelper
     * @param SupplierHelper $supplierHelper
     */
    public function __construct(
        SubscriptionHelper $subscriptionHelper,
        SupplierHelper $supplierHelper
    ) {
        $this->subscriptionHelper = $subscriptionHelper;
        $this->supplierHelper = $supplierHelper;
    }

    /**
     * Set default subscription plan to supplier if has no one.
     *
     * @param Observer $observer
     *
     * @return Login
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getCustomer();

        if ($this->subscriptionHelper->isEnabled() === false) {
            return $this;
        }
        if ($this->supplierHelper->isEnabled() === false
            || $this->supplierHelper->isSupplier($customer->getId()) === false
        ) {
            return $this;
        }

        // add default plan
        $this->subscriptionHelper->checkCustomerDefaultPlan($customer);

        return $this;
    }
}
