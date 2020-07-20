<?php

namespace Cminds\MultipleProductVendors\Observer\Product\SaveAfter;

use Cminds\MultipleProductVendors\Model\Product\Synchronizer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Indexer\Model\IndexerFactory;
use Cminds\MultipleProductVendors\Model\Config as ModuleConfig;

class RunIndexer implements ObserverInterface
{
    const MANUFACTURER_INDEXER_NAME = 'manufacturer_product';

    /**
     * Core registry.
     *
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Indexer factory.
     *
     * @var IndexerFactory
     */
    private $indexerFactory;

    /**
     * Module config.
     *
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * RunIndexer constructor.
     *
     * @param Registry       $coreRegistry
     * @param IndexerFactory $indexerFactory
     * @param ModuleConfig   $moduleConfig
     */
    public function __construct(
        Registry $coreRegistry,
        IndexerFactory $indexerFactory,
        ModuleConfig $moduleConfig
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->indexerFactory = $indexerFactory;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Execute observer.
     *
     * @param Observer $observer
     *
     * @return RunIndexer
     * @throws \InvalidArgumentException
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isEnabled() === false) {
            return $this;
        }
        if ($this->coreRegistry->registry(Synchronizer::PROCESSING_VENDOR_CANDIDATES)) {
            return $this;
        }

        $product = $observer->getEvent()->getProduct();

        $manufacturerCode = $product->getManufacturerCode();
        if (!$manufacturerCode) {
            return $this;
        }

        if (!$this->coreRegistry->registry('reindex_manufacturer')) {
            $indexer = $this->indexerFactory->create()
                ->load(static::MANUFACTURER_INDEXER_NAME);

            if (!$indexer->isScheduled()) {
                $id = $product->getEntityId();
                $indexer->reindexRow($id);
            }
        }

        return $this;
    }
}
