<?php

namespace Cminds\MarketplaceRma\Observer\Status;

use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Helper\Email as EmailHelper;
use Cminds\MarketplaceRma\Model\Status;
use Cminds\MarketplaceRma\Model\Rma;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Manager as EventManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

/**
 * Class StatusChangedObserver
 *
 * @package Cminds\MarketplaceRma\Observer\Status
 */
class StatusChangedObserver implements ObserverInterface
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
     * @var EventManager
     */
    private $eventManager;

    /**
     * StatusChangedObserver constructor.
     *
     * @param ModuleConfig $moduleConfig
     * @param EmailHelper  $emailHelper
     * @param Customer     $customer
     * @param Status       $status
     * @param Order        $order
     * @param EventManager $eventManager
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        EmailHelper $emailHelper,
        Customer $customer,
        Status $status,
        Order $order,
        EventManager $eventManager
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->emailHelper = $emailHelper;
        $this->customer = $customer;
        $this->status = $status;
        $this->order = $order;
        $this->eventManager = $eventManager;
    }

    /**
     * Execute method.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isActive() === true
            && $this->moduleConfig->getEmailCustomerAboutStatusChange() === true
        ) {
            $rmaModel = $observer->getData('rma_model');

            $currentCustomerId = $rmaModel->getData('customer_id');
            $currentCustomer = $this->customer->load($currentCustomerId);
            $currentStatusId = $rmaModel->getData('status');
            $currentStatus = $this->status->load($currentStatusId)->getData('name');
            $currentOrderId = $rmaModel->getData('order_id');
            $currentOrderIncrementId = $this->order->load($currentOrderId)->getIncrementId();

            $data = [];
            $data['receiver_name'] = $currentCustomer->getName();
            $data['subject'] = __('Returns status changed.');
            $data['message'] = __(
                'Returns status for order #' . $currentOrderIncrementId
                . ' has been changed. New status is: ' . $currentStatus
            );
            $data['receiver_email'] = $currentCustomer->getEmail();

            $this->emailHelper->sendMail($data);
        }
    }
}
