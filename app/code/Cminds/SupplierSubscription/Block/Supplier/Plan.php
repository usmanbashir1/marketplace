<?php

namespace Cminds\SupplierSubscription\Block\Supplier;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template\Context;
use Cminds\SupplierSubscription\Model\PlanFactory;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Cminds\SupplierSubscription\Model\Plan as SubscriptionPlan;
use Magento\Customer\Model\Customer;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Store\Model\StoreManagerInterface;
use Cminds\SupplierSubscription\Helper\Data as SubscriptionHelper;

abstract class Plan extends Template
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Subscription plan model factory.
     *
     * @var PlanFactory
     */
    protected $planFactory;

    /**
     * @var PriceHelper
     */
    public $priceHelper;

    /**
     * @var Customer
     */
    public $currentCustomer;

    /**
     * @var Currency
     */
    public $currency;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var SubscriptionHelper
     */
    public $subscriptionHelper;

    /**
     * Object initialization.
     *
     * @param   Context $context
     * @param   CustomerSession $customerSession
     * @param   PlanFactory $planFactory
     * @param   PriceHelper $priceHelper
     * @param   CurrencyFactory $currencyFactory
     * @param   StoreManagerInterface $storeManager
     * @param   SubscriptionHelper $subscriptionHelper
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        PlanFactory $planFactory,
        PriceHelper $priceHelper,
        CurrencyFactory $currencyFactory,
        StoreManagerInterface $storeManager,
        SubscriptionHelper $subscriptionHelper
    ) {
        $this->customerSession = $customerSession;
        $this->planFactory = $planFactory;
        $this->priceHelper = $priceHelper;
        $this->storeManager = $storeManager;
        $this->subscriptionHelper = $subscriptionHelper;
        $currentCurrencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $this->currency = $currencyFactory->create()->load($currentCurrencyCode);

        parent::__construct($context);
    }

    /**
     * Get subscription plan related to current supplier.
     *
     * @return SubscriptionPlan
     */
    public function getCurrentPlan()
    {
        $customer = $this->getCustomer();
        $this->subscriptionHelper->checkCustomerDefaultPlan($customer);
        $currentPlanId = $customer->getCurrentPlanId();
        $currentPlan = $this->planFactory->create()->load($currentPlanId);

        return $currentPlan;
    }

    /**
     * Get price with currency format.
     *
     * @param string|float $value
     *
     * @return string
     */
    public function getPriceFormat($value)
    {
        return $this->priceHelper->currency($value, true, false);
    }

    /**
     * Get formated date.
     *
     * @param string $value
     *
     * @return string
     */
    public function getDateFormat($value)
    {
        return date('m-d-Y', strtotime($value));
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
     * Get currency symbol for current locale and currency code
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->currency->getCurrencySymbol();
    }

    /**
     * Retrieve form action url.
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl(
            'supplier_subscription/plan/purchase',
            ['_secure' => true]
        );
    }

    /**
     * Validate if plan can be purchased by vendor.
     *
     * @param SubscriptionPlan $plan
     *
     * @return bool|string
     */
    public function checkIsSaleable(SubscriptionPlan $plan)
    {
        $error = false;
        if (!$plan || !$plan->getId()) {
            $error = __('Plan does not exists, please find upgrade section.');
        } elseif (!$plan->validateVirtualProduct()) {
            $error = __('Plan is not valid to renew, please contact with admin.');
        }

        return $error;
    }
}
