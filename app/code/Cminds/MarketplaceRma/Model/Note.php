<?php

namespace Cminds\MarketplaceRma\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Note
 *
 * @package Cminds\MarketplaceRma\Model
 */
class Note extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceRma\Model\ResourceModel\Note'
        );
    }
}
