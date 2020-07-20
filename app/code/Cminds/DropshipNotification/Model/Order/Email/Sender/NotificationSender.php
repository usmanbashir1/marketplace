<?php

namespace Cminds\DropshipNotification\Model\Order\Email\Sender;

use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\Sender;
use Cminds\DropshipNotification\Model\Config;
use Magento\Sales\Model\Order\Email\SenderBuilderFactory;
use Psr\Log\LoggerInterface;

/**
 * Cminds DropshipNotification dropship notification sender.
 *
 * @category Cminds
 * @package  Cminds_DropshipNotification
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class NotificationSender extends Sender
{
    /**
     * Application Event Dispatcher
     *
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var string
     */
    private $recipientName;

    /**
     * @var string
     */
    private $recipientEmail;

    /**
     * Module Config.
     *
     * @var Config
     */
    private $moduleConfig;

    /**
     * NotificationSender constructor.
     *
     * @param Template $templateContainer
     * @param OrderIdentity $identityContainer
     * @param SenderBuilderFactory $senderBuilderFactory
     * @param LoggerInterface $logger
     * @param Renderer $addressRenderer
     * @param ManagerInterface $eventManager
     * @param Config $moduleConfig
     */
    public function __construct(
        Template $templateContainer,
        OrderIdentity $identityContainer,
        SenderBuilderFactory $senderBuilderFactory,
        LoggerInterface $logger,
        Renderer $addressRenderer,
        ManagerInterface $eventManager,
        Config $moduleConfig
    ) {
        $this->eventManager = $eventManager;

        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer
        );

        $this->moduleConfig = $moduleConfig;
    }

    /**
     * @param Order $order
     * @param string $recipientName
     * @param string $recipientEmail
     *
     * @return bool
     */
    public function send(Order $order, $recipientName, $recipientEmail)
    {
        $this->recipientName = $recipientName;
        $this->recipientEmail = $recipientEmail;

        if ($this->checkAndSend($order)) {
            return true;
        }

        return false;
    }

    /**
     * @param Order $order
     * @return void
     */
    protected function prepareTemplate(Order $order)
    {
        $transport = [
            'order' => $order,
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'supplierName' => $this->recipientName,
        ];
        $transport = new \Magento\Framework\DataObject($transport);

        $this->eventManager->dispatch(
            'email_dropship_notification_set_template_vars_before',
            ['sender' => $this, 'transport' => $transport]
        );

        $this->templateContainer->setTemplateVars($transport->getData());
        $this->templateContainer->setTemplateOptions($this->getTemplateOptions());

        $this->identityContainer->setCustomerName($this->recipientName);
        $this->identityContainer->setCustomerEmail($this->recipientEmail);
        $this->templateContainer->setTemplateId($this->moduleConfig->getTemplateId());
    }
}
