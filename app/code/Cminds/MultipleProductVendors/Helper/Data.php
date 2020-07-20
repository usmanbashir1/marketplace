<?php

namespace Cminds\MultipleProductVendors\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }


    public function isEnabled()
    {
        $value = (int)$this->scopeConfig->getValue(
            'multiple_product_vendors/general/module_enabled'
        );

        return $value === 1;
    }
    
    public function isSupplierPageEnabled()
    {
        $value = (int)$this->scopeConfig->getValue(
            'configuration_marketplace/configure/enable_supplier_pages'
        );

        return $value === 1;
    }

    public function canShowSoldBy()
    {
        $value = (int)$this->scopeConfig->getValue(
            'configuration/presentation/add_sold_by_option_on_product_page'
        );

        return $value === 1;
    }
}