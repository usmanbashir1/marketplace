<?php

namespace Cminds\SupplierSubscription\Block\Adminhtml\Catalog\Plan\Plans;

use Cminds\SupplierSubscription\Model\Plan;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use IntlDateFormatter;

/**
 * Class Grid
 *
 * @package Cminds\SupplierSubscription\Block\Adminhtml\Catalog\Plan\Plans
 */
class Grid extends Extended
{
    /**
     * Subscription plan object.
     *
     * @var Plan
     */
    protected $plan;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Grid constructor.
     *
     * @param Context $context
     * @param Data    $backendHelper
     * @param Plan    $plan
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Plan $plan
    ) {
        parent::__construct(
            $context,
            $backendHelper
        );

        $this->plan = $plan;
        $this->storeManager = $context->getStoreManager();
    }

    /**
     * Object initialization.
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('subscription_plans');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for grid.
     *
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->plan->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * Initialize grid columns.
     *
     * @return Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'index' => 'entity_id',
                'type' => 'number',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
            ]
        );

        $store = $this->storeManager->getStore();
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
                'type' => 'price',
                'header_css_class' => 'col-price',
                'column_css_class' => 'col-price',
            ]
        );

        $this->addColumn(
            'products_number',
            [
                'header' => __('Number of Products'),
                'index' => 'products_number',
                'type' => 'number',
            ]
        );
        $this->addColumn(
            'images_number',
            [
                'header' => __('Number of Images Per Product'),
                'index' => 'images_number',
                'type' => 'number',
            ]
        );
        $this->addColumn(
            'updated_at',
            [
                'header' => __('Updated At'),
                'index' => 'updated_at',
                'type' => 'datetime',
                'format' => IntlDateFormatter::MEDIUM
            ]
        );
        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at',
                'type' => 'datetime',
                'format' => IntlDateFormatter::MEDIUM
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
                        'url' => ['base' => '*/*/edit'],
                        'field' => 'entity_id',
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
