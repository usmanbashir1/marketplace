<?php

namespace Cminds\MultipleProductVendors\Observer\Product\DeleteAfter;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Cminds\MultipleProductVendors\Model\ManufacturerFactory;
use Cminds\MultipleProductVendors\Model\Config as ModuleConfig;

class CheckManufacturer implements ObserverInterface
{
    /**
     * Product factory.
     *
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * Product collection factory.
     *
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * Manufacturer code entity.
     *
     * @var ManufacturerFactory
     */
    private $manufacturerFactory;

    /**
     * Module config.
     *
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * CheckManufacturer constructor.
     *
     * @param ProductFactory $productFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ManufacturerFactory $manufacturerFactory
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        ProductFactory $productFactory,
        ProductCollectionFactory $productCollectionFactory,
        ManufacturerFactory $manufacturerFactory,
        ModuleConfig $moduleConfig
    ) {
        $this->productFactory = $productFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->manufacturerFactory = $manufacturerFactory;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Execute observer.
     *
     * @param Observer $observer
     *
     * @return CheckManufacturer
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isEnabled() === false) {
            return $this;
        }

        $productId = $observer
            ->getEvent()
            ->getProduct()
            ->getId();

        $product = $this->productFactory->create()
            ->setStoreId(Store::DEFAULT_STORE_ID)
            ->load($productId);

        $manufacturerCode = $product->getManufacturerCode();

        if (!$manufacturerCode) {
            return $this;
        }

        $manufacturerProducts = $this->productCollectionFactory->create()
            ->addFieldToFilter('manufacturer_code', $manufacturerCode)
            ->load();

        if (count($manufacturerProducts) !== 0
            && count($manufacturerProducts) !== 1
        ) {
            return $this;
        }

        $codeEntity = $this->manufacturerFactory->create()
            ->load($manufacturerCode, 'manufacturer_code')
            ->delete();

        return $this;
    }
}
