<?php

namespace Cminds\MarketplaceRma\Model\ResourceModel\Type;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Cminds\MarketplaceRma\Model\ResourceModel\Type
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceRma\Model\Type',
            'Cminds\MarketplaceRma\Model\ResourceModel\Type'
        );
    }
}
