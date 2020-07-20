<?php

namespace Cminds\MarketplaceRma\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Note
 *
 * @package Cminds\MarketplaceRma\Model\ResourceModel
 */
class Note extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('cminds_marketplace_rma_note', 'id');
    }
}
