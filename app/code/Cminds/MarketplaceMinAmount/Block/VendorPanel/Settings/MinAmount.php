<?php

declare(strict_types=1);

namespace Cminds\MarketplaceMinAmount\Block\VendorPanel\Settings;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Cminds\MarketplaceMinAmount\Model\Config\Source\MinimumAmount;
use Magento\Framework\Pricing\PriceCurrencyInterface as PriceCurrency;

/**
 * MarketplaceMinAmount Block
 *
 * @category Cminds
 * @package  MarketplaceMinAmount
 * @author   Cminds Core Team <info@cminds.com>
 */
class MinAmount extends Template
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var MinimumAmount
     */
    protected $minimumAmount;

    /**
     * @var PriceCurrency
     */
    protected $priceCurrency;

    /**
     * MarketplaceMinAmount constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        MinimumAmount $minimumAmount,
        PriceCurrency $priceCurrency,
        array $data
    ) {
        parent::__construct($context, $data);

        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->minimumAmount = $minimumAmount;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Get Supplier Minium Order Amount
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMinOrderAmount()
    {
        $minAmount = [];
        $customerId = $this->customerSession->getCustomerId();
        $customer = $this->customerRepository->getById($customerId);

        $paypalEmail = $customer->getCustomAttribute('supplier_paypal_email');
        $paypalEmail = $paypalEmail !== null ? $paypalEmail->getValue() : '';

        $value = $customer->getCustomAttribute('supplier_min_order_amount');
        $minAmount['supplier_min_order_amount'] = $value !== null ? $value->getValue() : '';

        $value = $customer->getCustomAttribute('supplier_min_order_qty');
        $minAmount['supplier_min_order_qty'] = $value !== null ? $value->getValue() : '';

        $value = $customer->getCustomAttribute('supplier_min_order_amount_per');
        $minAmount['supplier_min_order_amount_per'] = $value !== null ? $value->getValue() : '0';

        return $minAmount;
    }

    /**
     * Get options array of Supplier Minium Order Amount
     *
     * @return array
     */
    public function getMinAmountOptions()
    {
        return $this->minimumAmount->toOptionArray();
    }

    /**
     * Convert decimal qty to int
     *
     * @param $qty
     *
     * @return string
     */
    public function convertMinOrderQty($qty)
    {
        $delimiters = [",","."];
        $ready = str_replace($delimiters, $delimiters[0], $qty);
        $launch = explode($delimiters[0], $ready);
        if ($launch[1] == 0) {
            $qty = number_format((float)$qty, 0);
        }

        return $qty;
    }

    /**
     * Get current currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->priceCurrency->getCurrency()->getCurrencyCode();
    }
}
