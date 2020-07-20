<?php

namespace Cminds\MultipleProductVendors\Model\Indexer\Product\Manufacturer\Action;

use Cminds\MultipleProductVendors\Model\Product\Synchronizer;
use Cminds\MultipleProductVendors\Model\ResourceModel\Manufacturer\CollectionFactory as ManufacturerCollectionFactory;
use Magento\Framework\Registry;

class Full implements FullActionInterface
{
    /**
     * Synchronizer handler.
     * Class responsible for synchronizing data between
     * main product and cheapest manufacturer's product.
     *
     * @var Synchronizer
     */
    private $synchronizer;

    /**
     * Collection of manufacturer codes.
     *
     * @var ManufacturerCollectionFactory
     */
    private $manufacturerCollectionFactory;

    /**
     * Core registry.
     *
     * @var Registry
     */
    private $registry;

    /**
     * Full constructor.
     *
     * @param Synchronizer                  $synchronizer
     * @param ManufacturerCollectionFactory $manufacturerCollectionFactory
     * @param Registry                      $registry
     */
    public function __construct(
        Synchronizer $synchronizer,
        ManufacturerCollectionFactory $manufacturerCollectionFactory,
        Registry $registry
    ) {
        $this->synchronizer = $synchronizer;
        $this->manufacturerCollectionFactory = $manufacturerCollectionFactory;
        $this->registry = $registry;
    }

    /**
     * Execute full reindexing
     *
     * @param int[]|null $ids
     * @param            void
     *
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function execute($ids = null)
    {
        $this->registry->register('reindex_manufacturer', 1);

        $manufacturers = $this->manufacturerCollectionFactory->create()
            ->load();

        foreach ($manufacturers as $manufacturer) {
            $this->synchronizer
                ->setManufacturerCode($manufacturer->getManufacturerCode())
                ->findCheapestProduct();
        }

        $this->registry->unregister('reindex_manufacturer');
    }
}
