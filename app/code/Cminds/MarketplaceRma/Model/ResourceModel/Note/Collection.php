<?php

namespace Cminds\MarketplaceRma\Model\ResourceModel\Note;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Cminds\MarketplaceRma\Model\ResourceModel\Note
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceRma\Model\Note',
            'Cminds\MarketplaceRma\Model\ResourceModel\Note'
        );
    }
}
