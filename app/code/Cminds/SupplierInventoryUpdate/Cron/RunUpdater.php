<?php

namespace Cminds\SupplierInventoryUpdate\Cron;

use Braintree\Exception;
use Cminds\SupplierInventoryUpdate\Model\InventoryUpdateFactory;
use Cminds\SupplierInventoryUpdate\Model\Update\Cost;
use Cminds\SupplierInventoryUpdate\Model\Update\Stock;
use Magento\Framework\DataObject;
use Psr\Log\LoggerInterface;

/**
 * Cminds SupplierInventoryUpdate Cron Class view.
 *
 * @category Cminds
 * @package  Cminds_SupplierInventoryUpdate
 * @author   Mateusz Niziolek
 */
class RunUpdater extends DataObject
{
    private $updateCost;
    private $updateStock;
    private $logger;
    private $inventoryUpdateFactory;

    private $vendors = [];

    public function __construct(
        Stock $updateStock,
        Cost $updateCost,
        InventoryUpdateFactory $inventoryUpdateFactory,
        LoggerInterface $logger
    ) {
        $this->inventoryUpdateFactory = $inventoryUpdateFactory;
        $this->logger = $logger;
        $this->updateStock = $updateStock;
        $this->updateCost = $updateCost;
    }

    public function execute()
    {
        $this->run();

        return $this;
    }

    private function run()
    {
        try {
            $updater = [
                $this->updateCost,
                $this->updateStock,
            ];

            if (!$updater) {
                throw new Exception(__('Updater does not exists.'));
            }

            foreach ($updater as $updaterItem) {
                $updater = $updaterItem;
                $vendors = $this->getVendors();
                foreach ($vendors as $vendor) {
                    $updater->setVendor($vendor);
                    $updater->prepare();
                    $updater->run();
                    $updater->notify();
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    private function getVendors()
    {
        if (!$this->vendors) {
            $modelFactory = $this->inventoryUpdateFactory->create();
            $collection = $modelFactory->getCollection();
            $this->vendors = $collection;
        }

        return $this->vendors;
    }
}
