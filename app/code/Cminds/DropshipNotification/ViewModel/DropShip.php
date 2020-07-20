<?php

namespace Cminds\DropshipNotification\ViewModel;

use Cminds\DropshipNotification\Helper\Data;
use Magento\Sales\Model\Order;
use Cminds\DropshipNotification\Model\ResourceModel\Order\Item\CollectionFactory;
use Cminds\DropshipNotification\Model\Config as ModuleConfig;

class DropShip
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /** @var Data */
    private $helper;

    public function __construct(
        CollectionFactory $collectionFactory,
        ModuleConfig $moduleConfig,
        Data $helper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->moduleConfig = $moduleConfig;
        $this->helper = $helper;
    }

    /**
     * Get grids meta info in header => status filter format
     *
     * @return array
     */
    public function getGrids()
    {
        return [
            'Pending Order Items' => ModuleConfig::STATUS_INCOMPLETE,
            'Dropshipped Order Items' => ModuleConfig::STATUS_COMPLETED
        ];
    }

    /**
     * Get tab collection by status filter
     *
     * @param Order $order
     * @param int $status
     * @return \Cminds\DropshipNotification\Model\ResourceModel\Order\Item\Collection
     */
    public function getItemsCollectionByFilter($order, $status)
    {
        $collection = $this->collectionFactory->create();
        $collection
            ->filterByOrderId($order->getId())
            ->filterByStatus($status);

        return $collection->load();
    }

    /**
     * Get can show tab
     *
     * @param int $orderId
     * @return bool
     */
    public function canShowTab($orderId)
    {
        if ($this->moduleConfig->isEnabled() === false) {
            return false;
        }

        return $this->helper->isDropshipButtonAvailable($orderId);
    }

}
