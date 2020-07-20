<?php

namespace Cminds\SupplierSubscription\Block\Adminhtml\Catalog\Plan\Plans;

use Magento\Backend\Block\Widget\Form\Container;

class Form extends Container
{
    /**
     * @var string
     */
    protected $_objectId = 'entity_id';

    /**
     * Object initialization.
     */
    public function _construct()
    {
        parent::_construct();

        $this->_controller = 'adminhtml_catalog_plan_plans';
        $this->_blockGroup = 'Cminds_SupplierSubscription';
        $this->_mode = 'edit';

        $newOrEdit = $this->getRequest()->getParam('id')
            ? __('Edit')
            : __('New');
        $this->_headerText = $newOrEdit . ' ' . __('Subscription Plan');

        $this->removeButton('add');
    }

    /**
     * Return delete url for current plan.
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            '*/*/delete',
            ['id' => $this->getRequest()->getParam('entity_id')]
        );
    }
}
