<?php

namespace Cminds\Marketplace\Model\ResourceModel\Torate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\Marketplace\Model\Torate',
            'Cminds\Marketplace\Model\ResourceModel\Torate'
        );
    }
}
