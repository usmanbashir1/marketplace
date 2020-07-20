<?php

namespace Cminds\MarketplaceRma\Block\SupplierRma;

use Cminds\MarketplaceRma\Helper\Data as RmaHelper;
use Cminds\MarketplaceRma\Model\ResourceModel\Status\CollectionFactory as RmaStatusFactory;
use Cminds\MarketplaceRma\Model\Rma as RmaModel;
use Cminds\MarketplaceRma\Model\ResourceModel\Note\CollectionFactory as NoteCollectionFactory;
use Cminds\MarketplaceRma\Model\ResourceModel\ReturnProduct\CollectionFactory as ReturnProductCollectionFactory;
use Cminds\MarketplaceRma\Model\ResourceModel\Rma\CollectionFactory as RmaCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as CreditmemoCollection;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

/**
 * Class View
 * @package Cminds\MarketplaceRma\Block\SupplierRma
 */
class View extends Template
{
    /**
     * @var CreditmemoCollection
     */
    private $creditmemoCollection;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var RmaModel
     */
    private $rma;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var RmaCollectionFactory
     */
    private $rmaCollectionFactory;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var RmaStatusFactory
     */
    private $rmaStatus;

    /**
     * @var NoteCollectionFactory
     */
    private $noteCollectionFactory;

    /**
     * @var ReturnProductCollectionFactory
     */
    private $returnProductCollectionFactory;

    /**
     * @var RmaHelper
     */
    private $rmaHelper;

    /**
     * View constructor.
     *
     * @param Context                        $context
     * @param RmaCollectionFactory           $rmaCollectionFactory
     * @param CreditmemoCollection           $creditmemoCollection
     * @param RmaModel                       $rma
     * @param Registry                       $registry
     * @param OrderFactory                   $orderFactory
     * @param RmaStatusFactory               $rmaStatus
     * @param Session                        $customerSession
     * @param NoteCollectionFactory          $noteCollectionFactory
     * @param ReturnProductCollectionFactory $returnProductCollectionFactory
     * @param RmaHelper                      $rmaHelper
     */
    public function __construct(
        Context $context,
        RmaCollectionFactory $rmaCollectionFactory,
        CreditmemoCollection $creditmemoCollection,
        RmaModel $rma,
        Registry $registry,
        OrderFactory $orderFactory,
        RmaStatusFactory $rmaStatus,
        Session $customerSession,
        NoteCollectionFactory $noteCollectionFactory,
        ReturnProductCollectionFactory $returnProductCollectionFactory,
        RmaHelper $rmaHelper
    ) {
        parent::__construct($context);

        $this->creditmemoCollection = $creditmemoCollection;
        $this->rma = $rma;
        $this->rmaStatus = $rmaStatus;
        $this->registry = $registry;
        $this->orderFactory = $orderFactory;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
        $this->customerSession = $customerSession;
        $this->noteCollectionFactory = $noteCollectionFactory;
        $this->returnProductCollectionFactory = $returnProductCollectionFactory;
        $this->rmaHelper = $rmaHelper;
    }

    /**
     * Get rma.
     *
     * @return bool|\Magento\Framework\DataObject|mixed
     */
    public function getRma()
    {
        if (!($customerId = $this->getCustomerSession()->getCustomerId())) {
            return false;
        }

        $rmaId = $this->getId();

        if (isset($rmaId)) {
            $rma = $this->rmaCollectionFactory->create();
            $rma->getSelect()
                ->joinLeft(
                    ['o' => 'sales_order'],
                    'o.entity_id = main_table.order_id',
                    [
                        'status',
                        'state',
                        'increment_id',
                        'created_at as sales_created',
                        'customer_firstname',
                        'customer_lastname',
                        'customer_email',
                        'status as order_status',
                        'created_at as order_date'
                    ]
                )
                ->joinLeft(
                    ['rma_status' => 'cminds_marketplace_rma_status'],
                    'main_table.status = rma_status.id',
                    [
                        'name as status_name',
                        'id as status_id'
                    ]
                )
                ->joinLeft(
                    ['rma_reason' => 'cminds_marketplace_rma_reason'],
                    'main_table.reason = rma_reason.id',
                    ['name as reason_name']
                )
                ->joinLeft(
                    ['rma_type' => 'cminds_marketplace_rma_type'],
                    'main_table.request_type = rma_type.id',
                    ['name as type_name']
                )
                ->where('main_table.id = ?', $rmaId);
                
            return $rma->getFirstItem();
        } 
        
        return $rmaId;
    }

    /**
     * Get customer session.
     *
     * @return Session
     */
    public function getCustomerSession()
    {
        return $this->customerSession;
    }

    /**
     * Get rma id.
     *
     * @return bool|mixed
     */
    public function getId()
    {
        $rmaId = $this->registry->registry('rma_view_id');

        if (isset($rmaId)) {
            return $rmaId;
        } else {
            return false;
        }
    }

    /**
     * Get items.
     *
     * @param $orderId
     *
     * @return mixed
     */
    public function getItems($orderId)
    {
        return $this->orderFactory->create()->load($orderId);
    }

    /**
     * Get rma status.
     *
     * @return array
     */
    public function getRmaStatus()
    {
        return $this->rmaStatus->create()->toArray();
    }

    /**
     * Get credit memo.
     *
     * @param $orderId
     *
     * @return View|\Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection
     */
    public function getCreditMemo($orderId)
    {
        return $this->creditmemoCollection->create()->getFiltered(['order_id' => $orderId]);
    }

    /**
     * Get Notes
     *
     * @param $rmaId
     *
     * @return \Cminds\MarketplaceRma\Model\ResourceModel\Note\Collection
     */
    public function getNotes($rmaId)
    {
        $collection = $this->noteCollectionFactory
            ->create()
            ->addFieldToFilter('rma_id', $rmaId);

        return $collection;
    }

    /**
     * Get return products.
     *
     * @param $orderId
     *
     * @return array
     */
    public function getReturnProducts($orderId)
    {
        $order = $this->orderFactory->create()->load($orderId);

        $orderReturnProducts = $this->returnProductCollectionFactory
            ->create()
            ->addFieldToFilter('order_id', $orderId)
            ->getItems();

        $returnOrderItems = $this->rmaHelper->mapRmaProductIdsToOrderItemIds($orderReturnProducts, $order);

        return $returnOrderItems;
    }
}
