<?php

namespace Cminds\MarketplaceQa\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Qa extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('cminds_marketplace_qa', 'id');
    }
}
