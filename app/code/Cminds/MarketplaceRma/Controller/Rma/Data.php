<?php

namespace Cminds\MarketplaceRma\Controller\Rma;

use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Model\ResourceModel\Rma\CollectionFactory as RmaCollectionFactory;
use Cminds\MarketplaceRma\Model\ResourceModel\ReturnProduct\CollectionFactory as ReturnProductCollectionFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\OrderFactory;

/**
 * Class Data
 *
 * @package Cminds\MarketplaceRma\Controller\Rma
 */
class Data extends AbstractController
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var RmaCollectionFactory
     */
    private $rmaCollectionFactory;

    /**
     * @var ReturnProductCollectionFactory
     */
    private $returnProductCollectionFactory;
    
    /**
     * Data constructor.
     *
     * @param Context               $context
     * @param ModuleConfig          $moduleConfig
     * @param CustomerSession       $customerSession
     * @param JsonFactory           $resultJsonFactory
     * @param OrderFactory          $orderFactory
     * @param RmaCollectionFactory  $rmaCollectionFactory
     * @param ReturnProductCollectionFactory $returnProductCollectionFactory
     */
    public function  __construct(
        Context $context,
        ModuleConfig $moduleConfig,
        CustomerSession $customerSession,
        JsonFactory $resultJsonFactory,
        OrderFactory $orderFactory,
        RmaCollectionFactory $rmaCollectionFactory,
        ReturnProductCollectionFactory $returnProductCollectionFactory
    ) {
        parent::__construct($context, $moduleConfig);

        $this->customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->orderFactory = $orderFactory;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
        $this->returnProductCollectionFactory = $returnProductCollectionFactory;
    }

    /**
     * Return all invoice items requested by AJAX.
     *
     * @return $this|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $isAjax = $this->getRequest()->isAjax();

        if ($isAjax === true) {
            $orderId = $this->getRequest()->getParam('order_id');
            $invoices = $this->getOrderInvoices($orderId);

            return $result->setData($invoices);
        }
    }

    /**
     * Get order related invoices.
     *
     * @param $orderId
     *
     * @return array
     */
    private function getOrderInvoices($orderId)
    {
        $order = $this->orderFactory->create()->load($orderId);

        $rmaCollection = $this->rmaCollectionFactory
            ->create()
            ->addFieldToFilter('order_id', $orderId);
        
        if ($order->hasInvoices() && $rmaCollection->count() == 0) {
            $invoices = $order->getInvoiceCollection();

            $invoiceData = [];

            foreach ($invoices as $invoice) {
                $invoiceItems = [];
                foreach ($invoice->getItems() as $item) {
                    if ($item->getPrice() != 0) {
                        $invoiceItems[] = [
                            'product_id' => $item->getProductId(),
                            'product_name' => $item->getName(),
                            'product_sku' => $item->getSku(),
                            'product_price' => round($item->getPrice(), 2),
                            'product_qty' => (int)$item->getQty()
                        ];
                    }
                }
                $invoiceData [] = [
                    'id' => $invoice->getId(),
                    'increment_id' => $invoice->getIncrementId(),
                    'items' => $invoiceItems
                ];
            }

            return $invoiceData;
        } elseif ($order->hasInvoices() && $rmaCollection->count() > 0) {
            $returnProductCollection = $this->returnProductCollectionFactory
                                            ->create()
                                            ->addFieldToFilter('order_id', $orderId);
            $returnProducts = [];            
            foreach ($returnProductCollection as $returnProduct){
                $returnProducts[$returnProduct->getProductId()] = $returnProduct->getReturnQty();
            }
            
            $invoices = $order->getInvoiceCollection();
            $invoiceData = [];
            
            foreach ($invoices as $invoice) {
                $invoiceItems = [];
                foreach ($invoice->getItems() as $item) {
                    $canReturnProductQty = $item->getQty();
                    if(in_array($item->getProductId(), array_keys($returnProducts))){
                        $canReturnProductQty = $canReturnProductQty - $returnProducts[$item->getProductId()];
                    }
                    
                    if ($item->getPrice() != 0 && $canReturnProductQty > 0) {
                        $invoiceItems[] = [
                            'product_id' => $item->getProductId(),
                            'product_name' => $item->getName(),
                            'product_sku' => $item->getSku(),
                            'product_price' => round($item->getPrice(), 2),
                            'product_qty' => (int)$canReturnProductQty
                        ];
                    }
                }
                $invoiceData [] = [
                    'id' => $invoice->getId(),
                    'increment_id' => $invoice->getIncrementId(),
                    'items' => $invoiceItems
                ];
            }
            
            return $invoiceData;
        }

        return [];
    }
}
