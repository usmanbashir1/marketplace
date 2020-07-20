<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Status\Edit\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;

/**
 * Class Form
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Status\Edit\Edit
 */
class Form extends Generic
{
    /**
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
     * Prepare form.
     *
     * @return Form|Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $params = $this->registry->registry('rma_status_data');
        if (isset($params['id'])) {
            $legend = __('Edit Status');
        } else {
            $legend = '';
        }

        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl(
                        'marketplacerma/status/save'
                    ),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset(
            'createrma_form',
            [
                'legend' => $legend,
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
            'name',
            'text',
            [
                'label' => __('Status Name'),
                'name' => 'name',
                'value' => $params['name'],
            ]
        );

        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}
