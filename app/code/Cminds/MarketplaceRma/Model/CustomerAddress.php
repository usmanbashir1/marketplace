<?php

namespace Cminds\MarketplaceRma\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class CustomerAddress
 *
 * @package Cminds\MarketplaceRma\Model
 */
class CustomerAddress extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceRma\Model\ResourceModel\CustomerAddress'
        );
    }
}
