<?php

namespace Cminds\SupplierSubscription\Observer\Supplier\Navbar;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Cminds\SupplierSubscription\Helper\Data as SubscriptionHelper;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\DataObject;

class Init implements ObserverInterface
{
    /**
     * Navigation items.
     *
     * @var DataObject
     */
    public $navItems;

    /**
     * Cminds_Subscription helper data.
     *
     * @var SubscriptionHelper
     */
    protected $subscriptionHelper;

    /**
     * DataObject factory.
     *
     * @var DataObjectFactory
     */
    public $dataObjectFactory;

    /**
     * Helper object.
     *
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * DataObject object.
     *
     * @var DataObject
     */
    public $dataObject;

    /**
     * Init constructor.
     *
     * @param SubscriptionHelper $subscriptionHelper
     * @param DataObjectFactory $dataObjectFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObject $dataObject
     */
    public function __construct(
        SubscriptionHelper $subscriptionHelper,
        DataObjectFactory $dataObjectFactory,
        DataObjectHelper $dataObjectHelper,
        DataObject $dataObject
    ) {
        $this->subscriptionHelper = $subscriptionHelper;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObject = $dataObject;
    }

    /**
     * Set navigation items used in Cminds\Supplierfrontendproductuploader\Block\Navbar.
     *
     * @param Observer $observer
     *
     * @return Init
     */
    public function execute(Observer $observer)
    {
        // check if module is not disabled in configuration
        if ($this->subscriptionHelper->isEnabled() === false) {
            return;
        }

        $navItems = $observer->getNavigationItems();
        $navItemsData = $navItems->getData();
        $items = array_merge($this->getNavigationItems()->getData(), $navItemsData);
        $navItems->setData($items);

        return $this;
    }

    /**
     * Get vendor panel navigation items for subscription management.
     *
     * @return DataObject
     */
    protected function getNavigationItems()
    {
        if ($this->navItems === null) {
            $navItems = [
                'subscription_renew' => [
                    'label' => 'Renew Plan',
                    'url' => 'supplier_subscription/plan/renew',
                    'parent' => null,
                    'action_names' => [
                        'supplier_subscription_plan_renew',
                    ],
                    'sort' => 3.1
                ],
                'subscription_upgrade' => [
                    'label' => 'Upgrade Plan',
                    'url' => 'supplier_subscription/plan/upgrade',
                    'parent' => null,
                    'action_names' => [
                        'supplier_subscription_plan_upgrade'
                    ],
                    'sort' => 3.2
                ]
            ];

            $this->navItems = $this->dataObjectFactory->create()->setData($navItems);
        }

        return $this->navItems;
    }
}
