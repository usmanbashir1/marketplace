<?php

namespace Cminds\MarketplaceQa\Observer\Questions\Navbar;

use Cminds\MarketplaceQa\Helper\Data as CmindsHelper;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Init implements ObserverInterface
{
    /**
     * Navigation items.
     *
     * @var DataObject
     */
    private $navItems;

    /**
     * Marketplace helper object.
     *
     * @var CmindsHelper
     */
    private $cmindsHelper;

    /**
     * DataObject factory.
     *
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    public function __construct(
        CmindsHelper $cmindsHelper,
        DataObjectFactory $dataObjectFactory,
        DataObjectHelper $dataObjectHelper,
        DataObject $dataObject
    ) {
        $this->cmindsHelper = $cmindsHelper;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    public function execute(Observer $observer)
    {
        $navItems = $observer->getNavigationItems();
        $navItemsData = $navItems->getData();
        $items = array_merge(
            $this->getNavigationItems()->getData(),
            $navItemsData
        );
        $navItems->setData($items);

        return $this;
    }

    private function marketplaceQaEnabled()
    {
        return $this->cmindsHelper->marketplaceQaEnabled();
    }

    private function getNavigationItems()
    {
        if ($this->navItems === null) {
            $navItems = [];
            if ($this->marketplaceQaEnabled()) {
                $navItems['qa_page'] = [
                    'label' => 'Q & A',
                    'url' => 'marketplaceqa/questions/index',
                    'parent' => null,
                    'sort' => 6,
                ];
            }

            $this->navItems = $this->dataObjectFactory->create()
                ->setData($navItems);
        }

        return $this->navItems;
    }
}
