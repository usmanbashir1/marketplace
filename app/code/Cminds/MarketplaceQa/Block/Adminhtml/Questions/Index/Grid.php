<?php

namespace Cminds\MarketplaceQa\Block\Adminhtml\Questions\Index;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Customer\Model\ResourceModel\Customer\Collection as Customer;
use Magento\Directory\Model\Currency;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Sales\Model\Order\Item;
use Cminds\MarketplaceQa\Model\ResourceModel\Qa\CollectionFactory as Qa;

class Grid extends Extended
{
    protected $_productFactory;
    protected $_queueFactory;
    protected $_customer;
    protected $_attributeFactory;
    protected $_coreResource;
    protected $_item;
    protected $_orderConfig;
    protected $qa;

    public function __construct(
        Context $context,
        Data $backendHelper,
        Customer $customer,
        AttributeFactory $attributeFactory,
        ResourceConnection $resourceConnection,
        Item $item,
        OrderConfig $config,
        Qa $qa,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendHelper,
            $data
        );

        $this->_customer = $customer;
        $this->_attributeFactory = $attributeFactory;
        $this->_coreResource = $resourceConnection;
        $this->_item = $item;
        $this->_orderConfig = $config;
        $this->qa = $qa;
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('supplier_list');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Retrieve current request store object.
     *
     * @return StoreInterface
     */
    private function getStore()
    {
        $storeId = (int)$this->_request->getParam('store', 0);

        return $this->_storeManager->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->getStore();
        $collection = $this->qa->create()
                ->join(
                    ['ce' => 'customer_entity'],
                    'main_table.supplier_id = ce.entity_id',
                    [
                        'email AS supplier_email',
                        'lastname AS supplier_lastname',
                        'firstname AS supplier_firstname',
                    ]
                );
         
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this->getCollection();
    }

    /**
     * Add column filtering conditions to collection.
     *
     * @param Column $column Column object.
     *
     * @return Extended
     */
    protected function _addColumnFilterToCollection($column)
    {
         
        return parent::_addColumnFilterToCollection($column);
    }

    /**
     * Prepare grid columns.
     *
     * @return Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
                'id',
                [
                    'header' => __('ID'),
                    'type' => 'number',
                    'index' => 'id',
                    'header_css_class' => 'col-id',
                    'column_css_class' => 'col-id',
                ]
            )
            ->addColumn(
                'supplier_id',
                [
                    'header' => __('Vendor Name'),
                    'width' => '50px',
                    'index' => 'supplier_id',
                    'renderer' => '\Cminds\Marketplace\Block\Adminhtml'
                            . '\Billing\Billinglist\Grid\Renderer\Supplier',
                ]
            )
            ->addColumn(
                'customer_name',
                [
                    'header' => __('Customer Name'),
                    'index' => 'customer_name',
                ]
            )
            ->addColumn(
                'question',
                [
                    'header' => __('Question'),
                    'index' => 'question',
                ]
            )
            ->addColumn(
                'answer',
                [
                    'header' => __('Answer'),
                    'index' => 'answer',
                ]
            )
            ->addColumn(
                'visible_on_frontend',
                [
                    'header' => __('Visible on Frontend'),
                    'index' => 'visible_on_frontend',
                    'renderer' => '\Cminds\MarketplaceQa\Block\Adminhtml'
                            . '\Questions\Index\Grid\Renderer\Visible',
                ]
            );

        $this->addColumn(
            'approved',
            [
                'header' => __('Approved'),
                'filter' => false,
                'sortable' => false,
                'index' => 'approved',
                'type' => 'action',
                'renderer' => 'Cminds\MarketplaceQa\Block'
                    . '\Adminhtml\Questions\Index\Grid\Renderer\Approved',
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at',
            ]
        );

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => 'marketplaceqa/questions/edit'
                        ],
                        'field' => 'id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * Retrieve grid url.
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'marketplaceqa/questions/index',
            ['_current' => true]
        );
    }

    /**
     * Retrieve row url.
     *
     * @param DataObject $row Row object.
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'marketplaceqa/questions/edit',
            [
                'store' => $this->_request->getParam('store'),
                'question' => true,
                'id' => $row->getId(),
            ]
        );
    }
}
