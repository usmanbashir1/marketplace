<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Tabs;

use Cminds\MarketplaceRma\Model\ResourceModel\Note\CollectionFactory as NoteCollectionFactory;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;

/**
 * Class Notes
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Tabs
 */
class Notes extends Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
     * @var NoteCollectionFactory
     */
    private $noteCollectionFactory;

    /**
     * Notes constructor.
     *
     * @param Context               $context
     * @param Registry              $registry
     * @param FormFactory           $formFactory
     * @param NoteCollectionFactory $noteCollectionFactory
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        NoteCollectionFactory $noteCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->noteCollectionFactory = $noteCollectionFactory;
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

        $notes = $this->noteCollectionFactory
            ->create()
            ->addFieldToFilter('rma_id', $params->getData('id'))
            ->getItems();

        $form = $this->formFactory->create();

        $fieldsetAdd = $form->addFieldset(
            'rma_customer_notes',
            [
                'legend' => __('Add Note'),
                'collapsible' => true
            ]
        );
        $fieldsetAdd->addField(
            'rma_customer_notes_add',
            'textarea',
            [
                'name' => 'rma_customer_notes_add[content]',
                'label' => __('Note')
            ]
        );
        $fieldsetAdd->addField(
            'rma_customer_notes_add_notify',
            'checkbox',
            [
                'name' => 'rma_customer_notes_add[notify]',
                'label' => __('Notify Customer'),
                'onchange' => 'this.value = this.checked;'
            ]
        );

        foreach ($notes as $note) {
            $fieldsetNotes = $form->addFieldset(
                'rma_customer_notes_listing_' . $note->getData('id'),
                [
                    'legend' => __('Note'),
                    'collapsible' => true
                ]
            );

            $fieldsetNotes->addField(
                'rma_customer_notes_listing_note_' . $note->getData('id'),
                'label',
                [
                    'value' => $note->getData('note'),
                    'label' => __('Note')
                ]
            );

            $fieldsetNotes->addField(
                'rma_customer_notes_listing_notified_' . $note->getData('id'),
                'label',
                [
                    'value' => $note->getData('notify_customer') ? __('Yes') : __('No'),
                    'label' => __('Customer Notified'),
                ]
            );
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Return Tab label
     *
     * @return string
     *
     * @api
     */
    public function getTabLabel()
    {
        return __('Notes');
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
        return __('Notes');
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
