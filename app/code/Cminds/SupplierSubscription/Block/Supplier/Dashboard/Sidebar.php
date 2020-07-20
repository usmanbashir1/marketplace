<?php

namespace Cminds\SupplierSubscription\Block\Supplier\Dashboard;

use Cminds\SupplierSubscription\Block\Supplier\Plan;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template\Context;
use Cminds\SupplierSubscription\Model\PlanFactory;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Directory\Model\CurrencyFactory;
use Cminds\SupplierSubscription\Helper\Product as ProductHelper;
use Cminds\SupplierSubscription\Helper\Data as SubscriptionHelper;
use Magento\Store\Model\StoreManagerInterface;

class Sidebar extends Plan
{
    /**
     * @var ProductHelper
     */
    protected $productHelper;

    /**
     * Sidebar constructor.
     *
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param PlanFactory $planFactory
     * @param PriceHelper $priceHelper
     * @param CurrencyFactory $currency
     * @param ProductHelper $productHelper
     * @param SubscriptionHelper $subscriptionHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        PlanFactory $planFactory,
        PriceHelper $priceHelper,
        CurrencyFactory $currency,
        ProductHelper $productHelper,
        SubscriptionHelper $subscriptionHelper,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $planFactory,
            $priceHelper,
            $currency,
            $storeManager,
            $subscriptionHelper
        );

        $this->productHelper = $productHelper;
    }

    /**
     * Is user has active plan.
     *
     * @return bool
     */
    public function isPlanActive()
    {
        $supplierPlan = $this->getCurrentPlan();

        if (!$supplierPlan || !$supplierPlan->getId()) {
            return false;
        }

        $planToDate = strtotime($this->getCustomer()->getPlanToDate());
        if ($planToDate === false || time() > $planToDate) {
            return false;
        }

        return true;
    }

    /**
     * Count products belongs to current vendor.
     *
     * @return int
     */
    public function countVendorProducts()
    {
        return $this->productHelper->countVendorProducts($this->getCustomer());
    }

    /**
     * Is module enabled in configuration.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->subscriptionHelper->isEnabled();
    }
}
