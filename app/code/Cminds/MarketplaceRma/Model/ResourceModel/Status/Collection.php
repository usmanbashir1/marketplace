<?php

namespace Cminds\MarketplaceRma\Model\ResourceModel\Status;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Cminds\MarketplaceRma\Model\ResourceModel\Status
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceRma\Model\Status',
            'Cminds\MarketplaceRma\Model\ResourceModel\Status'
        );
    }
}
