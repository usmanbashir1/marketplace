<?php

namespace Cminds\MarketplaceRma\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Reason
 *
 * @package Cminds\MarketplaceRma\Model
 */
class Reason extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceRma\Model\ResourceModel\Reason'
        );
    }
}
