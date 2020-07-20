<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Status\Index;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Backend\Block\Widget\Grid\Extended;
use Cminds\MarketplaceRma\Model\ResourceModel\Status\CollectionFactory;

/**
 * Class Grid
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Status\Index
 */
class Grid extends Extended
{
    /**
     * @var CollectionFactory
     */
    private $status;

    /**
     * Grid constructor.
     *
     * @param Context           $context
     * @param Data              $backendHelper
     * @param CollectionFactory $status
     * @param array             $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $status,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendHelper,
            $data
        );

        $this->status = $status;
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('rma_status');
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
        $collection = $this->status->create();

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * Prepare grid columns.
     *
     * @return Grid|Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('Id'),
                'width' => '50px',
                'index' => 'id',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'width' => '50px',
                'index' => 'name',
            ]
        );
        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'width' => '50px',
                'index' => 'created_at',
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
                            'base' => 'marketplacerma/status/edit',
                        ],
                        'field' => 'id',
                    ],
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
                            'base' => 'marketplacerma/status/delete',
                        ],
                        'field' => 'id',
                    ],
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