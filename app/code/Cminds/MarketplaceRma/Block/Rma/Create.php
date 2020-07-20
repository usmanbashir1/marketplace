<?php

namespace Cminds\MarketplaceRma\Block\Rma;

use Cminds\MarketplaceRma\Model\ResourceModel\Type\CollectionFactory as RmaTypeFactory;
use Cminds\MarketplaceRma\Model\ResourceModel\Reason\CollectionFactory as RmaReasonFactory;
use Magento\Config\Model\Config\Source\YesnoFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/**
 * Class Create
 *
 * @package Cminds\MarketplaceRma\Block\Rma
 */
class Create extends Template
{
    /**
     * Block .phtml template path.
     * @var string
     */
    protected $_template = 'rma/create.phtml';

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var YesnoFactory
     */
    private $yesnoFactory;

    /**
     * @var RmaTypeFactory
     */
    private $rmaTypeFactory;

    /**
     * @var RmaReasonFactory
     */
    private $rmaReasonFactory;


    private $orderFactory;


    public function __construct(
        Template\Context $context,
        CustomerSession $customerSession,
        OrderCollectionFactory $orderCollectionFactory,
        YesnoFactory $yesnoFactory,
        RmaTypeFactory $rmaTypeFactory,
        RmaReasonFactory $rmaReasonFactory,
        OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->yesnoFactory = $yesnoFactory;
        $this->rmaTypeFactory = $rmaTypeFactory;
        $this->rmaReasonFactory = $rmaReasonFactory;
        $this->orderFactory = $orderFactory;

        parent::__construct($context, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->pageConfig->getTitle()->set(__('Create Returns Request'));
    }

    /**
     * Get orders belong to customer.
     *
     * @return \Magento\Framework\DataObject[]
     */
    public function getOrders()
    {
        $orders = $this->orderCollectionFactory
            ->create()
            ->addFieldToFilter('customer_id', $this->customerSession->getCustomerId())
            ->getItems();

        return $orders;
    }

    /**
     * Get options for 'Package Open' (Yes/No).
     *
     * @return array
     */
    public function getPackage()
    {
        return $this->yesnoFactory->create()->toOptionArray();
    }

    /**
     * Get available request types.
     *
     * @return array
     */
    public function getRequestType()
    {
        return $this->rmaTypeFactory->create()->toArray();
    }

    /**
     * Get available reasons.
     *
     * @return array
     */
    public function getReason()
    {
        return $this->rmaReasonFactory->create()->toArray();
    }

    /**
     * Get path to save controller for form.
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('marketplace-rma/rma/save/');
    }

    public function getFetchDataUrl()
    {
        return $this->getUrl('marketplace-rma/rma/data/');
    }

    /**
     * Get path to index controller.
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('marketplace-rma/rma/index/');
    }
}
