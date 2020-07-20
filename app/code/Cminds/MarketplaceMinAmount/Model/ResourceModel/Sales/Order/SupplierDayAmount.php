<?php

declare(strict_types=1);

namespace Cminds\MarketplaceMinAmount\Model\ResourceModel\Sales\Order;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollection;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;

/**
 * Supplier Day Amount Order Collection
 *
 * @category Cminds
 * @package  MarketplaceMinAmount
 * @author   Cminds Core Team <info@cminds.com>
 */
class SupplierDayAmount extends OrderCollection
{
    /**
     * @var TimezoneInterface
     */
    protected $_date;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var Attribute
     */
    protected $eavAttribute;

    /**
     * @var OrderCollection
     */
    protected $orderCollection;

    /**
     * SupplierDayAmount constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param Attribute $eavAttribute
     */
    public function __construct(
        TimezoneInterface $date,
        ResourceConnection $resourceConnection,
        Attribute $eavAttribute,
        OrderCollection $orderCollection
    ) {
        $this->_date = $date;
        $this->resource = $resourceConnection;
        $this->eavAttribute = $eavAttribute;
        $this->orderCollection = $orderCollection;
    }

    /**
     * Ordered amount of the supplier per day
     *
     * @param $customerId
     * @return mixed
     */
    public function getSupplierDayAmount($customerId)
    {
        $code = $this->eavAttribute->getIdByCode('catalog_product', 'creator_id');

        $now = $this->_date->date();
        $dateStart = $now->format('Y-m-d 00:00:00');
        $dateEnd = $now->format('Y-m-d 23:59:59');

        $productIntTable = $this->resource->getTableName('catalog_product_entity_int');
        $orderItemTable = $this->resource->getTableName('sales_order_item');

        $collection = $this->orderCollection->create();

        $collection->addFieldToFilter('main_table.created_at', array('from' => $dateStart, 'to' => $dateEnd));

        $collection->getSelect()
            ->joinInner(array('i' => $orderItemTable), 'i.order_id = main_table.entity_id', array())
            ->joinInner(
                array('e' => $productIntTable),
                'e.entity_id = i.product_id AND e.attribute_id = ' . $code,
                array()
            )
            ->where('i.parent_item_id is null')
            ->where('e.value = ?', $customerId);

        $collection->addExpressionFieldToSelect(
            'sale_amount',
            'SUM(i.base_row_total - i.base_discount_amount)',
            'i.base_row_total - i.base_discount_amount'
        );

        $orderAmount = $collection->getFirstItem()->getData('sale_amount');

        return $orderAmount;
    }

    /**
     * Ordered qty of the supplier per day
     *
     * @param $creatorId
     * @return mixed
     */
    public function getSupplierDayQty($creatorId)
    {
        $code = $this->eavAttribute->getIdByCode('catalog_product', 'creator_id');

        $now = $this->_date->date();
        $dateStart = $now->format('Y-m-d 00:00:00');
        $dateEnd = $now->format('Y-m-d 23:59:59');

        $productIntTable = $this->resource->getTableName('catalog_product_entity_int');
        $orderItemTable = $this->resource->getTableName('sales_order_item');

        $collection = $this->orderCollection->create();

        $collection->addFieldToFilter('main_table.created_at', array('from' => $dateStart, 'to' => $dateEnd));

        $collection->getSelect()
            ->joinInner(array('i' => $orderItemTable), 'i.order_id = main_table.entity_id', array())
            ->joinInner(
                array('e' => $productIntTable),
                'e.entity_id = i.product_id AND e.attribute_id = ' . $code,
                array()
            )
            ->where('i.parent_item_id is null')
            ->where('e.value = ?', $creatorId);

        $collection->addExpressionFieldToSelect('qty_sum', 'SUM(total_qty_ordered)', 'total_qty_ordered');

        $qtyAmount = $collection->getFirstItem()->getData('qty_sum');

        return $qtyAmount;
    }
}