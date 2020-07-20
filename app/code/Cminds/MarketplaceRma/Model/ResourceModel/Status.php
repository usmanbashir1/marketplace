<?php

namespace Cminds\MarketplaceRma\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Status
 *
 * @package Cminds\MarketplaceRma\Model\ResourceModel
 */
class Status extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('cminds_marketplace_rma_status', 'id');
    }
}
