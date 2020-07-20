<?php

namespace Cminds\SupplierInventoryUpdate\Model\ResourceModel\InventoryUpdate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Cminds SupplierInventoryUpdate Collection initiate.
 *
 * @category Cminds
 * @package  Cminds_SupplierInventoryUpdate
 * @author   Mateusz Niziolek
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\SupplierInventoryUpdate\Model\InventoryUpdate',
            'Cminds\SupplierInventoryUpdate\Model\ResourceModel\InventoryUpdate'
        );
    }
}
