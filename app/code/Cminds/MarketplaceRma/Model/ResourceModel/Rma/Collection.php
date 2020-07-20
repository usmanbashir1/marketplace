<?php

namespace Cminds\MarketplaceRma\Model\ResourceModel\Rma;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Cminds\MarketplaceRma\Model\ResourceModel\Rma
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceRma\Model\Rma',
            'Cminds\MarketplaceRma\Model\ResourceModel\Rma'
        );
    }
}
