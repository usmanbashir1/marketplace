<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Reason\Create\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Config\Model\Config\Source\YesnoFactory;

/**
 * Class Form
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Reason\Create\Edit
 */
class Form extends Generic
{
    /**
     * @var YesnoFactory
     */
    private $yesnoFactory;

    /**
     * @var FormFactory
     */
    protected $formFactory;

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
        $this->formFactory = $formFactory;
    }

    /**
     * Prepare form.
     *
     * @return Form|Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl(
                        'marketplacerma/reason/save'
                    ),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset(
            'createrma_form',
            [
                'legend' => __('Edit Returns'),
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
                'label' => __('Reason'),
                'name' => 'name',
            ]
        );

        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}
