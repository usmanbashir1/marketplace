<?php

namespace Cminds\SupplierInventoryUpdate\Controller\Adminhtml\Index;

use Magento\Customer\Controller\Adminhtml\Index;

class Update extends Index
{
    /**
     * Customer compare grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();

        return $resultLayout;
    }
}
