<?php

namespace Cminds\SupplierSubscription\Block\Adminhtml\Catalog\Plan;

class Plans extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Object initialization.
     */
    public function _construct()
    {
        $this->_controller = 'adminhtml_catalog_plan_plans';
        $this->_blockGroup = 'Cminds_SupplierSubscription';
        $this->_headerText = __('Supplier Subscription Plans');
        $this->_addButtonLabel = __('Add New');

        parent::_construct();
    }

    /**
     * Get create plan url.
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/add');
    }
}
