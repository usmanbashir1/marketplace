<?php
namespace WeltPixel\ProductLabels\Model\Indexer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use WeltPixel\ProductLabels\Model\ProductLabels;
use WeltPixel\ProductLabels\Model\ProductLabelsFactory;
use WeltPixel\ProductLabels\Model\ResourceModel\ProductLabels\CollectionFactory as ProductLabelsCollectionFactory;
use WeltPixel\ProductLabels\Model\Indexer\IndexBuilder\ProductLoader;
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
     * @var ProductLabelsCollectionFactory
     */
    protected $productLabelsCollectionFactory;

    /**
     * @var ProductLabelsFactory
     */
    protected $productLabelsFactory;

    /**
     * @var ProductLoader
     */
    private $productLoader;

    /**
     * @param ResourceConnection $resource
     * @param ProductLabelsCollectionFactory $productLabelsCollectionFactory
     * @param ProductLabelsFactory $productLabelsFactory
     * @param ProductLoader $productLoader
     */
    public function __construct(
        ResourceConnection $resource,
        ProductLabelsCollectionFactory $productLabelsCollectionFactory,
        ProductLabelsFactory $productLabelsFactory,
        ProductLoader $productLoader
    )
    {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->productLabelsCollectionFactory = $productLabelsCollectionFactory;
        $this->productLabelsFactory = $productLabelsFactory;
        $this->productLoader = $productLoader;
        $this->batchCount = 1000;
        $this->indexTableName = 'weltpixel_productlabels_rule_idx';
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
    protected function getAllProductLabels()
    {
        return $this->productLabelsCollectionFactory->create();
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
            foreach ($this->getAllProductLabels() as $productLabel) {
                $this->executeIndexForLabel($productLabel);
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
            foreach ($ids as $productLabelId) {
                $productLabel = $this->productLabelsFactory->create()->load($productLabelId);
                $this->executeIndexForLabel($productLabel);
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
     * @param \WeltPixel\ProductLabels\Model\ProductLabels $productLabel
     * @return bool
     */
    protected function executeIndexForLabel($productLabel)
    {
        $isProductLabelEnabled = $productLabel->getStatus();
        if (!$isProductLabelEnabled) {
            return false;
        }

        $rows = [];
        $ruleId = $productLabel->getId();
        $indexTableName = $this->getIndexTableName();

        $productIds = $productLabel->getMatchingProductIds();

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
            foreach ($this->getAllProductLabels() as $productLabel) {
                foreach ($products as $product) {
                    $this->applyRule($productLabel, $product);
                }
            }
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param ProductLabels $productLabel
     * @param Product $product
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function applyRule(ProductLabels $productLabel, $product)
    {
        $ruleId = $productLabel->getId();
        $productId = $product->getId();
        $storeIds = $productLabel->getAllStoreIdsAssigned();
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
                if (!$productLabel->validate($product)) {
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
