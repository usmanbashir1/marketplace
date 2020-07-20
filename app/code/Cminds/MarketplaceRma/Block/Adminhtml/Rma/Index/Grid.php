<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Rma\Index;

use Cminds\MarketplaceRma\Model\OptionProvider;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
#use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Backend\Block\Widget\Grid\Extended;
use Cminds\MarketplaceRma\Model\ResourceModel\Rma\CollectionFactory;

/**
 * Class Grid
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Rma\Index
 */
class Grid extends Extended
{
    /**
     * Customer collection factory object.
     *
     * @var CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var OptionProvider
     */
    private $optionProvider;

    /**
     * Grid constructor.
     *
     * @param Context           $context
     * @param Data              $backendHelper
     * @param CollectionFactory $customerCollectionFactory
     * @param OptionProvider    $optionProvider
     * @param array             $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $customerCollectionFactory,
        OptionProvider $optionProvider,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendHelper,
            $data
        );

        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->optionProvider = $optionProvider;
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('rma_list');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection.
     *
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->customerCollectionFactory->create();
        $collection->getSelect()
            ->joinLeft(
                ['ce' => 'customer_entity'],
                'ce.entity_id = main_table.customer_id',
                ['firstname', 'lastname']
            )
            ->joinLeft(
                ['so' => 'sales_order'],
                'so.entity_id = main_table.order_id',
                ['increment_id']
            );

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * Prepare grid columns.
     *
     * @return Grid|Extended
     *
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('#'),
                'width' => '50px',
                'index' => 'id',
            ]
        );
        $this->addColumn(
            'firstname',
            [
                'header' => __('Customer Firstname'),
                'width' => '50px',
                'index' => 'firstname',
            ]
        );
        $this->addColumn(
            'lastname',
            [
                'header' => __('Customer Lastname'),
                'width' => '50px',
                'index' => 'lastname',
            ]
        );
        $this->addColumn(
            'increment_id',
            [
                'header' => __('Order Id'),
                'width' => '50px',
                'index' => 'increment_id',
                'filter_index' => 'so.increment_id'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'width' => '50px',
                'index' => 'status',
                'type' => 'options',
                'options' => $this->optionProvider->getAvailableStatusesForIndexGrid(),
                'filter_index' => 'main_table.status'
            ]
        );
        $this->addColumn(
            'action_edit',
            [
                'header' => __('Edit'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => 'marketplacerma/rma/edit',
                        ],
                        'field' => 'id',
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'id',
                'is_system' => true,
            ]
        );

        $this->addColumn(
            'action_delete',
            [
                'header' => __('Delete'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Delete'),
                        'url' => [
                            'base' => 'marketplacerma/rma/delete',
                        ],
                        'field' => 'id',
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'id',
                'is_system' => true,
            ]
        );

        return parent::_prepareColumns();
    }
}
