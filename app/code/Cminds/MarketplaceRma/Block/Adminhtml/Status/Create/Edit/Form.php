<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Status\Create\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Config\Model\Config\Source\YesnoFactory;

/**
 * Class Form
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Status\Create\Edit
 */
class Form extends Generic
{
    /**
     * @var YesnoFactory
     */
    private $yesnoFactory;

    /**
     * Form constructor.
     *
     * @param Context      $context
     * @param Registry     $registry
     * @param FormFactory  $formFactory
     * @param YesnoFactory $yesnoFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        YesnoFactory $yesnoFactory
    ) {
        parent::__construct(
            $context,
            $registry,
            $formFactory
        );

        $this->yesnoFactory = $yesnoFactory;
    }

    /**
     * Prepare form.
     *
     * @return Form|Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
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
                'legend' => __('Edit Status'),
            ]
        );
        $fieldset->addField(
            'id',
            'text',
            [
                'name' => 'id',
                'disabled' => true,
                'label' => __('ID'),
            ]
        );
        $fieldset->addField(
            'name',
            'text',
            [
                'label' => __('Status'),
                'name' => 'name',
            ]
        );

        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}
