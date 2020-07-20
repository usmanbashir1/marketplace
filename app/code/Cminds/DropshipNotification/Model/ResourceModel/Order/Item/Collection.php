<?php

namespace Cminds\DropshipNotification\Model\ResourceModel\Order\Item;

use Cminds\DropshipNotification\Model\Config;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\CustomerFactory;

/**
 * Cminds DropshipNotification order items custom collection resource model.
 *
 * @category Cminds
 * @package  Cminds_DropshipNotification
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Collection extends OrderItemCollection
{
    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var null|int
     */
    private $statusFilter;

    /**
     * Collection constructor.
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface        $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface       $eventManager
     * @param Snapshot               $entitySnapshot
     * @param ProductFactory         $productFactory
     * @param CustomerFactory        $customerFactory
     * @param AdapterInterface|null  $connection
     * @param AbstractDb|null        $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Snapshot $entitySnapshot,
        ProductFactory $productFactory,
        CustomerFactory $customerFactory,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->productFactory = $productFactory;
        $this->customerFactory = $customerFactory;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $entitySnapshot,
            $connection,
            $resource
        );
    }

    /**
     * Filter collection by order_id.
     *
     * @param int $orderId
     *
     * @return Collection
     */
    public function filterByOrderId($orderId = null)
    {
        $this->addFieldToFilter('order_id', $orderId);

        return $this;
    }

    /**
     * Filter collection by status.
     *
     * @param int $status
     *
     * @return Collection
     */
    public function filterByStatus($status)
    {
        $this->statusFilter = $status ? 1 : 0;

        return $this;
    }

    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        if ($this->isLoaded() === false) {
            $this->load();
        }

        return parent::getSize();
    }

    /**
     * Load additional data and filter collection.
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        $this->getSize();

        foreach ($this as $key => $item) {
            $product = $this->productFactory->create()
                ->load($item->getProductId());

            $supplierId = $product->getCreatorId();
            if (empty($supplierId)) {
                $this->removeItemByKey($key);
                $this->_totalRecords -= 1;
                continue;
            }

            $status = (int)$item->getDropshipNotificationFlag();
            if (($this->statusFilter === Config::STATUS_COMPLETED && $status === Config::STATUS_INCOMPLETE)
                || ($this->statusFilter === Config::STATUS_INCOMPLETE && $status === Config::STATUS_COMPLETED)
            ) {
                $this->removeItemByKey($key);
                $this->_totalRecords -= 1;
                continue;
            }

            if ($status === Config::STATUS_INCOMPLETE) {
                $item
                    ->setNotificationMethod('-')
                    ->setDropshipNotificationDate('-');
            } else {
                $item->setNotificationMethod(__('Email'));
            }

            $customer = $this->customerFactory->create()
                ->load($supplierId);
            $customerName = sprintf(
                '%s %s',
                $customer->getFirstname(),
                $customer->getLastname()
            );
            $item
                ->setSupplierName($customerName)
                ->setSupplierEmail($customer->getEmail())
                ->setSupplierId($supplierId);
        }

        return $this;
    }
}
