<?php

namespace Cminds\MultipleProductVendors\Observer\Product\SaveBefore;

use Cminds\MultipleProductVendors\Model\Config as ModuleConfig;
use Cminds\MultipleProductVendors\Model\Product\Synchronizer;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;

class MainProduct implements ObserverInterface
{
    const IS_MAIN_PRODUCT = 1;
    const NOT_VISIBLE_INDUVIDUALLY = 1;

    /**
     * Product collection factory.
     *
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * Message manager.
     *
     * @var ManagerInterface
     */
    private $messageManager;

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
     * MainProduct constructor.
     *
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ManagerInterface         $messageManager
     * @param ModuleConfig             $moduleConfig
     * @param Registry                 $coreRegistry
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        ManagerInterface $messageManager,
        ModuleConfig $moduleConfig,
        Registry $coreRegistry
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->messageManager = $messageManager;
        $this->moduleConfig = $moduleConfig;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Execute observer.
     *
     * @param Observer $observer
     *
     * @throws LocalizedException
     * @return MainProduct
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isEnabled() === false) {
            return $this;
        }
        if ($this->coreRegistry->registry(Synchronizer::PROCESSING_VENDOR_CANDIDATES)) {
            return $this;
        }

        $currentProduct = $observer->getProduct();

        $manufacturerCode = $currentProduct->getManufacturerCode();
        if (!$manufacturerCode) {
            $currentProduct->setMainProduct(0);

            return $this;
        }

        $isMain = $currentProduct->getMainProduct();
        if (!$isMain) {
            return $this;
        }

        $existingMainProduct = $this->productCollectionFactory->create()
            ->addFieldToFilter('manufacturer_code', $manufacturerCode)
            ->addFieldToFilter('main_product', static::IS_MAIN_PRODUCT)
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();

        // If not found any that is good, leave.
        if ($existingMainProduct->getId() === null) {
            return $this;
        }

        // If it is the same product that is fine.
        if ((int)$existingMainProduct->getId() === (int)$currentProduct->getId()) {
            return $this;
        }

        // In other case we need to prevent another product to be set as main.
        $currentProduct->setMainProduct(0);

        $this->messageManager->addErrorMessage(
            __('There is already main currentProduct defined for this manufacturer code.')
        );

        return $this;
    }
}
