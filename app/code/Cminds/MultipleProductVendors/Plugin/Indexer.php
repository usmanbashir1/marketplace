<?php

namespace Cminds\MultipleProductVendors\Plugin;

use Magento\Indexer\Model\Indexer as MagentoIndexer;

class Indexer
{
    /**
     * This method is workaround for creating new indexer.
     * When new indexer is created the method getLatestUpdate is null.
     * Some of the methods because of PHP7.0 require, that this method returns only string.
     *
     * @param MagentoIndexer $indexer
     * @param string $result
     *
     * @return string
     */
    public function afterGetLatestUpdated(MagentoIndexer $indexer, $result)
    {
        if ($result === null) {
            return '';
        }

        return $result;
    }
}
