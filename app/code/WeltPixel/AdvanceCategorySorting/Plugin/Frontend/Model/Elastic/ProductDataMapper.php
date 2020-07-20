<?php

namespace WeltPixel\AdvanceCategorySorting\Plugin\Frontend\Model\Elastic;

use Magento\Elasticsearch\Model\Adapter\BatchDataMapper\ProductDataMapper as ProductDataMapperAlias;
use Magento\Elasticsearch\Model\Adapter\FieldType\Date as DateFieldType;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class ProductDataMapper
{

    /**
     * @var DateFieldType
     */
    private $dateFieldType;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var string
     */
    protected $productEntityTable;

    /**
     * @var string
     */
    protected $reviewEntityTable;

    /**
     * @var string
     */
    protected $ratingVoteTable;

    /**
     * @var string
     */
    protected $salesOrderItemTable;

    /**
     * @var string
     */
    protected $salesOrderTable;

    /**
     * @var string
     */
    protected $salesBestsellerAggregateTable;

    /**
     * @var bool
     */
    protected $useSalesAggregateTable;

    /**
     * @param DateFieldType $dateFieldType
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        DateFieldType $dateFieldType,
        ResourceConnection $resourceConnection
    ) {
        $this->dateFieldType = $dateFieldType;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Map index data for using in search engine metadata
     *
     * @param ProductDataMapperAlias $subject
     * @param array $result
     * @param array $documentData
     * @param int $storeId
     * @param array $context
     * @return array
     */
    public function afterMap(ProductDataMapperAlias $subject, $result, array $documentData, $storeId, array $context = [])
    {
        $this->connection = $this->resourceConnection->getConnection();
        $this->productEntityTable = $this->resourceConnection->getTableName('catalog_product_entity');
        $this->reviewEntityTable = $this->resourceConnection->getTableName('review_entity_summary');
        $this->ratingVoteTable = $this->resourceConnection->getTableName('rating_option_vote_aggregated');
        $this->salesOrderItemTable = $this->resourceConnection->getTableName('sales_order_item');
        $this->salesOrderTable = $this->resourceConnection->getTableName('sales_order');
        $this->salesBestsellerAggregateTable = $this->resourceConnection->getTableName('sales_bestsellers_aggregated_yearly');

        if (!isset($this->useSalesAggregateTable)) {
            $countResult = $this->connection->fetchOne("SELECT count(*) FROM " . $this->salesBestsellerAggregateTable);

            if ($countResult) {
                $this->useSalesAggregateTable = true;
            } else {
                $this->useSalesAggregateTable = false;
            }
        }

        foreach ($result as $productId => $indexData) {
            $additionalData = $this->getAdditionalData($productId, $storeId);
            $result[$productId] = array_merge_recursive(
                $result[$productId],
                $additionalData
            );
        }

        return $result;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    protected function getAdditionalData($productId, $storeId)
    {
        $additionalData = [];
        $productCreatedAt = $this->getProductCreatedAt($productId, $storeId);
        $productRates = $this->getProductRates($productId, $storeId);
        $productReview = $this->getProductReview($productId, $storeId);
        $productSales = $this->getProductSales($productId, $storeId);

        $additionalData['created_at']  = $productCreatedAt;
        $additionalData['wp_sortby_new']  = $productCreatedAt;
        $additionalData['wp_sortby_rates']  = $productRates;
        $additionalData['wp_sortby_review']  = $productReview;
        $additionalData['wp_sortby_sales']  = $productSales;

        return $additionalData;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return string
     */
    protected function getProductCreatedAt($productId, $storeId)
    {
        $productCreatedAt = $this->connection->fetchOne(
            "SELECT created_at FROM " . $this->productEntityTable . " WHERE entity_id = " . $productId
        );

        $productCreatedAt = $this->dateFieldType->formatDate($storeId, $productCreatedAt);

        return $productCreatedAt;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return float
     */
    protected function getProductRates($productId, $storeId)
    {
        $productRates = $this->connection->fetchOne(
            "SELECT percent_approved FROM " . $this->ratingVoteTable . " WHERE entity_pk_value = " .
            $productId . " AND store_id = " . $storeId
        );
        $productRates = ($productRates) ? $productRates : 0;

        return $productRates;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return float
     */
    protected function getProductReview($productId, $storeId)
    {
        $productReview = $this->connection->fetchOne(
            "SELECT reviews_count FROM " . $this->reviewEntityTable . " WHERE entity_pk_value = "
            . $productId . " AND store_id = " . $storeId
        );
        $productReview = ($productReview) ? $productReview : 0;

        return $productReview;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return float
     */
    protected function getProductSales($productId, $storeId)
    {
        if ($this->useSalesAggregateTable) {
            $productSales = $this->connection->fetchOne(
              "SELECT SUM(qty_ordered) FROM " . $this->salesBestsellerAggregateTable . " WHERE product_id = "
                . $productId . " AND store_id = " . $storeId
            );
        } else {
            $productSales = $this->connection->fetchOne(
                "SELECT SUM(salesitem.qty_ordered - salesitem.qty_refunded) FROM "
                . $this->salesOrderItemTable . " AS salesitem LEFT JOIN  " . $this->salesOrderTable . " AS sales "
                . " ON  salesitem.order_id = sales.entity_id "
                . " WHERE sales.status = 'complete' AND salesitem.store_id = " . $storeId
                . " AND salesitem.product_id =  " . $productId
            );
        }

        $productSales = ($productSales) ? $productSales : 0;

        return $productSales;
    }
}
