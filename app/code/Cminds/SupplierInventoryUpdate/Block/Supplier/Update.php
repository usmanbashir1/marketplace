<?php

namespace Cminds\SupplierInventoryUpdate\Block\Supplier;

use Cminds\Supplierfrontendproductuploader\Helper\Data as SupplierHelper;
use Cminds\SupplierInventoryUpdate\Model\Config\Source\Action as DropDownSource;
use Cminds\SupplierInventoryUpdate\Model\ResourceModel\InventoryUpdate\CollectionFactory as UpdateCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Customer\Api\CustomerRepositoryInterfaceFactory as CustomerRepository;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Cminds SupplierInventoryUpdate Block class with form in vendor panel.
 *
 * @category Cminds
 * @package  Cminds_SupplierInventoryUpdate
 * @author   Mateusz Niziolek
 */
class Update extends Template
{
    private $urlBuilder;
    private $updateCollectionFactory;
    private $updateFactory;
    private $attributeFactory;
    private $customerRepository;
    private $supplierHelper;
    private $customerSession;
    private $dropDownSource;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        SupplierHelper $supplierHelper,
        DropDownSource $dropDownSource,
        CustomerRepository $customerRepository,
        Attribute $attributeFactory,
        UpdateFactory $updateFactory,
        UpdateCollectionFactory $updateCollectionFactory
    ) {
        parent::__construct($context);

        $this->customerSession = $customerSession;
        $this->dropDownSource = $dropDownSource;
        $this->supplierHelper = $supplierHelper;
        $this->customerRepository = $customerRepository->create();
        $this->attributeFactory = $attributeFactory;
        $this->updateFactory = $updateFactory;
        $this->updateCollectionFactory = $updateCollectionFactory;
        $this->urlBuilder = $context->getUrlBuilder();
    }

    public function getInventoryUpdate()
    {
        $model = $this->updateFactory->create();

        return $model;
    }

    public function getSupplier()
    {
        if ($this->customerSession->isLoggedIn()) {
            $supplier = $this->customerRepository->getById(
                $this->supplierHelper->getSupplierId()
            );

            return $supplier;
        } else {
            $this->customerSession->setAfterAuthUrl($this->urlBuilder->getCurrentUrl());
            $this->customerSession->authenticate();
        }
    }

    public function getCustomAttr($attributeCode)
    {
        $supplier = $this->getSupplier();
        if ($supplier->getCustomAttribute($attributeCode)) {
            return $supplier->getCustomAttribute($attributeCode)->getValue();
        }

        return '';
    }

    /**
     * Get Update model collection from custom database table
     *
     * @return array|false
     */
    public function getUpdateCollection($supplierId)
    {
        $collection = $this->updateCollectionFactory->create();
        foreach ($collection as $supplier) {
            if ($supplier->getSupplierId() == $supplierId) {
                return $supplier->getData();
            }
        }

        return false;
    }

    /**
     * Get options from custom source file
     * Cminds\SupplierInventoryUpdate\Model\Config\Source\Action
     *
     * @return array
     */
    public function getActionOptions()
    {
        $allOptions = $this->dropDownSource->toOptionArray();

        return $allOptions;
    }

    /**
     * Get all product attributes
     *
     * @return array
     */
    public function getAllAvailableAttributes()
    {
        $attributeCollection = $this->attributeFactory->getCollection();
        $attributeCollection->addFieldToFilter('entity_type_id', 4);
        $values = [];

        foreach ($attributeCollection as $attribute) {
            $attributeLabel = $attribute->getStoreLabel();
            $attributeCode = $attribute->getAttributeCode();

            if ($attributeLabel !== null) {
                $values[] = [
                    'label' => $attributeLabel,
                    'code' => $attributeCode,
                ];
            }
        }

        return $values;
    }
}
