<?php

namespace Cminds\MultipleProductVendors\Model\Indexer\Product;

use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\Indexer\ActionInterface;
use Cminds\MultipleProductVendors\Model\Indexer\Product\Manufacturer\Action\FullActionInterface;
use Cminds\MultipleProductVendors\Model\Indexer\Product\Manufacturer\Action\RowActionInterface;
use Cminds\MultipleProductVendors\Model\Config as ModuleConfig;

class Manufacturer implements ActionInterface, MviewActionInterface
{
    /**
     * Full indexer for manufacturers.
     *
     * @var FullActionInterface
     */
    private $productManufacturerFull;

    /**
     * Row indexer for manufacturers.
     *
     * @var RowActionInterface
     */
    private $productManufacturerRow;

    /**
     * Module config.
     *
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * Manufacturer constructor.
     *
     * @param FullActionInterface $productManufacturerFull
     * @param RowActionInterface $productManufacturerRow
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        FullActionInterface $productManufacturerFull,
        RowActionInterface $productManufacturerRow,
        ModuleConfig $moduleConfig
    ) {
        $this->productManufacturerFull = $productManufacturerFull;
        $this->productManufacturerRow = $productManufacturerRow;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Execute full indexer.
     */
    public function executeFull()
    {
        if ($this->moduleConfig->isEnabled() === false) {
            return;
        }

        $this->productManufacturerFull->execute();
    }

    /**
     * Reindex only some products.
     *
     * @param array $ids
     */
    public function executeList(array $ids)
    {
        if ($this->moduleConfig->isEnabled() === false) {
            return;
        }

        $this->executeFull();
    }

    /**
     * Execute one type of products.
     *
     * @param int $id
     */
    public function executeRow($id)
    {
        if ($this->moduleConfig->isEnabled() === false) {
            return;
        }

        $this->productManufacturerRow->execute($id);
    }

    /**
     * Execute indexers in cron.
     *
     * @param int[] $ids
     */
    public function execute($ids)
    {
        if ($this->moduleConfig->isEnabled() === false) {
            return;
        }

        $this->executeFull();
    }
}
