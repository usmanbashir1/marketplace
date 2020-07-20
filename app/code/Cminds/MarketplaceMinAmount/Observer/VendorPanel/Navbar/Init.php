<?php

declare(strict_types=1);

namespace Cminds\MarketplaceMinAmount\Observer\VendorPanel\Navbar;

use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Cminds\MarketplaceMinAmount\Helper\Data;

/**
 * Cminds MarketplaceMinAmount vendor portal navigation initialization.
 *
 * @category Cminds
 * @package  MarketplaceMinAmount
 * @author   Vadym Moiseiuk <developer.maxvision@gmail.com>
 */
class Init implements ObserverInterface
{
    /**
     * @var DataObject
     */
    private $navItems;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * Init constructor.
     *
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        Data $dataHelper
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->dataHelper = $dataHelper;
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
        if (!$this->dataHelper->isModuleEnabled()) {
            return $this;
        }

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
                'supplier_minamount' => [
                    'label' => 'Minimum Order Amount',
                    'url' => 'marketplaceminamount/settings/minamount',
                    'parent' => 'settings',
                    'sort' => 3,
                ],
            ];

            $this->navItems = $this->dataObjectFactory->create()
                ->setData($navItems);
        }

        return $this->navItems;
    }
}
