<?php

namespace Cminds\MarketplaceRma\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Type
 *
 * @package Cminds\MarketplaceRma\Model
 */
class Type extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceRma\Model\ResourceModel\Type'
        );
    }
}
