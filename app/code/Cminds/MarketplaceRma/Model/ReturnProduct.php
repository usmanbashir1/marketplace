<?php

namespace Cminds\MarketplaceRma\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class ReturnProduct
 *
 * @package Cminds\MarketplaceRma\Model
 */
class ReturnProduct extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceRma\Model\ResourceModel\ReturnProduct'
        );
    }
}
