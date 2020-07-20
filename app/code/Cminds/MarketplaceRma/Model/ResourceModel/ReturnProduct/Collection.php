<?php

namespace Cminds\MarketplaceRma\Model\ResourceModel\ReturnProduct;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Cminds\MarketplaceRma\Model\ResourceModel\ReturnProduct
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceRma\Model\ReturnProduct',
            'Cminds\MarketplaceRma\Model\ResourceModel\ReturnProduct'
        );
    }
}
