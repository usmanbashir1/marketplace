<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Tabs;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderFactory;

/**
 * Class Creditmemo
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Tabs
 */
class Creditmemo extends Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * Creditmemo constructor.
     *
     * @param Context      $context
     * @param Data         $backendHelper
     * @param OrderFactory $orderFactory
     * @param Registry     $registry
     * @param array        $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        OrderFactory $orderFactory,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);

        $this->registry = $registry;
        $this->orderFactory = $orderFactory;
    }

    /**
     * Prepare collection.
     *
     * @return DataObject
     */
    protected function _prepareCollection()
    {
        $params = $this->registry->registry('rma_data');

        if (isset($params['order_id'])) {
            $orderId = $params['order_id'];
        } else {
            $orderId = 0;
        }

        $order = $this->orderFactory->create()->load($orderId);
        $collection = $order->getCreditmemosCollection();

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('rma_products_list');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
    }

    /**
     * Prepare columns.
     *
     * @return Creditmemo|Extended
     *
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'increment_id',
            [
                'header' => __('Increment Id'),
                'width' => '50px',
                'index' => 'increment_id',
            ]
        );
        $this->addColumn(
            'state',
            [
                'header' => __('Status'),
                'width' => '50px',
                'index' => 'state',
                'type' => 'options',
                'options' => [
                    1 => __('Open'),
                    2 => __('Refunded'),
                    3 => __('Canceled')
                ]
            ]
        );
        $this->addColumn(
            'grand_total',
            [
                'header' => __('Refunded'),
                'width' => '50px',
                'index' => 'grand_total'
            ]
        );
        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'width' => '50px',
                'index' => 'created_at'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Return Tab label
     *
     * @return string
     *
     * @api
     */
    public function getTabLabel()
    {
        return __('Credit Memo');
    }

    /**
     * Return Tab title
     *
     * @return string
     *
     * @api
     */
    public function getTabTitle()
    {
        return __('Credit Memo');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     *
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     *
     * @api
     */
    public function isHidden()
    {
        return false;
    }
}
