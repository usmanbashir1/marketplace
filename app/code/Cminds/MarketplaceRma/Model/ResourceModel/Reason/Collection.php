<?php

namespace Cminds\MarketplaceRma\Model\ResourceModel\Reason;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Cminds\MarketplaceRma\Model\ResourceModel\Reason
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceRma\Model\Reason',
            'Cminds\MarketplaceRma\Model\ResourceModel\Reason'
        );
    }
}
