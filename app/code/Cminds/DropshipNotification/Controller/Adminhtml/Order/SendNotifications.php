<?php

namespace Cminds\DropshipNotification\Controller\Adminhtml\Order;

use Cminds\DropshipNotification\Model\Config as ModuleConfig;
use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\Adminhtml\Order;
use Psr\Log\LoggerInterface;
use Cminds\DropshipNotification\Model\Handler;

/**
 * Cminds DropshipNotification admin order view send notifications controller.
 *
 * @category Cminds
 * @package  Cminds_DropshipNotification
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class SendNotifications extends Order
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var Handler
     */
    private $dropShipHandler;

    /**
     * @param Action\Context                                   $context
     * @param \Magento\Framework\Registry                      $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Translate\InlineInterface     $translateInline
     * @param \Magento\Framework\View\Result\PageFactory       $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory     $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory  $resultRawFactory
     * @param OrderManagementInterface                         $orderManagement
     * @param OrderRepositoryInterface                         $orderRepository
     * @param LoggerInterface                                  $logger
     * @param ModuleConfig                                     $moduleConfig
     * @param Handler                                          $dropShipHandler
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        ModuleConfig $moduleConfig,
        Handler $dropShipHandler
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->dropShipHandler = $dropShipHandler;
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $orderManagement,
            $orderRepository,
            $logger
        );
    }

    /**
     * Process drop ship for current order
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Exception
     */
    public function execute()
    {
        $this->_initOrder();

        $orderId = $this->getRequest()->getParam('order_id');

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath(
            'sales/order/view',
            ['order_id' => $orderId]
        );

        if ($this->moduleConfig->isEnabled() === false) {
            return $resultRedirect;
        }

        $this->dropShipHandler->process($orderId);

        return $resultRedirect;
    }
}
