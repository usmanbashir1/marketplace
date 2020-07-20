<?php

namespace Cminds\MarketplaceRma\Block\Adminhtml\Reason\Index;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Backend\Block\Widget\Grid\Extended;
use Cminds\MarketplaceRma\Model\ResourceModel\Reason\CollectionFactory;

/**
 * Class Grid
 *
 * @package Cminds\MarketplaceRma\Block\Adminhtml\Reason\Index
 */
class Grid extends Extended
{
    /**
     * @var CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * Object constructor.
     *
     * @param Context           $context                   Context object.
     * @param Data              $backendHelper             Data helper object.
     * @param CollectionFactory $customerCollectionFactory Collection factory object.
     * @param array             $data                      Data array.
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $customerCollectionFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendHelper,
            $data
        );

        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('rma_reason');
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
                            'base' => 'marketplacerma/reason/edit',
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
                            'base' => 'marketplacerma/reason/delete',
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
