<?php

namespace Cminds\MarketplaceRma\Observer\Supplier\Navbar;

use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Init
 *
 * @package Cminds\MarketplaceRma\Observer\Supplier\Navbar
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
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * Init constructor.
     *
     * @param DataObjectFactory $dataObjectFactory
     * @param ModuleConfig      $moduleConfig
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        ModuleConfig $moduleConfig
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Execute method.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $navItems = $observer->getNavigationItems();
        $navItemsData = $navItems->getData();
        $items = array_merge(
            $this->getNavigationItems()->getData(),
            $navItemsData
        );
        $navItems->setData($items);
    }

    /**
     * Check is module enabled.
     *
     * @return bool
     */
    private function marketplaceRmaEnabled()
    {
        if ($this->moduleConfig->isActive() === false) {
            return false;
        }

        return true;
    }

    /**
     * Inject Returns nav item.
     *
     * @return $this|DataObject
     */
    private function getNavigationItems()
    {
        if ($this->navItems === null) {
            $navItems = [];
            if ($this->marketplaceRmaEnabled()) {
                $navItems['rma_page'] = [
                    'label' => 'Returns',
                    'url' => 'marketplacerma/supplierrma/index',
                    'parent' => null,
                    'sort' => 7,
                ];
            }

            $this->navItems = $this->dataObjectFactory->create()
                ->setData($navItems);
        }

        return $this->navItems;
    }
}
