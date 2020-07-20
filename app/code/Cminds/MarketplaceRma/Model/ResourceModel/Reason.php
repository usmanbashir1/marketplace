<?php

namespace Cminds\MarketplaceRma\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Reason
 *
 * @package Cminds\MarketplaceRma\Model\ResourceModel
 */
class Reason extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('cminds_marketplace_rma_reason', 'id');
    }
}
