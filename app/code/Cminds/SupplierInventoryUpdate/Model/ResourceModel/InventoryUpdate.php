<?php

namespace Cminds\SupplierInventoryUpdate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Cminds SupplierInventoryUpdate setting up main module table.
 *
 * @category Cminds
 * @package  Cminds_SupplierInventoryUpdate
 * @author   Mateusz Niziolek
 */
class InventoryUpdate extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('cminds_inventoryUpdate', 'entity_id');
    }
}
