<?php

namespace Cminds\SupplierSubscription\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Plan extends AbstractDb
{
    /**
     * Resource model construct that should be used for object initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cminds_suppliersubscription_plan', 'entity_id');
    }
}
