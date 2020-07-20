<?php

namespace Cminds\DropshipNotification\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Cminds\DropshipNotification\Model\ResourceModel\Order\Item\CollectionFactory;
use Magento\Framework\App\Helper\Context;
use Cminds\DropshipNotification\Model\Config as ModuleConfig;

class Data extends AbstractHelper
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get dropship availability for order id
     *
     * @param int $orderId
     * @return bool
     */
    public function isDropshipButtonAvailable($orderId)
    {
        $collection = $this->collectionFactory->create();
        $collection
            ->filterByOrderId($orderId)
            ->filterByStatus(ModuleConfig::STATUS_INCOMPLETE)
            ->load();

        return $collection->getSize() !== 0;
    }
}
