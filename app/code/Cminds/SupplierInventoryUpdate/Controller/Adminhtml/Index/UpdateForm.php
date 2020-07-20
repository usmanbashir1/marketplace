<?php

namespace Cminds\SupplierInventoryUpdate\Controller\Adminhtml\Index;

use Magento\Customer\Controller\Adminhtml\Index;

/**
 * Cminds SupplierInventoryUpdate Custom Tab Controller.
 *
 * @category Cminds
 * @package  Cminds_SupplierInventoryUpdate
 * @author   Mateusz Niziolek
 */
class UpdateForm extends Index
{
    public function execute()
    {
        $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();

        return $resultLayout;
    }
}
