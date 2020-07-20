<?php

namespace Cminds\SupplierSubscription\Block\Adminhtml\Catalog\Plan\Plans\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

class Form extends Generic
{
    /**
     * Registry object.
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Form constructor.
     *
     * @param Context     $context
     * @param Registry    $registry
     * @param FormFactory $formFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory
    ) {
        parent::__construct(
            $context,
            $registry,
            $formFactory
        );

        $this->registry = $registry;
    }

    /**
     * Prepare form method.
     *
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl(
                        '*/*/edit',
                        ['_current' => true, 'continue' => 0]
                    ),
                    'method' => 'post',
                ],
            ]
        );

        $form->setUseContainer(true);
        $fieldset = $form->addFieldset(
            'general',
            [
                'legend' => __('Details'),
            ]
        );


        $this->addFieldsToFieldset(
            $fieldset,
            [
                'name' => [
                    'label' => __('Name'),
                    'input' => 'text',
                    'required' => true,
                ],
            ]
        );

        $this->addFieldsToFieldset(
            $fieldset,
            [
                'price' => [
                    'label' => __('Price'),
                    'input' => 'text',
                    'required' => true,
                    'class' => 'validate-number',
                ],
            ]
        );

        $this->addFieldsToFieldset(
            $fieldset,
            [
                'products_number' => [
                    'label' => __('Number of Products'),
                    'input' => 'text',
                    'class' => 'validate-number',
                    'required' => true,
                ],
            ]
        );

        $this->addFieldsToFieldset(
            $fieldset,
            [
                'images_number' => [
                    'label' => __('Number of Images Per Product'),
                    'input' => 'text',
                    'required' => true,
                    'class' => 'validate-number',
                ],
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Add field to fieldset method.
     *
     * @param Fieldset $fieldset
     * @param array    $fields
     *
     * @return Form
     */
    protected function addFieldsToFieldset(Fieldset $fieldset, array $fields)
    {
        $requestData = new DataObject($this->getRequest()->getParams());

        foreach ($fields as $name => $data) {
            $requestValue = $requestData->getData($name);
            if ($requestValue) {
                $data['value'] = $requestValue;
            }

            $data['name'] = "fieldData[$name]";
            $data['title'] = $data['label'];

            if (!array_key_exists('value', $data) && $this->getField()) {
                $data['value'] = $this->getField()->getData($name);
            }

            $fieldset->addField($name, $data['input'], $data);
        }

        return $this;
    }

    /**
     * Get field method.
     *
     * @return Cminds\SupplierSubscription\Model\Plan|null
     */
    protected function getField()
    {
        if (!$this->hasData('field')) {
            $data = $this->registry->registry('current_plan_data');

            $this->setData('field', $data);
        }

        return $this->getData('field');
    }
}
