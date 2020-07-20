<?php

namespace Cminds\MarketplaceQa\Model;

use Magento\Framework\Model\AbstractModel;

class Qa extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Cminds\MarketplaceQa\Model\ResourceModel\Qa');
    }
}
