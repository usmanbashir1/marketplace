<?php

namespace Cminds\MarketplaceRma\Block\Rma;

use Cminds\MarketplaceRma\Model\ResourceModel\Type\CollectionFactory as RmaTypeFactory;
use Cminds\MarketplaceRma\Model\ResourceModel\Reason\CollectionFactory as RmaReasonFactory;
use Cminds\MarketplaceRma\Model\ResourceModel\Rma\CollectionFactory as RmaCollectionFactory;
use Cminds\MarketplaceRma\Model\ResourceModel\Note\CollectionFactory as NoteCollectionFactory;
use Cminds\MarketplaceRma\Model\Rma as RmaModel;
use Cminds\MarketplaceRma\Helper\Data as RmaHelper;
use Cminds\MarketplaceRma\Model\ResourceModel\ReturnProduct\Collection as ReturnProductCollection;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderFactory;
use Magento\Config\Model\Config\Source\YesnoFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;

/**
 * Class View
 *
 * @package Cminds\MarketplaceRma\Block\Rma
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'rma/view.phtml';

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var RmaModel
     */
    private $rma;

    /**
     * @var RmaTypeFactory
     */
    private $rmaType;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var YesnoFactory
     */
    private $yesnoFactory;

    /**
     * @var RmaReasonFactory
     */
    private $rmaReason;

    /**
     * @var RmaCollectionFactory
     */
    private $rmaCollectionFactory;

    /**
     * @var ReturnProductCollection
     */
    private $returnProductCollection;

    /**
     * @var RmaHelper
     */
    private $rmaHelper;

    /**
     * @var NoteCollectionFactory
     */
    private $noteCollectionFactory;

    /**
     * View constructor.
     *
     * @param Context                 $context
     * @param Session                 $customerSession
     * @param RmaTypeFactory          $rmaType
     * @param OrderFactory            $orderFactory
     * @param RmaCollectionFactory    $rmaCollectionFactory
     * @param RmaModel                $rma
     * @param RmaReasonFactory        $rmaReason
     * @param Registry                $registry
     * @param YesnoFactory            $yesnoFactory
     * @param ReturnProductCollection $returnProductCollection
     * @param RmaHelper               $rmaHelper
     * @param array                   $data
     * @param NoteCollectionFactory   $noteCollectionFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        RmaTypeFactory $rmaType,
        OrderFactory $orderFactory,
        RmaCollectionFactory $rmaCollectionFactory,
        RmaModel $rma,
        RmaReasonFactory $rmaReason,
        Registry $registry,
        YesnoFactory $yesnoFactory,
        ReturnProductCollection $returnProductCollection,
        RmaHelper $rmaHelper,
        NoteCollectionFactory $noteCollectionFactory,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->orderFactory = $orderFactory;
        $this->rma = $rma;
        $this->rmaType = $rmaType;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
        $this->rmaReason = $rmaReason;
        $this->registry = $registry;
        $this->yesnoFactory = $yesnoFactory;
        $this->returnProductCollection = $returnProductCollection;
        $this->rmaHelper = $rmaHelper;
        $this->noteCollectionFactory = $noteCollectionFactory;

        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->pageConfig->getTitle()->set(__('View Returns Requests'));
    }

    /**
     * Get package options.
     *
     * @return array
     */
    public function getPackage()
    {
        return $this->yesnoFactory->create()->toOptionArray();
    }

    /**
     * Get request types.
     *
     * @return array
     */
    public function getRequestType()
    {
        return $this->rmaType->create()->toArray();
    }

    /**
     * Get resons.
     *
     * @return array
     */
    public function getReason()
    {
        return $this->rmaReason->create()->toArray();
    }

    /**
     * Get current rma id.
     *
     * @return bool|mixed
     */
    public function getId(){
        $rmaId = $this->registry->registry('rma_edit_id');

        if (isset($rmaId)) {

            return $rmaId;
        } else {

            return false;
        }
    }

    /**
     * Get customer session.
     *
     * @return Session
     */
    public function getCustomerSession(){
        return $this->customerSession;
    }

    /**
     * Get Rma instance.
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
                    ['name as status_name']
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
     * Get back url.
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('marketplace-rma/rma/index/');
    }

    /**
     * Get return items collection.
     * Also contains original order items, but it is limited to return items.
     *
     * @param $orderId
     *
     * @return array
     */
    public function getReturnItems($orderId)
    {
        $order = $this->orderFactory->create()->load($orderId);

        $orderReturnProducts = $this->returnProductCollection
            ->addFieldToFilter('order_id', $orderId)
            ->getItems();

        $returnOrderItems = $this->rmaHelper->mapRmaProductIdsToOrderItemIds($orderReturnProducts, $order);

        return $returnOrderItems;
    }

    /**
     * Get notes.
     *
     * @param $rmaId
     *
     * @return \Magento\Framework\DataObject[]
     */
    public function getNotes($rmaId)
    {
        $notes = $this->noteCollectionFactory
            ->create()
            ->addFieldToFilter('rma_id', $rmaId)
            ->getItems();

        return $notes;
    }
}
