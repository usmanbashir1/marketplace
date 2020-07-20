<?php

namespace WeltPixel\ProductLabels\Plugin\Indexer;

use WeltPixel\ProductLabels\Model\ProductLabels;
use Magento\Framework\Indexer\IndexerRegistry;
use WeltPixel\ProductLabels\Model\Indexer\Rule\RuleProductIndexer;

class ReindexRuleIdProducts
{
    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var RuleProductIndexer
     */
    protected $productLabelRuleIndexer;

    /**
     * ReindexRuleIdProducts constructor.
     * @param IndexerRegistry $indexerRegistry
     * @param RuleProductIndexer $productLabelRuleIndexer
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        RuleProductIndexer $productLabelRuleIndexer
    )
    {
        $this->indexerRegistry = $indexerRegistry;
        $this->productLabelRuleIndexer = $productLabelRuleIndexer;
    }

    /**
     * @param ProductLabels $subject
     * @param ProductLabels $result
     * @return ProductLabels
     */
    public function afterAfterSave(ProductLabels $subject, ProductLabels $result) {
        $ruleId = $subject->getId();
        $indexer = $this->indexerRegistry->get('weltpixel_productlabels_rule');
        if($indexer->isScheduled() == false){
            $this->productLabelRuleIndexer->executeRow($ruleId);
        }
        return $result;
    }
}
