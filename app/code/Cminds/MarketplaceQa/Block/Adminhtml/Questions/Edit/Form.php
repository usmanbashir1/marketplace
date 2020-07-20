<?php

namespace Cminds\MarketplaceQa\Block\Adminhtml\Questions\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry as CoreRegistry;

class Form extends Generic
{
    private $coreRegistry;
    private $productFactory;

    public function __construct(
        Context $context,
        CoreRegistry $coreRegistry,
        FormFactory $formFactory,
        ProductFactory $productFactory
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $formFactory
        );

        $this->coreRegistry = $coreRegistry;
        $this->productFactory = $productFactory;
    }

    protected function _prepareForm() // @codingStandardsIgnoreLine
    {
        $params = $this->coreRegistry->registry('question_data');
        $product = $this->productFactory->create()
            ->load($params['product_id']);

        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl(
                        'marketplaceqa/questions/save',
                        ['id' => $params['id']]
                    ),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset(
            'edit_form',
            [
                'legend' => __('Edit question of '.$params['customer_name']),
            ]
        );
        $fieldset->addField(
            'id',
            'hidden',
            [
                'name' => 'id',
            ]
        );
        $fieldset->addField(
            'product_name',
            'text',
            [
                'label' => __('Product Name'),
                'name' => 'product_name',
                'disabled' => true,
                'value' => $product->getName(),
            ]
        );
        $fieldset->addField(
            'customer_name',
            'text',
            [
                'label' => __('Author Name'),
                'name' => 'customer_name',
                'value' => $params['customer_name'],
            ]
        );
        $fieldset->addField(
            'visible_on_frontend',
            'select',
            [
                'label' => __('Visible on Frontend'),
                'name' => 'visible_on_frontend',
                'value' => $params['visible_on_frontend'],
                'options' => [0 => __('No'), 1 => __('Yes')],
            ]
        );
        $fieldset->addField(
            'approved',
            'select',
            [
                'label' => __('Approved'),
                'name' => 'approved',
                'value' => $params['approved'],
                'options' => [0 => __('Disapproved'), 1 => __('Approved')],
            ]
        );
        $fieldset->addField(
            'question',
            'textarea',
            [
                'label' => __('Question'),
                'name' => 'question',
                'value' => $params['question'],
            ]
        );
        $fieldset->addField(
            'answer',
            'textarea',
            [
                'label' => __('Answer'),
                'name' => 'answer',
                'value' => $params['answer'],
            ]
        );

        $this->setForm($form);
        parent::_prepareForm();

        return $this;
    }
}