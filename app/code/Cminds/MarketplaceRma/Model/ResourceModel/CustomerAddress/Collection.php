<?php

namespace Cminds\MarketplaceRma\Model\ResourceModel\CustomerAddress;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Cminds\MarketplaceRma\Model\ResourceModel\CustomerAddress
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceRma\Model\CustomerAddress',
            'Cminds\MarketplaceRma\Model\ResourceModel\CustomerAddress'
        );
    }
}
