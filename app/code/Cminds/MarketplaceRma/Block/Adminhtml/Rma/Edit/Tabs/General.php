<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Tabs;

use Cminds\MarketplaceRma\Model\OptionProvider;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Cminds\MarketplaceRma\Model\Rma as CmindsRma;

/**
 * Class Form
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Edit
 */
class General extends Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CmindsRma
     */
    private $rma;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var OptionProvider
     */
    private $optionProvider;

    private $customerFactory;

    private $orderFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CmindsRma $rma,
        OptionProvider $optionProvider,
        CustomerFactory $customerFactory,
        OrderFactory $orderFactory
    ) {
        parent::__construct(
            $context,
            $registry,
            $formFactory
        );

        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->rma = $rma;
        $this->optionProvider = $optionProvider;
        $this->customerFactory = $customerFactory;
        $this->orderFactory = $orderFactory;
    }

    /**
     * Prepare form method.
     *
     * @return Form|Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $params = $this->registry->registry('rma_data');

        $customer = $this->customerFactory->create()->load($params['customer_id']);
        $order = $this->orderFactory->create()->load($params['order_id']);

        $form = $this->formFactory->create();

        $infoFieldset = $form->addFieldset(
            'rma_form_general_info',
            [
                'legend' => __('General Info'),
                'collapsible' => true
            ]
        );
        $infoFieldset->addField(
            'order_increment_id',
            'label',
            [
                'label' => __('Order ID'),
                'name' => 'order_increment_id',
                'value' => $order->getIncrementId(),
                'required' => true,
                'disabled' => true
            ]
        );
        $infoFieldset->addField(
            'customer_name',
            'label',
            [
                'label' => __('Customer'),
                'name' => 'customer_name',
                'value' => $customer->getFirstname() . ' ' . $customer->getLastname(),
                'required' => true,
                'disabled' => true
            ]
        );
        $infoFieldset->addField(
            'created_at',
            'label',
            [
                'label' => __('Created at'),
                'name' => 'created_at',
                'value' => $params['created_at'],
                'required' => true,
                'disabled' => true
            ]
        );

        $fieldset = $form->addFieldset(
            'createrma_form',
            [
                'legend' => __('Details'),
                'collapsible' => true
            ]
        );
        $fieldset->addField(
            'id',
            'hidden',
            [
                'name' => 'id',
                'value' => $params['id'],
                'label' => __('ID'),
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'name' => 'status',
                'values' => $this->optionProvider->getAvailableStatuses(),
                'value' => $params['status'],
                'required' => true,
            ]
        );
        $fieldset->addField(
            'package_opened',
            'select',
            [
                'label' => __('Package Opened'),
                'name' => 'package_opened',
                'value' => $params['package_opened'],
                'options' => [
                    0 => __('No'), 
                    1 => __('Yes')
                ],
                'required' => true,
            ]
        );
        $fieldset->addField(
            'request_type',
            'select',
            [
                'label' => __('Request Type'),
                'name' => 'request_type',
                'values' => $this->optionProvider->getAvailableTypes(),
                'value' => $params['request_type'],
                'required' => true,
            ]
        );
        $fieldset->addField(
            'additional_info',
            'textarea',
            [
                'label' => __('Additional Info'),
                'name' => 'additional_info',
                'value' => $params['additional_info'],
            ]
        );
        $fieldset->addField(
            'reason',
            'select',
            [
                'label' => __('Reason'),
                'name' => 'reason',
                'values' => $this->optionProvider->getAvailableReasons(),
                'value' => $params['reason'],
                'required' => true,
            ]
        );

        $this->setForm($form);
        
        return parent::_prepareForm();
    }

    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle()
    {
        return __('General');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        return false;
    }
}
