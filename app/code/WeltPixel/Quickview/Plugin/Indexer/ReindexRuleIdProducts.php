<?php

namespace WeltPixel\Quickview\Plugin\Indexer;

use WeltPixel\Quickview\Model\QuickviewMessages;
use Magento\Framework\Indexer\IndexerRegistry;
use WeltPixel\Quickview\Model\Indexer\Rule\RuleProductIndexer;

class ReindexRuleIdProducts
{
    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var RuleProductIndexer
     */
    protected $quickviewMessagesRuleIndexer;

    /**
     * ReindexRuleIdProducts constructor.
     * @param IndexerRegistry $indexerRegistry
     * @param RuleProductIndexer $quickviewMessagesRuleIndexer
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        RuleProductIndexer $quickviewMessagesRuleIndexer
    )
    {
        $this->indexerRegistry = $indexerRegistry;
        $this->quickviewMessagesRuleIndexer = $quickviewMessagesRuleIndexer;
    }

    /**
     * @param QuickviewMessages $subject
     * @param QuickviewMessages $result
     * @return QuickviewMessages
     */
    public function afterAfterSave(QuickviewMessages $subject, QuickviewMessages $result) {
        $ruleId = $subject->getId();
        $indexer = $this->indexerRegistry->get('weltpixel_quickviewmessages_rule');
        if($indexer->isScheduled() == false){
            $this->quickviewMessagesRuleIndexer->executeRow($ruleId);
        }
        return $result;
    }
}
