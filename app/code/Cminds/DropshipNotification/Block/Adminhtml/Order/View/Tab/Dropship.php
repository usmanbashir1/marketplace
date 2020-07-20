<?php

namespace Cminds\DropshipNotification\Block\Adminhtml\Order\View\Tab;

use Cminds\DropshipNotification\Model\Config as ModuleConfig;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Cminds\DropshipNotification\Model\ResourceModel\Order\Item\CollectionFactory;

/**
 * Cminds DropshipNotification admin order view dropship tab block.
 *
 * @category Cminds
 * @package  Cminds_DropshipNotification
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Dropship extends Template implements TabInterface
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Dropship constructor.
     *
     * @param Context           $context
     * @param ModuleConfig      $moduleConfig
     * @param CollectionFactory $collectionFactory
     * @param array             $data
     */
    public function __construct(
        Context $context,
        ModuleConfig $moduleConfig,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->collectionFactory = $collectionFactory;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Dropship');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Order History');
    }

    /**
     * Get Tab Class
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * Get Class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getTabClass();
    }

    /**
     * Get Tab Url
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl(
            'dropshipnotification/order/dropship',
            ['_current' => true]
        );
    }

    /**
     * return bool
     */
    public function canShowTab()
    {
        if ($this->moduleConfig->isEnabled() === false) {
            return false;
        }

        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $collection = $this->collectionFactory->create();
            $collection
                ->filterByOrderId($orderId)
                ->filterByStatus(ModuleConfig::STATUS_INCOMPLETE)
                ->load();

            if ($collection->getSize()) {
                return true;
            }
        }

        return false;
    }

    /**
     * return bool
     */
    public function isHidden()
    {
        return false;
    }
}
