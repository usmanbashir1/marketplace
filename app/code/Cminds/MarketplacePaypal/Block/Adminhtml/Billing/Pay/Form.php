<?php

namespace Cminds\MarketplacePaypal\Block\Adminhtml\Billing\Pay;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry as CoreRegistry;
use Magento\Sales\Model\OrderFactory;

/**
 * Billing Pay Form
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class Form extends Generic
{
    /**
     * Order factory object.
     *
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * Form constructor.
     *
     * @param Context      $context
     * @param CoreRegistry $coreRegistry
     * @param FormFactory  $formFactory
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        Context $context,
        CoreRegistry $coreRegistry,
        FormFactory $formFactory,
        OrderFactory $orderFactory
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $formFactory
        );

        $this->orderFactory = $orderFactory;
    }

    /**
     * Prepare form before rendering HTML.
     *
     * @return Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm() // @codingStandardsIgnoreLine
    {
        $payment = $this->_coreRegistry
            ->registry('marketplacepaypal_billing_payment');

        $orderId = $payment->getOrderId();
        $supplierId = $payment->getSupplierId();

        $order = $this->orderFactory->create()
            ->load($orderId);

        $vendorIncome = $payment->getTotalVendorIncome();
        $paidAmount = $payment->getTotalPaidAmount();
        $unsettledAmount = $vendorIncome - $paidAmount;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl(
                        'marketplacepaypal/billing/payPost'
                    ),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset(
            'payment_form',
            [
                'legend' => __('Paypal Payment Details'),
            ]
        );
        $fieldset->addField(
            'id',
            'hidden',
            [
                'name' => 'order_id',
                'value' => $orderId,
            ]
        );
        $fieldset->addField(
            'supplier_id',
            'hidden',
            [
                'name' => 'supplier_id',
                'value' => $supplierId,
            ]
        );
        $fieldset->addField(
            'order_id',
            'link',
            [
                'name' => 'order_id',
                'label' => __('Order Id'),
                'title' => __('Order Id'),
                'value' => __('#%1', $order->getRealOrderId()),
                'href' => $this->getUrl(
                    'sales/order/view',
                    ['order_id' => $order->getId()]
                ),
                'style' => 'font-weight:600;line-height:32px;',
            ]
        );
        $fieldset->addField(
            'paid',
            'text',
            [
                'name' => 'paid',
                'label' => __('Paid Amount'),
                'title' => __('Paid Amount'),
                'value' => $paidAmount,
                'disabled' => true,
            ]
        );
        $fieldset->addField(
            'unsettled',
            'text',
            [
                'name' => 'unsettled',
                'label' => __('Unsettled Amount'),
                'title' => __('Unsettled Amount'),
                'value' => $unsettledAmount,
                'disabled' => true,
            ]
        );
        $fieldset->addField(
            'amount',
            'text',
            [
                'label' => __('Amount To Pay'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'amount',
                'value' => $unsettledAmount,
            ]
        );

        $this->setForm($form);
        parent::_prepareForm();

        return $this;
    }
}
