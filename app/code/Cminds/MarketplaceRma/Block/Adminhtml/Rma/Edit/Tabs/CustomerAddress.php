<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Tabs;

use Cminds\MarketplaceRma\Model\CustomerAddressFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Class CustomerAddress
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Tabs
 */
class CustomerAddress extends Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CustomerAddressFactory
     */
    private $customerAddressFactory;

    /**
     * CustomerAddress constructor.
     *
     * @param Context                $context
     * @param Registry               $registry
     * @param FormFactory            $formFactory
     * @param CustomerAddressFactory $customerAddressFactory
     * @param array                  $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CustomerAddressFactory $customerAddressFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->customerAddressFactory = $customerAddressFactory;
    }

    /**
     * Prepare form.
     *
     * @return Generic
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $params = $this->registry->registry('rma_data');
        $customerAddress = $this->customerAddressFactory
            ->create()
            ->load($params->getData('id'), 'rma_id');

        $form = $this->formFactory->create();


        $fieldsetInfo = $form->addFieldset(
            'rma_customer_address_info',
            [
                'legend' => __('Contact Info'),
                'collapsible' => true
            ]
        );
        $fieldsetInfo->addField(
            'rma_customer_address_first_name',
            'text',
            [
                'name' => 'rma_customer_address[first_name]',
                'value' => $customerAddress->getData('first_name'),
                'label' => __('First Name'),
                'required' => true
            ]
        );
        $fieldsetInfo->addField(
            'rma_customer_address_last_name',
            'text',
            [
                'name' => 'rma_customer_address[last_name]',
                'value' => $customerAddress->getData('last_name'),
                'label' => __('Last Name'),
                'required' => true
            ]
        );
        $fieldsetInfo->addField(
            'rma_customer_address_company',
            'text',
            [
                'name' => 'rma_customer_address[company]',
                'value' => $customerAddress->getData('company'),
                'label' => __('Company'),
            ]
        );
        $fieldsetInfo->addField(
            'rma_customer_address_telephone',
            'text',
            [
                'name' => 'rma_customer_address[telephone]',
                'value' => $customerAddress->getData('telephone'),
                'label' => __('Telephone'),
                'required' => true
            ]
        );
        $fieldsetInfo->addField(
            'rma_customer_address_fax',
            'text',
            [
                'name' => 'rma_customer_address[fax]',
                'value' => $customerAddress->getData('fax'),
                'label' => __('Fax'),
            ]
        );

        $fieldsetReturnAddress = $form->addFieldset(
            'rma_customer_address_return_address',
            [
                'legend' => __('Return Address'),
                'collapsible' => true
            ]
        );
        $fieldsetReturnAddress->addField(
            'rma_customer_address_return_address_street',
            'text',
            [
                'name' => 'rma_customer_address[return_address_street]',
                'value' => $customerAddress->getData('street'),
                'label' => __('Street Address'),
                'required' => true
            ]
        );
        $fieldsetReturnAddress->addField(
            'rma_customer_address_return_address_city',
            'text',
            [
                'name' => 'rma_customer_address[return_address_city]',
                'value' => $customerAddress->getData('city'),
                'label' => __('City'),
                'required' => true
            ]
        );
        $fieldsetReturnAddress->addField(
            'rma_customer_address_return_address_country',
            'text',
            [
                'name' => 'rma_customer_address[return_address_country]',
                'value' => $customerAddress->getData('country'),
                'label' => __('Country'),
                'required' => true
            ]
        );
        $fieldsetReturnAddress->addField(
            'rma_customer_address_return_address_zipcode',
            'text',
            [
                'name' => 'rma_customer_address[return_address_zipcode]',
                'value' => $customerAddress->getData('zipcode'),
                'label' => __('Zip Code'),
                'required' => true,
                'class' => 'required-entry'
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
        return __('Customer Address');
    }

    /**
     * Return Tab title
     *
     * @return string
     *
     * @api
     */
    public function getTabTitle()
    {
        return __('Customer Address');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     *
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
     *
     * @api
     */
    public function isHidden()
    {
        return false;
    }
}
