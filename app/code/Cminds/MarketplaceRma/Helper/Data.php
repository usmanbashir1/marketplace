<?php

namespace Cminds\MarketplaceRma\Helper;

use Cminds\MarketplaceRma\Model\ResourceModel\ReturnProduct\Collection as ReturnProductCollection;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Exception\CouldNotRefundException;
use Magento\Sales\Exception\DocumentValidationException;
use Magento\Sales\Model\Order\Creditmemo\ItemCreationFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\RefundOrder;
use Magento\Sales\Model\Service\CreditmemoService;

/**
 * Class Data
 *
 * @package Cminds\MarketplaceRma\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var CreditmemoFactory
     */
    private $creditmemoFactory;

    /**
     * @var Invoice
     */
    private $invoice;

    /**
     * @var CreditmemoService
     */
    private $creditmemoService;

    /**
     * @var ItemCreationFactory
     */
    private $itemCreationFactory;

    /**
     * @var RefundOrder
     */
    private $refundOrder;

    /**
     * @var ReturnProductCollection
     */
    private $returnProductCollection;

    /**
     * Data constructor.
     *
     * @param Context                 $context
     * @param Session                 $session
     * @param OrderFactory            $orderFactory
     * @param CreditmemoFactory       $creditmemoFactory
     * @param Invoice                 $invoice
     * @param CreditmemoService       $creditmemoService
     * @param CustomerFactory         $customerFactory
     * @param RefundOrder             $refundOrder
     * @param ItemCreationFactory     $itemCreationFactory
     * @param ReturnProductCollection $returnProductCollection
     */
    public function __construct(
        Context $context,
        Session $session,
        OrderFactory $orderFactory,
        CreditmemoFactory $creditmemoFactory,
        Invoice $invoice,
        CreditmemoService $creditmemoService,
        CustomerFactory $customerFactory,
        RefundOrder $refundOrder,
        ItemCreationFactory $itemCreationFactory,
        ReturnProductCollection $returnProductCollection
    ) {
        $this->orderFactory = $orderFactory;
        $this->invoice = $invoice;
        $this->creditmemoService = $creditmemoService;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->customerSession = $session;
        $this->scopeConfig = $context->getScopeConfig();
        $this->customerFactory = $customerFactory;

        $this->refundOrder = $refundOrder;
        $this->itemCreationFactory = $itemCreationFactory;
        $this->returnProductCollection = $returnProductCollection;

        parent::__construct($context);
    }

    /**
     * Check is currently logged in customer is supplier.
     *
     * @param null|$userId
     *
     * @return bool
     */
    public function isSupplier($userId = null)
    {
        if ($userId === null) {
            $customerId = $this->customerSession->getCustomerId();
        } else {
            $customerId = $userId;
        }

        $customerGroupConfig = $this->scopeConfig->getValue(
            'configuration/suppliers_group/supplier_group'
        );
        $editorGroupConfig = $this->scopeConfig->getValue(
            'configuration/'
            . 'suppliers_group/'
            . 'suppliert_group_which_can_edit_own_products'
        );

        $allowedGroups = [];

        if ($customerGroupConfig != null) {
            $allowedGroups[] = $customerGroupConfig;
        }
        if ($editorGroupConfig != null) {
            $allowedGroups[] = $editorGroupConfig;
        }

        $customer = $this->customerFactory->create()->load($customerId);
        $groupId = $customer->getGroupId();

        return in_array($groupId, $allowedGroups);
    }

    /**
     * Proceed refund.
     *
     * @param $orderId
     *
     * @return bool
     */
    public function proceedRefund($orderId){
        $orderReturnProducts = $this->returnProductCollection
            ->addFieldToFilter('order_id', $orderId)
            ->getItems();

        $order = $this->orderFactory->create();
        $order->load($orderId);

        $returnOrderItems = $this->mapRmaProductIdsToOrderItemIds($orderReturnProducts, $order);
        $itemIdsToRefund = [];

        foreach ($returnOrderItems as $orderItemId => $item) {
            $creditmemoItem = $this->itemCreationFactory->create();
            $returnQty = $item['return']->getData('return_qty');
            $creditmemoItem->setQty($returnQty)->setOrderItemId($orderItemId);
            $itemIdsToRefund[] = $creditmemoItem;
        }

        try {
            $this->refundOrder->execute(
                $orderId,
                $itemIdsToRefund
            );
        } catch (CouldNotRefundException $e) {
            return false;
        } catch (DocumentValidationException $e) {
            return false;
        }

        return true;
    }

    /**
     * Check is order invoiced.
     *
     * @param $orderId
     *
     * @return bool
     */
    public function checkIsOrderInvoiced($orderId)
    {
        $order = $this->orderFactory->create()->load($orderId);
        $isOrderInvoiced = $order->hasInvoices();

        return (bool)$isOrderInvoiced;
    }

    /**
     * Check is order ready to make credit memo.
     *
     * @param $orderId
     *
     * @return bool
     */
    public function checkIsOrderReadyForCreditmemo($orderId)
    {
        $order = $this->orderFactory->create()->load($orderId);
        $isOrderReadyForCreditmemo = $order->hasInvoices();

        return (bool)$isOrderReadyForCreditmemo;
    }

    /**
     * Check is order already has credit memo.
     *
     * @param $orderId
     *
     * @return bool
     */
    public function checkIsOrderHasCreditmemos($orderId)
    {
        $order = $this->orderFactory->create()->load($orderId);
        $isOrderHasCreditmemos = $order->hasCreditmemos();

        return (bool)$isOrderHasCreditmemos;
    }

    /**
     * Map product ids to order item ids.
     *
     * @param $orderReturnProducts
     * @param $order
     *
     * @return array
     */
    public function mapRmaProductIdsToOrderItemIds($orderReturnProducts, $order)
    {
        $mappedArray = [];
        $orderItems = $order->getAllVisibleItems();

        foreach ($orderItems as $orderItem) {
            $orderItemId = $orderItem->getId();
            $orderItemProductId = $orderItem->getProductId();

            foreach ($orderReturnProducts as $orderReturnProduct) {
                $orderReturnProductId = $orderReturnProduct->getData('product_id');

                if ($orderItemProductId == $orderReturnProductId) {
                    $mappedArray[$orderItemId] = [
                        'return' => $orderReturnProduct,
                        'original' => $orderItem
                    ];
                }
            }
        }

        return $mappedArray;
    }
}
