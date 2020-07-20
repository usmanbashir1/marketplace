<?php

namespace Cminds\MultipleProductVendors\Observer\Product\SaveBefore;

use Cminds\MultipleProductVendors\Model\Config as ModuleConfig;
use Cminds\MultipleProductVendors\Model\ManufacturerFactory;
use Cminds\MultipleProductVendors\Model\Product\Synchronizer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class CheckManufacturer implements ObserverInterface
{
    const NOT_VISIBLE_INDUVIDUALLY = 1;

    /**
     * Manufacturer code entity factory.
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
     * @var Registry
     */
    private $coreRegistry;

    /**
     * CheckManufacturer constructor.
     *
     * @param ManufacturerFactory $manufacturerFactory
     * @param ModuleConfig        $moduleConfig
     * @param Registry            $coreRegistry
     */
    public function __construct(
        ManufacturerFactory $manufacturerFactory,
        ModuleConfig $moduleConfig,
        Registry $coreRegistry
    ) {
        $this->manufacturerFactory = $manufacturerFactory;
        $this->moduleConfig = $moduleConfig;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Execute observer.
     *
     * @param Observer $observer
     *
     * @return CheckManufacturer
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isEnabled() === false) {
            return $this;
        }
        if ($this->coreRegistry->registry(Synchronizer::PROCESSING_VENDOR_CANDIDATES)) {
            return $this;
        }

        $product = $observer->getProduct();
        $manufacturerCode = $product->getManufacturerCode();
        if (!$manufacturerCode) {
            return $this;
        }

        $this->manufacturerFactory->create()
            ->saveNewCode($manufacturerCode);

        return $this;
    }
}
