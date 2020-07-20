<?php

namespace Cminds\Marketplace\Helper;

use Cminds\Supplierfrontendproductuploader\Helper\Data as DataParent;
use Magento\Catalog\Model\ProductFactory;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;

class Data extends DataParent
{
    const LOAD_SUPPLIER_FOR_ORDER_FLAG = 'load_supplier_for_order_flag';

    /**
     * @return array
     */
    public function getAllShippingMethods()
    {
        $methods = [];
        $config = $this->scopeConfig->getValue('carriers');
        foreach ($config as $code => $methodConfig) {
            if (!isset($methodConfig['title'])) {
                continue;
            }
            $methods[$code] = $methodConfig['title'];
        }

        return $methods;
    }

    public function hasAccess()
    {
        return true;
    }

    public function getSupplierPageUrl($product)
    {
        if ($product->getCreatorId()) {
            return $this->getSupplierRawPageUrl($product->getCreatorId());
        }
    }

    public function setSupplierDataInstalled($installed)
    {
        mail(
            'david@cminds.com',
            'Marketplace installed',
            'IP: ' . $_SERVER['SERVER_ADDR'] . ' host : ' . $_SERVER['SERVER_NAME']
        );
    }

    public function canCreateConfigurable()
    {
        return $this->scopeConfig->getValue(
            'configuration/presentation/allow_create_configurable'
        );
    }

    /**
     * Get store config if supplier can manage his shipping methods.
     *
     * @return bool
     */
    public function shippingMethodsEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            'configuration_marketplace/configure/allow_supplier_manage_shipping_costs'
        );
    }

    public function csvImportEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            'products_settings/csv_import/enable_csv_import'
        );
    }

    public function array2Csv(array $data)
    {
        if (count($data) === 0) {
            return null;
        }

        ob_start();
        $df = fopen('php://output', 'wb');
        fputcsv($df, array_keys(reset($data)));
        foreach ($data as $row) {
            foreach ($row as $column => &$value) {
                $value = ' ' . $value;
            }
            unset($value);
            fputcsv($df, $row);
        }
        fclose($df);

        return ob_get_clean();
    }

    public function getStatusesCanSee()
    {
        return explode(
            ',',
            $this->scopeConfig->getValue(
                'configuration_marketplace/'
                . 'presentation/'
                . 'order_statuses_supplier_can_see'
            )
        );
    }

    /**
     * Get store config if supplier shipping method is enabled.
     *
     * @return bool
     */
    public function isSupplierShippingMethodEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            'carriers/supplier/active'
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getEmailAdditionalAttributes($store = null)
    {
        return $this->scopeConfig->getValue(
            'configuration_marketplace/presentation/additional_attributes',
            'store',
            $store
        );
    }

    /**
     * @param bool $supplierId
     * @return bool|string
     */
    public function getSupplierLogoPath($supplierId = false)
    {
        if (!$supplierId) {
            $supplier = $this->getLoggedSupplier();
        } else {
            if (!$this->isSupplier($supplierId)) {
                return false;
            }
            $supplier = $this->customerFactory->create()->load($supplierId);
        }
        $path = $this->directoryList->getPath('media') . '/supplier_logos/';
        $path .= $supplier->getSupplierLogo();
        return $path;
    }

    /**
     * @return mixed
     */
    public function getGuestInvoiceEmailTemplate()
    {
        return $this->scopeConfig->getValue(
            'configuration_marketplace/presentation/invoice_guest_template',
            'store'
        );
    }

    /**
     * @return mixed
     */
    public function getInvoiceEmailTemplate()
    {
        return $this->scopeConfig->getValue(
            'configuration_marketplace/presentation/invoice_template',
            'store'
        );
    }

    /**
     * @return mixed
     */
    public function getAddSoldBy()
    {
        return $this->scopeConfig->getValue(
            'configuration_marketplace/presentation/add_sold_by_option_on_product_page',
            'store'
        );
    }

    /**
     * @return mixed
     */
    public function getSupplierShippingPriceNonSupplier()
    {
        return $this->scopeConfig->getValue(
            'carriers/supplier/shipping_cost_non_supplier_products',
            'store'
            );
    }
}
