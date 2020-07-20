<?php

namespace Cminds\MarketplaceRma\Observer\Rma;

use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Helper\Email as EmailHelper;
use Cminds\MarketplaceRma\Model\Status;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

/**
 * Class RmaApprovalObserver
 *
 * @package Cminds\MarketplaceRma\Observer\Rma
 */
class RmaApprovalObserver implements ObserverInterface
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var EmailHelper
     */
    private $emailHelper;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var Order
     */
    private $order;

    /**
     * RmaApprovalObserver constructor.
     *
     * @param ModuleConfig $moduleConfig
     * @param EmailHelper  $emailHelper
     * @param Customer     $customer
     * @param Status       $status
     * @param Order        $order
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        EmailHelper $emailHelper,
        Customer $customer,
        Status $status,
        Order $order
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->emailHelper = $emailHelper;
        $this->customer = $customer;
        $this->status = $status;
        $this->order = $order;
    }

    /**
     * Execute method.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isActive() === true
            && $this->moduleConfig->getEmailCustomerAboutApproval() === true
        ) {
            $rmaModel = $observer->getData('rma_model');

            $currentCustomerId = $rmaModel->getData('customer_id');
            $currentCustomer = $this->customer->load($currentCustomerId);
            $currentOrderId = $rmaModel->getData('order_id');
            $currentOrderIncrementId = $this->order->load($currentOrderId)->getIncrementId();

            $data = [];
            $data['receiver_name'] = $currentCustomer->getName();
            $data['subject'] = __('Returns approved');
            $data['message'] = __(
                'Your Returns for order #' . $currentOrderIncrementId . ' has been approved.'
            );
            $data['receiver_email'] = $currentCustomer->getEmail();

            $this->emailHelper->sendMail($data);
        }
    }
}
