<?php

namespace Cminds\MarketplaceRma\Block\SupplierRma;

use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Cminds\Supplierfrontendproductuploader\Model\Product as CmindsProduct;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Model\Stock\Item as StockItem;
use Magento\Framework\Pricing\Helper\Data as CurrencyHelper;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory;

/**
 * Class Index
 *
 * @package Cminds\MarketplaceRma\Block\SupplierRma
 */
class Index extends Template
{
    /**
     * @var AttributeFactory
     */
    private $attributeFactory;

    /**
     * @var Data
     */
    private $cmindsHelper;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var CurrencyHelper
     */
    private $currencyHelper;

    /**
     * @var StockItem
     */
    private $stockItem;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var StockStateInterface
     */
    private $stockState;

    /**
     * @var OrderItemCollectionFactory
     */
    private $orderItemCollectionFactory;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * Index constructor.
     *
     * @param AttributeFactory           $attributeFactory
     * @param Context                    $context
     * @param Data                       $helperCminds
     * @param Product                    $product
     * @param CurrencyHelper             $coreHelper
     * @param StockItem                  $stockItem
     * @param ResourceConnection         $resource
     * @param ProductFactory             $productFactory
     * @param OrderItemCollectionFactory $orderItemCollectionFactory
     * @param StockStateInterface        $stockState
     * @param ModuleConfig               $moduleConfig
     */
    public function __construct(
        AttributeFactory $attributeFactory,
        Context $context,
        Data $helperCminds,
        Product $product,
        CurrencyHelper $coreHelper,
        StockItem $stockItem,
        ResourceConnection $resource,
        ProductFactory $productFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        StockStateInterface $stockState,
        ModuleConfig $moduleConfig
    ) {
        parent::__construct($context);

        $this->cmindsHelper = $helperCminds;
        $this->product = $product;
        $this->productFactory = $productFactory;
        $this->currencyHelper = $coreHelper;
        $this->stockItem = $stockItem;
        $this->resource = $resource;
        $this->stockState = $stockState;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->attributeFactory = $attributeFactory;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Prepare layout.
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->getItems()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'supplier.product.pager'
            )
            ->setAvailableLimit([20=>20])
            ->setShowPerPage(true)
            ->setCollection(
                $this->getItems()
            );
            $this->setChild('pager', $pager);
            $this->getItems()->load();
        }
        
        return $this;
    }

    /**
     * Get pager html.
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get collection items.
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    public function getItems()
    {
        $eavAttribute = $this->attributeFactory->create();
        $attributeId = $eavAttribute->getIdByCode(
            'catalog_product',
            'creator_id'
        );
        $supplierId = $this->cmindsHelper->getSupplierId();
        $collection = $this->orderItemCollectionFactory->create();
        $collection->getSelect()
            ->join(
                ['pi' => 'catalog_product_entity_int'],
                'pi.entity_id = main_table.product_id AND pi.attribute_id = ' . $attributeId,
                ['value as supplier_id', 'entity_id']
            )
            ->join(
                ['o' => 'sales_order'],
                'o.entity_id = main_table.order_id',
                [
                    'status',
                    'state',
                    'increment_id',
                    'created_at',
                    'customer_firstname',
                    'customer_lastname',
                    'customer_email'
                ]
            )
            ->joinRight(
                ['cmr' => 'cminds_marketplace_rma'],
                'cmr.order_id = main_table.order_id',
                [
                    'id AS rma_id',
                    'additional_info',
                    'created_at as rma_created_at'
                ]
            )
            ->joinLeft(
                ['rma_status' => 'cminds_marketplace_rma_status'],
                'cmr.status = rma_status.id',
                ['name as status_name']
            )
            ->joinLeft(
                ['rma_reason' => 'cminds_marketplace_rma_reason'],
                'cmr.reason = rma_reason.id',
                ['name as reason_name']
            )
            ->where('main_table.parent_item_id is null')
            ->where('o.state != "canceled"')
            ->where('pi.value = ?', $supplierId)
            ->group('main_table.order_id') // @codingStandardsIgnoreLine
            ->group('pi.value') // @codingStandardsIgnoreLine
            ->order('o.entity_id desc');

        $page = $this->_request->getParam('p', 1);
        $collection->setPageSize(20)->setCurPage($page);

        return $collection;
    }

    /**
     * Get helper.
     *
     * @return Data
     */
    public function getCmindsHelper()
    {
        return $this->cmindsHelper;
    }

    /**
     * Get Items count.
     *
     * @return int
     */
    public function getItemsCount()
    {

        $items = $this->getItems()->getData();

        return count($items);
    }

    /**
     * Get currency helper.
     *
     * @return CurrencyHelper
     */
    public function getCurrencyHelper()
    {
        return $this->currencyHelper;
    }

    /**
     * Get stock item.
     *
     * @return StockItem
     */
    public function getStockItem()
    {
        return $this->stockItem;
    }

    /**
     * Get view url.
     *
     * @param $rmaId
     *
     * @return string
     */
    public function getViewUrl($rmaId)
    {
        return $this->getUrl('marketplace-rma/supplierrma/view/', ['id' => $rmaId]);
    }

    /**
     * Get delete url.
     *
     * @param $rmaId
     *
     * @return string
     */
    public function getDeleteUrl($rmaId)
    {
        return $this->getUrl('marketplace-rma/supplierrma/delete/', ['id' => $rmaId]);
    }

    /**
     * Check is delete option is enabled in configuration.
     *
     * @return bool
     */
    public function canDeleteRma()
    {
        return $this->moduleConfig->getCanVendorDeleteRma();
    }

    /**
     * Get product qty.
     *
     * @param $product_id
     *
     * @return float
     */
    public function getQty($product_id)
    {
        $product = $this->productFactory->create()->load($product_id);

        return $this->stockState->getStockQty(
            $product_id,
            $product->getStore()->getWebsiteId()
        );
    }
}
