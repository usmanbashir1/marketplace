<?php

namespace Cminds\MarketplaceRma\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class CustomerAddress
 *
 * @package Cminds\MarketplaceRma\Model\ResourceModel
 */
class CustomerAddress extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('cminds_marketplace_rma_customer_address', 'id');
    }
}
