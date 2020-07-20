<?php

namespace Cminds\Marketplace\Block\Catalog\Product\Supplier;

use Cminds\Marketplace\Helper\Data as CmindsHelper;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Cminds\Marketplace\Block\Catalog\Product\Supplier;

/**
 * Class Name
 *
 * @package Cminds\Marketplace\Block\Catalog\Product\Supplier
 */
class Name extends Supplier
{
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerResource
     */
    protected $customerResource;

    /**
     * Scope config object.
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        Context $context,
        Registry $registry,
        CmindsHelper $cmindsHelper,
        ObjectManagerInterface $objectManagerInterface,
        CustomerFactory $customerFactory,
        CustomerResource $customerResource,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct(
            $context,
            $registry,
            $cmindsHelper,
            $objectManagerInterface
        );

        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->scopeConfig = $scopeConfig;
    }

    public function _construct()
    {
        $this->setTemplate('marketplace/catalog/product/supplier/name.phtml');
    }

    public function getProductSupplierName()
    {
        $supplierId = $this->getSupplierId();
        if (!$supplierId) {
            return false;
        }

        $customer = $this->customerFactory->create();
        $this->customerResource->load($customer, $supplierId);

        if (!$customer->getId()) {
            return false;
        }

        if ($customer->getSupplierName()) {
            return $customer->getSupplierName();
        }

        return sprintf(
            '%s %s',
            $customer->getFirstname(),
            $customer->getLastname()
        );
    }

    public function canShowBlockOnFront()
    {
        return $this->isCreatedBySupplier() && $this->canShowSoldBy() && !empty($this->getProductSupplierName());
    }

    public function isSupplierHasProfile()
    {
        if (!$this->isCreatedBySupplier()) {
            return false;
        }

        $customer = $this->customerFactory->create();
        $this->customerResource->load($customer, $this->getSupplierId());
        if (!$customer->getId()) {
            return false;
        }

        if (!$this->scopeConfig->getValue('configuration_marketplace/configure/enable_supplier_pages')) {
            return false;
        }

        if (!$customer->getSupplierProfileVisible()) {
            return false;
        }

        if (!$customer->getSupplierProfileApproved()) {
            return false;
        }

        return true;
    }

    public function getSupplierPageUrl()
    {
        return $this->getMarketplaceHelper()->getSupplierPageUrl($this->getProduct());
    }

    public function getMarketplaceHelper()
    {
        return $this->_cmindsHelper;
    }
}
