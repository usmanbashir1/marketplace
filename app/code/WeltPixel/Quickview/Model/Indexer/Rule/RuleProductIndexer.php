<?php
namespace WeltPixel\Quickview\Model\Indexer\Rule;

use WeltPixel\Quickview\Model\Indexer\IndexBuilder;

class RuleProductIndexer implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var IndexBuilder
     */
    private $indexBuilder;

    /**
     * @param IndexBuilder $indexBuilder
     */
    public function __construct(
        IndexBuilder $indexBuilder
    ) {
        $this->indexBuilder = $indexBuilder;
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->indexBuilder->fullReindex();
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids)
    {
        $this->indexBuilder->reindexRule($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id)
    {
        if (!is_array($id)) {
            $id = [$id];
        }
        $this->indexBuilder->reindexRule($id);
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids)
    {
        $this->indexBuilder->reindexRule($ids);
    }
}
