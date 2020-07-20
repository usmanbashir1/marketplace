<?php

namespace Cminds\MarketplaceRma\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Type
 *
 * @package Cminds\MarketplaceRma\Model\ResourceModel
 */
class Type extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('cminds_marketplace_rma_type', 'id');
    }
}
