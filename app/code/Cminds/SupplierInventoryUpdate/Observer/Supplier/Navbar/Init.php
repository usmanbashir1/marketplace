<?php

namespace Cminds\SupplierInventoryUpdate\Observer\Supplier\Navbar;

use Cminds\SupplierInventoryUpdate\Helper\Data as DataHelper;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Module\Manager;

class Init implements ObserverInterface
{
    private $dataHelper;
    private $navItems;
    private $dataObjectFactory;
    private $moduleManager;

    public function __construct(
        DataObjectFactory $dataObjectFactory,
        DataHelper $dataHelper,
        Manager $moduleManager
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->dataHelper = $dataHelper;
        $this->moduleManager = $moduleManager;
    }

    public function execute(Observer $observer)
    {
        if ($this->moduleManager->isOutputEnabled('Cminds_SupplierInventoryUpdate')
            && $this->dataHelper->isEnabled()
        ) {
            $navItems = $observer->getNavigationItems();
            $navItemsData = $navItems->getData();
            $items = array_merge(
                $this->getNavigationItems()->getData(),
                $navItemsData
            );
            $navItems->setData($items);
        }

        return $this;
    }

    private function getNavigationItems()
    {
        if ($this->navItems === null) {
            $navItems = [
                'inventory_updater' => [
                    'label' => 'Inventory Updater',
                    'url' => 'supplier_inventory/inventory/update',
                    'parent' => 'import',
                    'action_names' => [
                        'supplier_subscriptions_plan_upgrade',
                    ],
                    'sort' => 3,
                ],
            ];
            $this->navItems = $this->dataObjectFactory->create()
                ->setData($navItems);
        }

        return $this->navItems;
    }
}
