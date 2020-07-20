<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Tabs;

use Cminds\MarketplaceRma\Model\ResourceModel\ReturnProduct\CollectionFactory as ReturnProductCollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;

/**
 * Class Product
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Rma\Edit\Tabs
 */
class Product extends Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ReturnProductCollectionFactory
     */
    private $returnProductCollectionFactory;

    /**
     * Product constructor.
     *
     * @param Context                        $context
     * @param Data                           $backendHelper
     * @param ReturnProductCollectionFactory $returnProductCollectionFactory
     * @param Registry                       $registry
     * @param array                          $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        ReturnProductCollectionFactory $returnProductCollectionFactory,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);

        $this->returnProductCollectionFactory = $returnProductCollectionFactory;
        $this->registry = $registry;
    }

    /**
     * Prepare collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $params = $this->registry->registry('rma_data');

        if (isset($params['order_id'])) {
            $orderId = $params['order_id'];
        } else {
            $orderId = 0;
        }
        $collection = $this->returnProductCollectionFactory->create();
        $collection
            ->getSelect()
            ->joinLeft(
                ['product_entity' => $collection->getTable('catalog_product_entity')],
                'main_table.product_id = product_entity.entity_id',
                ['entity_id', 'sku']
            )
            ->where('order_id =?', $orderId);

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
     * @return Product|Extended
     *
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'product_id',
            [
                'header' => __('Product Id'),
                'width' => '50px',
                'index' => 'product_id',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'width' => '50px',
                'index' => 'sku',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'width' => '50px',
                'index' => 'sku',
            ]
        );
        $this->addColumn(
            'return_qty',
            [
                'header' => __('Qty'),
                'width' => '50px',
                'index' => 'return_qty'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __('Products');
    }

    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle()
    {
        return __('Products');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
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
     * @api
     */
    public function isHidden()
    {
        return false;
    }
}
