<?php

namespace Cminds\MultipleProductVendors\Model\Indexer\Product\Manufacturer\Action;

use Cminds\MultipleProductVendors\Model\Product\Synchronizer;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;

class Row implements RowActionInterface
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
     * Product Factory.
     *
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * Core registry.
     *
     * @var Registry
     */
    private $registry;

    /**
     * Row constructor.
     *
     * @param Synchronizer   $synchronizer
     * @param ProductFactory $productFactory
     * @param Registry       $registry
     */
    public function __construct(
        Synchronizer $synchronizer,
        ProductFactory $productFactory,
        Registry $registry
    ) {
        $this->synchronizer = $synchronizer;
        $this->productFactory = $productFactory;
        $this->registry = $registry;
    }

    /**
     * Execute row indexing.
     *
     * @param void
     *
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function execute($id = null)
    {
        if (!$id) {
            return;
        }

        $this->registry->register('reindex_manufacturer', 1);

        $product = $this->productFactory->create()
            ->load($id);

        $manufacturerCode = $product->getManufacturerCode();

        if (!$manufacturerCode) {
            $this->registry->unregister('reindex_manufacturer');

            return;
        }

        $this->synchronizer
            ->setManufacturerCode($manufacturerCode)
            ->findCheapestProduct();

        $this->registry->unregister('reindex_manufacturer');
    }
}
