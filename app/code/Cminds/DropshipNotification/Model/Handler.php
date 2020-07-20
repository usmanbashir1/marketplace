<?php
namespace Cminds\DropshipNotification\Model;

use Cminds\DropshipNotification\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Cminds\DropshipNotification\Model\Config as ModuleConfig;
use Cminds\DropshipNotification\Model\Order\Email\Sender\NotificationSender;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\Message\ManagerInterface as MessagesManager;

class Handler
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var NotificationSender
     */
    private $notificationSender;

    /**
     * @var OrderItemCollectionFactory
     */
    private $orderItemCollectionFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var MessagesManager
     */
    protected $messageManager;


    public function __construct(
        ModuleConfig $moduleConfig,
        NotificationSender $notificationSender,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        Registry $registry,
        OrderFactory $orderFactory,
        CollectionFactory $collectionFactory,
        MessagesManager $messageManager
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->notificationSender = $notificationSender;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->registry = $registry;
        $this->orderFactory = $orderFactory;
        $this->collectionFactory = $collectionFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * Check if order is applicable for dropship and send mail, change status
     *
     * @param int $orderId
     * @throws LocalizedException
     */
    public function process($orderId)
    {
        $collection = $this->orderItemCollectionFactory->create();
        $collection
            ->filterByOrderId($orderId)
            ->filterByStatus(ModuleConfig::STATUS_INCOMPLETE);

        if ($this->moduleConfig->shouldSendEmailNotification()) {
            $itemsBySupplier = [];
            foreach ($collection as $item) {
                $supplierId = $item->getSupplierId();
                if (empty($supplierId)) {
                    continue;
                }

                $itemsBySupplier[$supplierId][$item->getProductId()] = $item;
            }

            $order = $this->registry->registry('current_order') ?? $this->orderFactory->create()->load($orderId);

            foreach ($itemsBySupplier as $supplierId => $items) {
                $recipientName = current($items)->getSupplierName();
                $recipientEmail = current($items)->getSupplierEmail();

                $order->setItems($items);

                if ($this->notificationSender->send($order, $recipientName, $recipientEmail) === false) {
                    throw new LocalizedException(__('Error occurred during emails sending.'));
                }
            }

            $this->messageManager->addSuccessMessage(__('Dropship notifications have been sent.'));
        }

        if ($this->moduleConfig->shouldChangeOrderStatus()) {
            $newOrderStatus = $this->moduleConfig->geNewOrderStatus();

            $order = $this->orderFactory->create()->load($orderId);
            $order
                ->setState($this->getStateForStatus($newOrderStatus))
                ->setStatus($newOrderStatus)
                ->save();

            foreach ($collection as $item) {
                $item
                    ->setDropshipNotificationFlag(ModuleConfig::STATUS_COMPLETED)
                    ->setDropshipNotificationDate(date('Y-m-d H:i:s'))
                    ->save();
            }

            $this->messageManager->addSuccessMessage(__('Order status has been updated.'));
        }
    }

    /**
     * Get state for status
     *
     * @param string $status
     * @return string
     */
    private function getStateForStatus($status)
    {
        $collection = $this->collectionFactory->create();
        $collection
            ->getSelect()
            ->joinLeft(
                ['state_table' => $collection->getTable('sales_order_status_state')],
                'main_table.status = state_table.status',
                ['state']
            )
            ->where('main_table.status = ?', $status);
        $status = $collection->getFirstItem();

        return $status->getState();
    }
}