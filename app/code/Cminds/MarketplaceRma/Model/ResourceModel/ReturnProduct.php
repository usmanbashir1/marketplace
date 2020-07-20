<?php

namespace Cminds\MarketplaceRma\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ReturnProduct
 *
 * @package Cminds\MarketplaceRma\Model\ResourceModel
 */
class ReturnProduct extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('cminds_marketplace_rma_return_product', 'id');
    }
}
