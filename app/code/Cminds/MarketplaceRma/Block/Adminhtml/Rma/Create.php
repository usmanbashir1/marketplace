<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Rma;

use Cminds\MarketplaceRma\Model\OptionProvider;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Model\Session;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderFactory;
use Magento\Customer\Model\CustomerFactory;

/**
 * Class Create
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Rma
 */
class Create extends Generic
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var OptionProvider
     */
    private $optionProvider;

    /**
     * @var Session
     */
    private $backendSession;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var Context
     */
    private $context;

    /**
     * Create constructor.
     *
     * @param Context         $context
     * @param Registry        $registry
     * @param FormFactory     $formFactory
     * @param OptionProvider  $optionProvider
     * @param OrderFactory    $orderFactory
     * @param CustomerFactory $customerFactory
     * @param array           $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        OptionProvider $optionProvider,
        OrderFactory $orderFactory,
        CustomerFactory $customerFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->formFactory = $formFactory;
        $this->optionProvider = $optionProvider;
        $this->context = $context;
        $this->orderFactory = $orderFactory;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Prepare layout
     *
     * @return Create|Generic
     */
    protected function _prepareLayout()
    {
        // add buttons
        $this->getToolbar()->addChild(
            'backButton',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Back'),
                'onclick' => 'window.location.href=\'' . $this->getUrl('*/*/') . '\'',
                'class' => 'back'
            ]
        );

        $this->getToolbar()->addChild(
            'resetButton',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Reset'),
                'onclick' => 'window.location.href=\'' . $this->getUrl('*/*/create/is_new/1') . '\'',
                'class' => 'reset'
            ]
        );

        $newRmaTempData = $this->context->getBackendSession()->getNewRmaTempData();

        if ($newRmaTempData['step'] == 3) {
            $saveButtonLabel = __('Save');
        } else {
            $saveButtonLabel = __('Next');
        }
        $this->getToolbar()->addChild(
            'saveButton',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => $saveButtonLabel,
                'class' => 'save primary save-role',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'submit', 'target' => '#create_new_rma_form']],
                ]
            ]
        );

        return parent::_prepareLayout();
    }

    /**
     * Prepare form.
     *
     * @return Create|Generic
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $newRmaTempData = $this->context->getBackendSession()->getNewRmaTempData();

        if ($newRmaTempData == null) {
            $form = $this->_formFactory->create(
                [
                    'data' => [
                        'id' => 'create_new_rma_form',
                        'action' => $this->getUrl('*/*/create'),
                        'method' => 'post']
                ]
            );
            $form = $this->selectCustomer($form);
            $form->setUseContainer(true);
            $this->setForm($form);
        } elseif (isset($newRmaTempData['step'])
            && $newRmaTempData['step'] == 1
            && isset($newRmaTempData['customer_id'])
            && $newRmaTempData['customer_id'] != 0
        ) {
            $form = $this->_formFactory->create(
                [
                    'data' => [
                        'id' => 'create_new_rma_form',
                        'action' => $this->getUrl('*/*/create'),
                        'method' => 'post'
                    ]
                ]
            );

            $form = $this->selectOrder($form, $newRmaTempData['customer_id']);
            $form->setUseContainer(true);
            $this->setForm($form);
        } elseif (isset($newRmaTempData['step'])
            && $newRmaTempData['step'] == 2
            && isset($newRmaTempData['order_id'])
            && $newRmaTempData['order_id'] != 0
        ) {
            $form = $this->_formFactory->create(
                [
                    'data' => [
                        'id' => 'create_new_rma_form',
                        'action' => $this->getUrl('*/*/create'),
                        'method' => 'post'
                    ]
                ]
            );

            $form = $this->selectProducts($form, $newRmaTempData);
            $form->setUseContainer(true);
            $this->setForm($form);
        } elseif (isset($newRmaTempData['step'])
            && $newRmaTempData['step'] == 3
            && isset($newRmaTempData['rma_products'])
            && $newRmaTempData['rma_products'] != 0
        ) {
            $form = $this->_formFactory->create(
                [
                    'data' => [
                        'id' => 'create_new_rma_form',
                        'action' => $this->getUrl('*/*/save'),
                        'method' => 'post'
                    ]
                ]
            );

            $form = $this->selectDetails($form, $newRmaTempData);
            $form->setUseContainer(true);
            $this->setForm($form);
        }

        return parent::_prepareForm();
    }

    /**
     * Select customer fieldset.
     *
     * @param $form
     *
     * @return mixed
     */
    private function selectCustomer($form)
    {
        $fieldset = $form->addFieldset(
            'rma_form_select_customer',
            [
                'legend' => __('Select Customer'),
                'collapsible' => true
            ]
        );

        $fieldset->addField(
            'customer_id',
            'select',
            [
                'label' => __('Customer'),
                'name' => 'customer_id',
                'values' => $this->optionProvider->getCustomers(),
                'required' => true,
            ]
        );

        return $form;
    }

    /**
     * Select order fieldset.
     *
     * @param $form
     * @param $customerId
     *
     * @return mixed
     */
    private function selectOrder($form, $customerId)
    {
        $infoFieldset = $form->addFieldset(
            'rma_form_select_order',
            [
                'legend' => __('Select Order'),
                'collapsible' => true
            ]
        );

        $infoFieldset->addField(
            'order_id',
            'select',
            [
                'label' => __('Order'),
                'name' => 'order_id',
                'values' => $this->optionProvider->getCustomerOrders($customerId),
                'required' => true,
            ]
        );

        return $form;
    }

    /**
     * Select details fieldset.
     *
     * @param $form
     * @param $newRmaTempData
     *
     * @return mixed
     */
    private function selectDetails($form, $newRmaTempData)
    {
        $infoFieldset = $form->addFieldset(
            'rma_form_general_info',
            [
                'legend' => __('General Info'),
                'collapsible' => true
            ]
        );

        $order = $this->orderFactory->create()->load($newRmaTempData['order_id']);
        $infoFieldset->addField(
            'order_increment_id',
            'label',
            [
                'label' => __('Order ID'),
                'value' => $order->getIncrementId(),
                'required' => true,
                'disabled' => true
            ]
        );

        $customer = $this->customerFactory->create()->load($newRmaTempData['customer_id']);
        $infoFieldset->addField(
            'customer_name',
            'label',
            [
                'label' => __('Customer Id'),
                'value' => $customer->getFirstname() . ' ' . $customer->getLastname(),
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
            'package_opened',
            'select',
            [
                'label' => __('Package Opened'),
                'name' => 'package_opened',
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
                'required' => true,
            ]
        );
        $fieldset->addField(
            'additional_info',
            'textarea',
            [
                'label' => __('Additional Info'),
                'name' => 'additional_info'
            ]
        );
        $fieldset->addField(
            'reason',
            'select',
            [
                'label' => __('Reason'),
                'name' => 'reason',
                'values' => $this->optionProvider->getAvailableReasons(),
                'required' => true,
            ]
        );

        return $form;
    }

    /**
     * Select products fieldset.
     *
     * @param $form
     * @param $newRmaTempData
     *
     * @return mixed
     */
    public function selectProducts($form, $newRmaTempData)
    {
        $order = $this->orderFactory->create()->load($newRmaTempData['order_id']);
        $invoices = $order->getInvoiceCollection();

        foreach ($invoices as $invoice) {
            $fieldset = $form->addFieldset(
                'createrma_form_products_' . $invoice->getId(),
                [
                    'legend' => __('Select products qty for invoice #'. $invoice->getIncrementId()),
                    'collapsible' => true
                ]
            );

            $invoiceId = $invoice->getId();
            foreach ($invoice->getItems() as $item) {
                $qtyOptions = [];
                $invoicedQty = (int)$item->getQty();
                $productId = $item->getProductId();

                for ($i = 0; $i <= $invoicedQty ; $i++) {
                    $qtyOptions[] = [
                        'value' => $i,
                        'label' => $i
                    ];
                }
                $fieldset->addField(
                    'rma_product_'. $item->getId(),
                    'select',
                    [
                        'label' => __($item->getSku()),
                        'name' => 'rma_products[' . $invoiceId . '#' . $productId . ']',
                        'values' => $qtyOptions,
                        'required' => true,
                    ]
                );
            }
        }

        return $form;
    }
}
