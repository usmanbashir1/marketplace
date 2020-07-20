<?php

namespace Cminds\DropshipNotification\Block\Adminhtml\Order\View\Tab\Dropship;

use Cminds\DropshipNotification\Model\Config as ModuleConfig;
use Cminds\DropshipNotification\Model\ResourceModel\Order\Item\CollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as DataHelper;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Cminds DropshipNotification admin order view dropship pending grid block.
 *
 * @category Cminds
 * @package  Cminds_DropshipNotification
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Pending extends Grid
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Pending constructor.
     *
     * @param Context           $context
     * @param DataHelper        $backendHelper
     * @param Registry          $coreRegistry
     * @param CollectionFactory $collectionFactory
     * @param array             $data
     */
    public function __construct(
        Context $context,
        DataHelper $backendHelper,
        Registry $coreRegistry,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;

        parent::__construct(
            $context,
            $backendHelper,
            $data
        );
    }

    /**
     * @return OrderInterface|null
     */
    private function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $order = $this->getOrder();

        $collection = $this->collectionFactory->create();
        $collection
            ->filterByOrderId($order->getId())
            ->filterByStatus(ModuleConfig::STATUS_INCOMPLETE);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Grid
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
            ]
        );
        $this->addColumn(
            'qty',
            [
                'header' => __('Qty'),
                'index' => 'qty_ordered',
            ]
        );
        $this->addColumn(
            'supplier_name',
            [
                'header' => __('Supplier Name'),
                'index' => 'supplier_name',
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'dropship_notification_flag',
                'renderer' => 'Cminds\DropshipNotification\Ui\Component'
                    . '\Column\Renderer\DropshipNotificationFlag',
            ]
        );
        $this->addColumn(
            'method',
            [
                'header' => __('Method'),
                'index' => 'notification_method',
            ]
        );
        $this->addColumn(
            'date',
            [
                'header' => __('Date'),
                'index' => 'dropship_notification_date',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getGridTitle()
    {
        return __('Pending Order Items');
    }
}
