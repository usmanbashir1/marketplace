<?php

namespace Cminds\MarketplaceRma\Observer\Rma;

use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Helper\Email as EmailHelper;
use Cminds\MarketplaceRma\Helper\Data as RmaHelper;
use Cminds\MarketplaceRma\Model\Status;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\OrderFactory;

/**
 * Class NewRmaObserver
 *
 * @package Cminds\MarketplaceRma\Observer\Rma
 */
class NewRmaObserver implements ObserverInterface
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
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var RmaHelper
     */
    private $rmaHelper;

    /**
     * NewRmaObserver constructor.
     *
     * @param ModuleConfig   $moduleConfig
     * @param EmailHelper    $emailHelper
     * @param Customer       $customer
     * @param Status         $status
     * @param OrderFactory   $orderFactory
     * @param ProductFactory $productFactory
     * @param RmaHelper      $rmaHelper
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        EmailHelper $emailHelper,
        Customer $customer,
        Status $status,
        OrderFactory $orderFactory,
        ProductFactory $productFactory,
        RmaHelper $rmaHelper
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->emailHelper = $emailHelper;
        $this->customer = $customer;
        $this->status = $status;
        $this->orderFactory = $orderFactory;
        $this->productFactory = $productFactory;
        $this->rmaHelper = $rmaHelper;
    }

    /**
     * Execute method.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isActive() === true
            && $this->moduleConfig->getEmailVendorAboutNewRma() === true
        ) {
            $rmaModel = $observer->getData('rma_model');
            $order = $this->orderFactory->create()->load($rmaModel->getData('order_id'));

            $suppliers = [];

            // get suppliers related to the order
            foreach ($order->getItems() as $item) {
                $product = $this->productFactory->create()->load($item->getProductId());
                if ($this->rmaHelper->isSupplier($product->getCreatorId())) {
                    $supplier = $this->customer->load($product->getCreatorId());
                        $suppliers[] = [
                            'id' => $product->getCreatorId(),
                            'email' => $supplier->getEmail(),
                            'firstname' => $supplier->getFirstname(),
                            'lastname' => $supplier->getLastname()
                        ];
                }
            }

            // prepare and send emails to the suppliers
            foreach ($suppliers as $supplier) {
                $data = [];
                $data['receiver_name'] = $supplier['firstname'] . ' ' . $supplier['lastname'];
                $data['subject'] = __('New Returns');
                $data['message'] = __(
                    'New Returns for order #' . $order->getIncrementId()
                );
                $data['receiver_email'] = $supplier['email'];

                $this->emailHelper->sendMail($data);
            }
        }
    }
}
