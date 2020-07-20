<?php

namespace Cminds\Marketplace\Observer\Sales\Invoice\Email;

use Cminds\Marketplace\Model\Fields;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderFactory;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Cminds\Marketplace\Helper\Data as ConfigHelper;

/**
 * Class AddEmailTemplateVars
 *
 * @package Cminds\Marketplace\Observer\Sales\Invoice\Email
 */
class AddEmailTemplateVars implements ObserverInterface
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var Data
     */
    protected $supplierFrontendProductUploaderHelperData;

    /**
     * Fields object.
     *
     * @var Fields
     */
    protected $fields;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * AddEmailTemplateVars constructor.
     * @param ProductFactory $productFactory
     * @param OrderFactory $orderFactory
     * @param Data $data
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param Fields $fields
     * @param ConfigHelper $configHelper
     * @param Registry $registry
     */
    public function __construct(
        ProductFactory $productFactory,
        OrderFactory $orderFactory,
        Data $data,
        CustomerRepositoryInterface $customerRepositoryInterface,
        Fields $fields,
        ConfigHelper $configHelper,
        Registry $registry
    ) {
        $this->productFactory = $productFactory;
        $this->orderFactory = $orderFactory;
        $this->supplierFrontendProductUploaderHelperData = $data;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->fields = $fields;
        $this->configHelper = $configHelper;
        $this->registry = $registry;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        if (!$this->supplierFrontendProductUploaderHelperData->isEnabled()) {
            return $this;
        }
        $transportObject = $observer->getData('transportObject');

        if (empty($transportObject) || empty($transportObject->getOrder())) {
            return $this;
        }

        $order = $transportObject->getOrder();

        $items = $order->getAllItems();
        $supplierId = null;
        $supplierId = $this->getSupplierIdFromOrderItems($items);
        if ($supplierId) {
            $supplier = $this->getSupplierById($supplierId);
            if (!empty($supplier)) {
                $additionalAttributes = $this->getCustomFieldsValues($supplier);
                if (!empty($additionalAttributes)) {
                    $counter = 1;
                    foreach ($additionalAttributes as $label => $value) {
                        if ($counter > 2) {
                            break;
                        }
                        $transportObject->setData('additional_attribute_label_' . $counter, $label);
                        $transportObject->setData('additional_attribute_value_' . $counter, $value);
                        $counter++;
                    }
                }
                $logo = $this->supplierFrontendProductUploaderHelperData->getSupplierLogo($supplierId);
                if (!empty($logo)) {
                    $transportObject->setData('supplier_logo', $logo);
                }

                $supplierName = $this->getSupplierName($supplier);
                $transportObject->setData('supplier_name', $supplierName);
                $this->registry->register(ConfigHelper::LOAD_SUPPLIER_FOR_ORDER_FLAG, $supplierId);
            }
        }
        return $transportObject;
    }

    /**
     * @param $supplier
     * @return string
     */
    protected function getSupplierName($supplier)
    {
        $supplierName = $supplier->getFirstname() . ' ' . $supplier->getLastname();
        if (empty($supplier->getCustomAttributes()) || empty($supplier->getCustomAttributes()['supplier_name'])) {
            return $supplierName;
        }
        return $supplier->getCustomAttributes()['supplier_name']->getValue();
    }

    /**
     * @param $items
     * @return string|null
     */
    protected function getSupplierIdFromOrderItems($items)
    {
        foreach ($items as $item) {
            $product = $this->productFactory->create()->load($item->getProductId());
            if ($product->getCreatorId() !== null) {
                return $product->getCreatorId();
            }
        }
        return null;
    }

    /**
     * @param $supplierId
     * @return CustomerInterface|null
     */
    protected function getSupplierById($supplierId)
    {
        try {
            return $this->customerRepositoryInterface->getById($supplierId);
        } catch (LocalizedException $e) {
            return null;
        }
    }

    /**
     * Get values of custom fields, which are approved.
     *
     * @param $customer
     *
     * @return array
     */
    public function getCustomFieldsValues($customer)
    {
        $additionalAttributesConfig = $this->configHelper->getEmailAdditionalAttributes();
        if (empty(trim($additionalAttributesConfig))) {
            return null;
        }
        if (empty($customer->getCustomAttributes()) || empty($customer->getCustomAttributes()['custom_fields_values'])) {
            return null;
        }
        $attributes = array_map(function ($item) {
            return trim($item);
        }, explode(',', $additionalAttributesConfig));

        $values = $customer->getCustomAttributes()['custom_fields_values'];
        $dbValues = unserialize($values->getValue());
        $ret = [];
        foreach ($dbValues AS $value) {
            if (in_array($value['name'], $attributes)) {
                $v = $this->fields->load($value['name'], 'name');
                if (isset($v)) {
                    $ret[$v->getLabel()] = $value['value'];
                }
            }
        }

        return $ret;
    }
}
