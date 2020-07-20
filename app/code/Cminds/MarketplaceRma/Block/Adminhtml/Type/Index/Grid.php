<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Type\Index;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Backend\Block\Widget\Grid\Extended;
use Cminds\MarketplaceRma\Model\ResourceModel\Type\CollectionFactory as RmaType;

/**
 * Class Grid
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Type\Index
 */
class Grid extends Extended
{
    /**
     * @var RmaType
     */
    private $rmaType;

    /**
     * Grid constructor.
     *
     * @param Context $context
     * @param Data    $backendHelper
     * @param RmaType $rmaType
     * @param array   $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        RmaType $rmaType,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendHelper,
            $data
        );

        $this->rmaType = $rmaType;
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('rma_type');
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
        $collection = $this->rmaType->create();

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
                'header' => __('ID'),
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
                            'base' => 'marketplacerma/type/edit',
                        ],
                        'field' => 'id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
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
                            'base' => 'marketplacerma/type/delete',
                        ],
                        'field' => 'id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ]
        );
        return parent::_prepareColumns();
    }
}
