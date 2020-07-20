<?php

namespace Cminds\MarketplaceQa\Model\ResourceModel\Qa;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceQa\Model\Qa',
            'Cminds\MarketplaceQa\Model\ResourceModel\Qa'
        );
    }
}
