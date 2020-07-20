<?php

namespace Cminds\SupplierSubscription\Model\ResourceModel\Plan;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Resource model construct that should be used for object initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_init(
            '\Cminds\SupplierSubscription\Model\Plan',
            '\Cminds\SupplierSubscription\Model\ResourceModel\Plan'
        );
    }
}
