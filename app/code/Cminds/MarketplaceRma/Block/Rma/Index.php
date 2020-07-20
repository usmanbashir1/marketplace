<?php

namespace Cminds\MarketplaceRma\Block\Rma;

use Magento\Framework\View\Element\Template;
use Cminds\MarketplaceRma\Model\ResourceModel\Rma\CollectionFactory as RmaCollectionFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Index
 *
 * @package Cminds\MarketplaceRma\Block\Rma
 */
class Index extends Template
{
    /**
     * Block template path.
     *
     * @var string
     */
    protected $_template = 'rma/index.phtml';

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var RmaCollectionFactory
     */
    private $rmaCollectionFactory;

    /**
     * Index constructor.
     *
     * @param Context              $context
     * @param CollectionFactory    $orderCollectionFactory
     * @param CustomerSession      $customerSession
     * @param RmaCollectionFactory $rmaCollectionFactory
     * @param array                $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $orderCollectionFactory,
        CustomerSession $customerSession,
        RmaCollectionFactory $rmaCollectionFactory,
        array $data = []
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerSession = $customerSession;
        $this->rmaCollectionFactory = $rmaCollectionFactory;

        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->pageConfig->getTitle()->set(__('My Returns Requests'));
    }

    /**
     * Get all rma belongs to current user.
     *
     * @return bool|Index|\Cminds\MarketplaceRma\Model\ResourceModel\Rma\Collection
     */
    public function getRmaOrders()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }

        $rmaOrders =  $this->rmaCollectionFactory
            ->create()
            ->addFieldToSelect('*');

        $rmaOrders
            ->getSelect()
            ->joinLeft(
                ['rs' => 'cminds_marketplace_rma_status'],
                'rs.id = main_table.status',
                ['name as status_name']
            )
            ->joinLeft(
                ['order' => 'sales_order'],
                'order.entity_id = main_table.order_id',
                ['increment_id as increment_order_id']
            )
            ->where('main_table.customer_id = ?', $customerId)
            ->order('main_table.id DESC');

        return $rmaOrders;
    }

    /**
     * Prepare layout.
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->getRma()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'cminds.rma.index.pager'
            )->setCollection(
                $this->getRma()
            );
            $this->setChild('pager', $pager);
            $this->getRma()->load();
        }
        
        return $this;
    }

    /**
     * Get pager html.
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get view url.
     *
     * @param $rmaOrder
     *
     * @return string
     */
    public function getViewUrl($rmaOrder)
    {
        return $this->getUrl('marketplace-rma/rma/view/', ['id' => $rmaOrder->getId()]);
    }

    /**
     * Get create url.
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('marketplace-rma/rma/create/');
    }

    /**
     * Get back url.
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
