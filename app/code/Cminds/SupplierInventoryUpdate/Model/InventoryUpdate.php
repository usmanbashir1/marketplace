<?php

namespace Cminds\SupplierInventoryUpdate\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Cminds SupplierInventoryUpdate one of the three file that
 * initiate resource model.
 *
 * @category Cminds
 * @package  Cminds_SupplierInventoryUpdate
 * @author   Mateusz Niziolek
 */
class InventoryUpdate extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\SupplierInventoryUpdate\Model\ResourceModel\InventoryUpdate'
        );
    }
}
