<?php

namespace Cminds\MarketplacePaypal\Observer\VendorPanel\Navbar;

use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Cminds MarketplacePaypal vendor portal navigation initialization.
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Piotr Pierzak <piotrek.pierzak@gmail.com>
 */
class Init implements ObserverInterface
{
    /**
     * Navigation items.
     *
     * @var DataObject
     */
    private $navItems;

    /**
     * DataObject factory.
     *
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * Init constructor.
     *
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
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
        $navItems = $observer->getNavigationItems();
        $navItemsData = $navItems->getData();

        $items = array_merge(
            $this->getNavigationItems($navItemsData)->getData(),
            $navItemsData
        );
        $navItems->setData($items);

        return $this;
    }

    /**
     * Get vendor panel navigation items from marketplace module.
     *
     * @return DataObject
     */
    private function getNavigationItems()
    {
        if ($this->navItems === null) {
            $navItems = [
                'supplier_paypal' => [
                    'label' => 'Paypal',
                    'url' => 'marketplacepaypal/settings/paypal',
                    'parent' => 'settings',
                    'sort' => 4,
                ],
            ];

            $this->navItems = $this->dataObjectFactory->create()
                ->setData($navItems);
        }

        return $this->navItems;
    }
}
