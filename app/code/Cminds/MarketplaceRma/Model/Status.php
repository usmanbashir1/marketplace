<?php

namespace Cminds\MarketplaceRma\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Status
 *
 * @package Cminds\MarketplaceRma\Model
 */
class Status extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceRma\Model\ResourceModel\Status'
        );
    }
}
