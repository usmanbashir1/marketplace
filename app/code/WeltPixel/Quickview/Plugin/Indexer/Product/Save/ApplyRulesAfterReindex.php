<?php
namespace WeltPixel\Quickview\Plugin\Indexer\Product\Save;

use WeltPixel\Quickview\Model\Indexer\Product\ProductRuleIndexer;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Catalog\Model\Product;

class ApplyRulesAfterReindex
{
    /**
     * @var ProductRuleIndexer
     */
    protected $productRuleIndexer;

    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @param IndexerRegistry $indexerRegistry
     * @param ProductRuleIndexer $productRuleIndexer
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        ProductRuleIndexer $productRuleIndexer
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->productRuleIndexer = $productRuleIndexer;
    }

    /**
     * Apply catalog rules after product resource model save
     *
     * @param Product $subject
     * @return void
     */
    public function afterReindex(Product $subject)
    {
        $this->productRuleIndexer->executeRow($subject->getId());
    }
}
