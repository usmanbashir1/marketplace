<?php

namespace Cminds\SupplierInventoryUpdate\Model\Update;

use Cminds\SupplierInventoryUpdate\Helper\Data as UpdaterHelper;
use Cminds\SupplierInventoryUpdate\Model\Config\Source\Action;
use Cminds\SupplierInventoryUpdate\Model\InventoryUpdateFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Model\Context;
use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;
use Magento\Framework\Module\Dir\Reader;

class Stock extends Csv implements UpdateInterface
{
    protected $inventoryUpdateFactory;
    protected $logger;
    protected $productCollectionFactory;
    protected $stockRegistry;

    public function __construct(
        InventoryUpdateFactory $inventoryUpdateFactory,
        UpdaterHelper $updaterHelper,
        LoggerInterface $logger,
        Context $context,
        ProductFactory $productCollectionFactory,
        StockRegistryInterface $stockRegistry,
        Registry $registry,
        Reader $moduleReader
    ) {
        $this->inventoryUpdateFactory = $inventoryUpdateFactory;
        $this->logger = $logger;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->stockRegistry = $stockRegistry;

        parent::__construct(
            $context,
            $registry,
            $updaterHelper,
            $moduleReader
        );
    }

    public function prepare()
    {
        $this->feedUrl = $this->getVendor()->getUpdaterCsvLink();
        $this->matchinCsvColumn = $this->getVendor()->getUpdaterCsvColumn();
        $this->matchingProductAttribute = $this->getVendor()->getUpdaterCsvAttribute();
        $this->matchingColumnIndex = $this->getVendor()->getUpdaterQtyColumn();
        $this->delimiter = $this->getVendor()->getUpdaterCsvDelimeter();
        $this->csvAction = $this->getVendor()->getUpdaterCsvAction();

        $this->parse();
    }

    public function run()
    {
        try {
            if (!$this->columnPos) {
                return false;
            }

            $processedProductIds = [];
            foreach ($this->getParsedData() as $productData) {
                if (!isset($productData[$this->matchingPos]) || !$productData[$this->matchingPos]) {
                    throw new \Exception($this->matchinCsvColumn . ' is empty or does not exist.');
                }

                $vendorLoadValue = trim($productData[$this->matchingPos]);
                $vendorLoadValue = str_replace("'", "", $vendorLoadValue);
                $vendorLoadValue = str_replace('"', "", $vendorLoadValue);

                $collection = $this->productCollectionFactory->create()->getCollection();
                $collection
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter(
                        $this->matchingProductAttribute,
                        $vendorLoadValue
                    );
                $product = $collection->getFirstItem();

                if (!$product->getId()) {
                    $this->logger->warning(
                        __(
                            'Product with matching value "%1" does not exists.',
                            $vendorLoadValue
                        )
                    );
                    continue;
                }

                if ($product->getCreatorId() != $this->getVendor()->getSupplierId()) {
                    $this->logger->warning(
                        __(
                            'Product with matching value "%1" does not belong to vendor %2.',
                            $vendorLoadValue,
                            $this->getVendor()->getName()
                        )
                    );
                    continue;
                }

                if ($this->columnPos) {
                    if ($product->getData() != 0) {
                        $sku = $product->getSku();
                        $stockItem = $this->stockRegistry->getStockItemBySku($sku);

                        $qty = $productData[$this->columnPos];

                        if (is_numeric($qty)) {
                            $stockItem->setQty($qty);
                            $stockItem->setIsInStock(1);
                            $this->stockRegistry->updateStockItemBySku(
                                $sku,
                                $stockItem
                            );

                            $processedProductIds[] = $product->getId();
                        }
                    }
                }
            }

            if ((int)$this->csvAction === Action::SET_OUT_OF_STOCK) {
                $collection = $this->productCollectionFactory->create()->getCollection();
                $collection
                    ->addAttributeToSelect(['sku', 'creator_id'])
                    ->addAttributeToFilter(
                        'creator_id',
                        $this->getVendor()->getSupplierId()
                    )
                    ->addFieldToFilter('entity_id', ['nin' => $processedProductIds]);

                foreach ($collection as $product) {
                    $sku = $product->getSku();

                    $stockItem = $this->stockRegistry->getStockItemBySku($sku);
                    $stockItem->setIsInStock(0);
                    $this->stockRegistry->updateStockItemBySku(
                        $sku,
                        $stockItem
                    );
                }
            }
        } catch (\Exception $e) {
            $message = '';
            if (isset($productData[$this->matchingPos])) {
                $message = 'Cannot update product with matching value ' . $productData[$this->matchingPos];
            }
            $message .= ' Reason : ' . $e->getMessage();
            $this->logger->critical($message);
        }

        return $this;
    }
}
