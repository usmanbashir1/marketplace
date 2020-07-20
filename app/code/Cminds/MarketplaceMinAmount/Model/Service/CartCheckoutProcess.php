<?php

declare(strict_types=1);

namespace Cminds\MarketplaceMinAmount\Model\Service;

use Cminds\MarketplaceMinAmount\Helper\Data;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Cminds\MarketplaceMinAmount\Model\ResourceModel\Sales\Order\SupplierDayAmount;

/**
 * MarketplaceMinAmount Cart and Checkout Process Service model
 *
 * @category Cminds
 * @package  MarketplaceMinAmount
 * @author   Cminds Core Team <info@cminds.com>
 */
class CartCheckoutProcess
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var \Cminds\MarketplaceMinAmount\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * @var CoreSession
     */
    protected $coreSession;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var ProductResource
     */
    protected $productResource;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerResource
     */
    protected $customerResource;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @var SupplierDayAmount
     */
    protected $supplierDayAmount;

    /**
     * @var $suppliers
     */
    protected $suppliers;

    /**
     * @var $products
     */
    protected $products;

    /**
     * CartCheckoutProcess constructor.
     * @param Data $dataHelper
     * @param MessageManager $messageManager
     * @param CoreSession $coreSession
     * @param CustomerSession $customerSession
     * @param ProductFactory $productFactory
     * @param ProductResource $productResource
     * @param CustomerFactory $customerFactory
     * @param CustomerResource $customerResource
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param SupplierDayAmount $supplierDayAmount
     */
    public function __construct(
        Data $dataHelper,
        MessageManager $messageManager,
        CoreSession $coreSession,
        CustomerSession $customerSession,
        ProductFactory $productFactory,
        ProductResource $productResource,
        CustomerFactory $customerFactory,
        CustomerResource $customerResource,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        SupplierDayAmount $supplierDayAmount
    ) {
        $this->dataHelper = $dataHelper;
        $this->messageManager = $messageManager;
        $this->coreSession = $coreSession;
        $this->customerSession = $customerSession;
        $this->productFactory = $productFactory;
        $this->productResource = $productResource;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->pricingHelper = $pricingHelper;
        $this->supplierDayAmount = $supplierDayAmount;
    }

    /**
     * Validate total order amount for each supplier.
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param array $results
     *
     * @return void
     */
    public function validateSupplierAmount($address, &$results)
    {
        if ($address->getQuote()->getIsVirtual()) {
            return $this;
        }

        $amounts = $this->collectAmountsByCreator($address);
        $qty = $this->collectQtyByCreator($address);

        foreach (array_keys($amounts) as $creatorId) {
            if ($creatorId === 'none') {
                continue;
            }
            if (isset($results[$creatorId])) {
                continue;
            }

            if (!isset($this->suppliers[$creatorId])) {
                $customer = $this->customerFactory->create();
                $this->customerResource->load($customer, $creatorId);
                $this->suppliers[$creatorId] = $customer;
            }

            $supplier = $this->suppliers[$creatorId];
            $limitValue = $this->getLimitValue($supplier);
            $limitQtyValue = $this->getLimitQtyValue($supplier);

            switch ($supplier->getSupplierMinOrderAmountPer()) {
                case \Cminds\MarketplaceMinAmount\Model\Config\Source\MinimumAmount::NONE:
                    continue;
                    break;

                case \Cminds\MarketplaceMinAmount\Model\Config\Source\MinimumAmount::ORDER:
                    $status = 0;
                    if ($limitValue > $amounts[$creatorId]) {
                        $status = 1;
                    }

                    if ($limitQtyValue > $qty[$creatorId]) {
                        $status = $status + 2;
                    }

                    if ($status > 0) {
                        $results[$creatorId] =
                            $this->getErrorResult(
                                $supplier,
                                $limitValue,
                                $amounts[$creatorId],
                                $limitQtyValue,
                                $qty[$creatorId],
                                $status
                            );
                    }
                    break;

                case \Cminds\MarketplaceMinAmount\Model\Config\Source\MinimumAmount::DAY:
                    $dayAmount = $this->getSupplierDayAmount($creatorId);
                    $dayQty = $this->getSupplierDayQty($creatorId);

                    $status = 0;

                    if ($limitValue - $dayAmount > $amounts[$creatorId]) {
                        $status = 1;
                    }

                    if ($limitQtyValue - $dayQty > $qty[$creatorId]) {
                        $status = $status + 2;
                    }

                    if ($status > 0) {
                        $results[$creatorId] = $this->getErrorResult(
                            $supplier,
                            $limitValue - $dayAmount,
                            $amounts[$creatorId],
                            $limitQtyValue - $dayQty,
                            $qty[$creatorId], $status);
                    }
                    break;
            }
        }

        return $this;
    }

    /**
     * Prepare error message.
     *
     * @param \Magento\Customer\Model\Customer $supplier
     * @param int $limitValue
     * @param int $amount
     * @param int $limitQty
     * @param int $qty
     * @param int $status
     *
     * @return array
     */
    public function getErrorResult($supplier, $limitValue, $amount, $limitQty, $qty, $status)
    {
        $result['error'] = true;
        if ($status === 1) {
            $result['message'] = __(
                'Minimum Order Amount for %1 products should be %2. Currently, you reached %3 in your Cart.',
                $this->getSupplierName($supplier),
                $this->pricingHelper->currency($limitValue, true, false),
                $this->pricingHelper->currency($amount, true, false)
            );
        } else {
            if ($status === 2) {
                $result['message'] = __(
                    'Minimum Order Qty for %1 products should be %2. Currently, you reached %3 in your Cart.',
                    $this->getSupplierName($supplier),
                    $this->dataHelper->convertMinOrderQty($limitQty),
                    $qty
                );
            } else {
                $result['message'] = __(
                    'Minimum Order Amount for %1 products should be %2 and the minimum Order Qty should be %3. Currently, you reached %4 in the Amount and %5 in the Qty in your Cart.',
                    $this->getSupplierName($supplier),
                    $this->pricingHelper->currency($limitValue, true, false),
                    $this->dataHelper->convertMinOrderQty($limitQty),
                    $this->pricingHelper->currency($amount, true, false),
                    $qty
                );
            }
        }

        return $result;
    }

    /**
     * Collect amount by creator.
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     *
     * @return array
     */
    public function collectAmountsByCreator($address)
    {
        $amounts = array();

        foreach ($address->getQuote()->getItemsCollection() as $item) {
            $currentAmount = 0;
            $productId = $item->getProductId();

            if (!isset($this->products[$productId])) {
                $product = $this->productFactory->create();
                $this->productResource->load($product, $productId);
                $this->products[$productId] = $product;
            }

            $creatorId = $this->products[$productId]->getCreatorId();

            if ($creatorId) {
                if (isset($amounts[$creatorId])) {
                    $currentAmount = $amounts[$creatorId];
                }
                $amounts[$creatorId] = $currentAmount + $item->getBaseRowTotal() - $item->getBaseDiscountAmount();
            } else {
                if (isset($amounts['none'])) {
                    $currentAmount = $amounts['none'];
                }
                $amounts['none'] = $currentAmount + $item->getBaseRowTotal() - $item->getBaseDiscountAmount();
            }
        }

        return $amounts;
    }

    /**
     * Collect amount by creator.
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     *
     * @return array
     */
    public function collectQtyByCreator($address)
    {
        $qty = array();

        foreach ($address->getQuote()->getItemsCollection() as $item) {
            $currentQty = 0;
            $productId = $item->getProductId();

            if (!isset($this->products[$productId])) {
                $product = $this->productFactory->create();
                $this->productResource->load($product, $productId);
                $this->products[$productId] = $product;
            }

            $creatorId = $this->products[$productId]->getCreatorId();

            if ($creatorId) {
                if (isset($qty[$creatorId])) {
                    $currentQty = $qty[$creatorId];
                }
                $qty[$creatorId] = $currentQty + $item->getQty();
            } else {
                if (isset($qty['none'])) {
                    $currentQty = $qty['none'];
                }
                $qty['none'] = $currentQty + 1;
            }
        }

        return $qty;
    }

    /**
     * Get minimum required amount for supplier.
     *
     * @param \Magento\Customer\Model\Customer $supplier
     *
     * @return float
     */
    public function getLimitValue($supplier)
    {
        return $supplier->getSupplierMinOrderAmount();
    }

    /**
     * Get minimum required Qty for supplier.
     *
     * @param \Magento\Customer\Model\Customer $supplier
     *
     * @return float
     */
    public function getLimitQtyValue($supplier)
    {
        return $supplier->getSupplierMinOrderQty();
    }

    /**
     * Get minimum required amount for supplier per day.
     *
     * @param $creatorId
     *
     * @return float
     */
    public function getSupplierDayAmount($creatorId)
    {
        $daySupplierAmount = $this->supplierDayAmount->getSupplierDayAmount($creatorId);

        return $daySupplierAmount;
    }

    /**
     * Get minimum required Qty for supplier per day.
     *
     * @param $creatorId
     *
     * @return float
     */
    public function getSupplierDayQty($creatorId)
    {
        $daySupplierQty = $this->supplierDayAmount->getSupplierDayQty($creatorId);

        return $daySupplierQty;
    }

    /**
     * Get name of supplier.
     *
     * @param \Magento\Customer\Model\Customer $supplier
     *
     * @return string
     */
    public function getSupplierName($supplier)
    {
        if ($supplier->getSupplierName()) {
            return $supplier->getSupplierName();
        }

        return $supplier->getFirstname() . ' ' . $supplier->getLastname();
    }
}