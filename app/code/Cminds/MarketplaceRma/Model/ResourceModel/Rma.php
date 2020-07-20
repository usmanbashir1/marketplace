<?php

namespace Cminds\MarketplaceRma\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Rma
 *
 * @package Cminds\MarketplaceRma\Model\ResourceModel
 */
class Rma extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('cminds_marketplace_rma', 'id');
    }
}
