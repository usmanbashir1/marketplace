<?php

namespace Cminds\SupplierInventoryUpdate\Model\Update;

use Cminds\SupplierInventoryUpdate\Helper\Data as UpdaterHelper;
use Cminds\SupplierInventoryUpdate\Helper\Email as HelperEmail;
use Cminds\SupplierInventoryUpdate\Model\InventoryUpdateFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Cost extends Csv implements UpdateInterface
{
    protected $inventoryUpdateFactory;
    protected $logger;
    protected $productCollectionFactory;
    protected $stockRegistry;
    protected $scopeConfig;
    protected $storeManager;
    protected $helperEmail;
    protected $changedProducts = [];

    public function __construct(
        Reader $moduleReader,
        Context $context,
        InventoryUpdateFactory $inventoryUpdateFactory,
        UpdaterHelper $updaterHelper,
        HelperEmail $helperEmail,
        LoggerInterface $logger,
        ProductFactory $productCollectionFactory,
        StockRegistryInterface $stockRegistry,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Registry $registry
    ) {
        $this->inventoryUpdateFactory = $inventoryUpdateFactory;
        $this->logger = $logger;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->stockRegistry = $stockRegistry;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->helperEmail = $helperEmail;

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
        $this->matchingColumnIndex = $this->getVendor()->getUpdaterCostColumn();
        $this->delimiter = $this->getVendor()->getUpdaterCsvDelimeter();

        $this->parse();
    }

    public function run()
    {
        try {
            if (!$this->columnPos) {
                return false;
            }

            $this->changedProducts = [];

            foreach ($this->getParsedData() as $productData) {
                if (!isset($productData[$this->matchingPos]) || !$productData[$this->matchingPos]) {
                    throw new \Exception($this->matchinCsvColumn . ' is empty or does not exist.');
                }

                $vendorLoadValue = trim($productData[$this->matchingPos]);
                $vendorLoadValue = str_replace('"', "", $vendorLoadValue);
                $vendorLoadValue = str_replace("'", "", $vendorLoadValue);

                $collection = $this->productCollectionFactory->create()->getCollection();
                $collection
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter(
                        $this->matchingProductAttribute,
                        $vendorLoadValue
                    );
                $product = $collection->getFirstItem();

                if (!$product->getId()) {
                    throw new \Exception('Product is not available in catalog.');
                }

                if ($product->getCreatorId() != $this->getVendor()->getSupplierId()) {
                    throw new \Exception('Product doesn\'t belong to Vendor: ' . $this->getVendor()->getName());
                }

                if ($this->columnPos) {
                    if ($product->getData() != 0) {
                        $cost = $productData[$this->columnPos];
                        if ($cost != $product->getCost()) {
                            $this->changedProducts[$this->getVendor()->getSupplierId()][] = [
                                'name' => $product->getName(),
                                'old_value' => $product->getCost(),
                                'value' => $cost,
                                'sku' => $product->getSku(),
                                'id' => $product->getId(),
                            ];

                            $product
                                ->setCost($cost)
                                ->save();
                        }
                    }
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

    public function notify()
    {
        if (!$this->isNotificationEnabled()) {
            $message = 'Entered isNotificationEnabled method, value: ' . $this->isNotificationEnabled();
            $this->logger->info($message);

            foreach ($this->changedProducts as $vendorId => $items) {
                $this->sendEmail($vendorId, $items);
            }
        }

        return $this;
    }

    protected function sendEmail($vendorId, $items)
    {
        $this->helperEmail->sendEmailTemplate($vendorId, $items);
    }

    public function isNotificationEnabled()
    {
        return $this->scopeConfig->getValue(
            'products_settings/csv_import/send_notification_when_supplier_uploads_products'
        );
    }
}
