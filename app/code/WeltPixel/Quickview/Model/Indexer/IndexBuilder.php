<?php
namespace WeltPixel\Quickview\Model\Indexer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use WeltPixel\Quickview\Model\QuickviewMessages;
use WeltPixel\Quickview\Model\QuickviewMessagesFactory;
use WeltPixel\Quickview\Model\ResourceModel\QuickviewMessages\CollectionFactory as QuickviewMessagesCollectionFactory;
use WeltPixel\Quickview\Model\Indexer\IndexBuilder\ProductLoader;
use Magento\Catalog\Model\Product;

class IndexBuilder
{
    /**
     * @var int
     */
    protected $batchCount;

    /**
     * @var string
     */
    protected $indexTableName;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var QuickviewMessagesCollectionFactory
     */
    protected $quickviewMessagesCollectionFactory;

    /**
     * @var QuickviewMessagesFactory
     */
    protected $quickviewMessagesFactory;

    /**
     * @var ProductLoader
     */
    private $productLoader;

    /**
     * @param ResourceConnection $resource
     * @param QuickviewMessagesCollectionFactory $quickviewMessagesCollectionFactory
     * @param QuickviewMessagesFactory $quickviewMessagesFactory
     * @param ProductLoader $productLoader
     */
    public function __construct(
        ResourceConnection $resource,
        QuickviewMessagesCollectionFactory $quickviewMessagesCollectionFactory,
        QuickviewMessagesFactory $quickviewMessagesFactory,
        ProductLoader $productLoader
    )
    {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->quickviewMessagesCollectionFactory = $quickviewMessagesCollectionFactory;
        $this->quickviewMessagesFactory = $quickviewMessagesFactory;
        $this->productLoader = $productLoader;
        $this->batchCount = 1000;
        $this->indexTableName = 'weltpixel_quickviewmessages_rule_idx';
    }

    /**
     * @return int
     */
    public function getBatchCount()
    {
        return $this->batchCount;
    }

    /**
     * @return string
     */
    public function getIndexTableName()
    {
        return $this->indexTableName;
    }

    /**
     * @return array
     */
    protected function getAllQuickviewMessages()
    {
        return $this->quickviewMessagesCollectionFactory->create();
    }

    /**
     * @throws \Exception
     * @return void
     */
    public function fullReindex()
    {
        $indexTableName = $this->getIndexTableName();
        $this->connection->truncateTable($this->resource->getTableName($indexTableName));
        try {
            foreach ($this->getAllQuickviewMessages() as $quickviewMessage) {
                $this->executeIndexForMessage($quickviewMessage);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param array $ids
     * @throws \Exception
     */
    public function reindexRule($ids)
    {
        $ids = array_unique($ids);
        $this->connection->beginTransaction();
        try {
            $this->cleanByIds($ids);
            foreach ($ids as $quickviewMessageId) {
                $quickviewMessage = $this->quickviewMessagesFactory->create()->load($quickviewMessageId);
                $this->executeIndexForMessage($quickviewMessage);
            }
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param array $ids
     * @return void
     */
    protected function cleanByIds($ids)
    {
        $query = $this->connection->deleteFromSelect(
            $this->connection
                ->select()
                ->from($this->resource->getTableName($this->getIndexTableName()), 'rule_id')
                ->distinct()
                ->where('rule_id IN (?)', $ids),
            $this->resource->getTableName($this->getIndexTableName())
        );
        $this->connection->query($query);
    }

    /**
     * @param array $ids
     * @return void
     */
    protected function cleanByProductIds($ids)
    {
        $query = $this->connection->deleteFromSelect(
            $this->connection
                ->select()
                ->from($this->resource->getTableName($this->getIndexTableName()), 'product_id')
                ->distinct()
                ->where('product_id IN (?)', $ids),
            $this->resource->getTableName($this->getIndexTableName())
        );
        $this->connection->query($query);
    }

    /**
     * @param \WeltPixel\Quickview\Model\QuickviewMessages $quickviewMessage
     * @return bool
     */
    protected function executeIndexForMessage($quickviewMessage)
    {
        $isQuickviewMessageEnabled = $quickviewMessage->getStatus();
        if (!$isQuickviewMessageEnabled) {
            return false;
        }

        $rows = [];
        $ruleId = $quickviewMessage->getId();
        $indexTableName = $this->getIndexTableName();

        $productIds = $quickviewMessage->getMatchingProductIds();

        foreach ($productIds as $productIdDetails) {
            foreach ($productIdDetails as $storeId => $productId) {
                $rows[] = [
                    'rule_id' => $ruleId,
                    'product_id' => $productId,
                    'store_id' => $storeId
                ];

                if (count($rows) == $this->batchCount) {
                    $this->connection->insertMultiple($this->resource->getTableName($indexTableName), $rows);
                    $rows = [];
                }
            }
        }

        if (!empty($rows)) {
            $this->connection->insertMultiple($this->resource->getTableName($indexTableName), $rows);
        }

        return true;
    }

    /**
     * @param array $ids
     */
    /**
     * @param array $ids
     * @throws \Exception
     */
    public function reindexProductRule($ids)
    {
        $ids = array_unique($ids);
        $this->connection->beginTransaction();
        try {
            $this->cleanByProductIds($ids);
            $products = $this->productLoader->getProducts($ids);
            foreach ($this->getAllQuickviewMessages() as $quickviewMessage) {
                foreach ($products as $product) {
                    $this->applyRule($quickviewMessage, $product);
                }
            }
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param QuickviewMessages $quickviewMessage
     * @param Product $product
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function applyRule(QuickviewMessages $quickviewMessage, $product)
    {
        $ruleId = $quickviewMessage->getId();
        $productId = $product->getId();
        $storeIds = $quickviewMessage->getAllStoreIdsAssigned();
        $indexTableName = $this->getIndexTableName();

        $this->connection->delete(
            $this->resource->getTableName($indexTableName),
            [
                $this->connection->quoteInto('rule_id = ?', $ruleId),
                $this->connection->quoteInto('product_id = ?', $productId)
            ]
        );

        $rows = [];
        try {
            foreach ($storeIds as $storeId) {
                $product->setStoreId($storeId);
                if (!$quickviewMessage->validate($product)) {
                    continue;
                }

                $rows[] = [
                    'rule_id' => $ruleId,
                    'product_id' => $productId,
                    'store_id' => $storeId
                ];

                if (count($rows) == $this->batchCount) {
                    $this->connection->insertMultiple($this->resource->getTableName($indexTableName), $rows);
                    $rows = [];
                }
            }

            if (!empty($rows)) {
                $this->connection->insertMultiple($this->resource->getTableName($indexTableName), $rows);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }
}
