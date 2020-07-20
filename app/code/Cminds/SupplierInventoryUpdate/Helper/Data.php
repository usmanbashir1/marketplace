<?php

namespace Cminds\SupplierInventoryUpdate\Helper;

use Cminds\Supplierfrontendproductuploader\Helper\Data as SupplierHelper;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Cminds SupplierInventoryUpdate helper class.
 *
 * @category Cminds
 * @package  Cminds_SupplierInventoryUpdate
 * @author   Mateusz Niziolek
 */
class Data extends AbstractHelper
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var SupplierHelper
     */
    private $supplierHelper;

    public function __construct(
        Context $context,
        SupplierHelper $supplierHelper,
        CustomerRepository $customerRepository
    ) {
        $this->supplierHelper = $supplierHelper;
        $this->customerRepository = $customerRepository;

        parent::__construct($context);
    }

    public function isEnabled()
    {
        $value = (int)$this->scopeConfig->getValue('inventory_update/general/enable');

        return $value === 1;
    }

    public function getSupplier()
    {
        $supplier = $this->customerRepository->getById(
            $this->supplierHelper->getSupplierId()
        );

        return $supplier;
    }
}
