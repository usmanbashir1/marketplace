<?php

namespace WeltPixel\QuickCart\Helper;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as SalesRuleCollectionFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * @var array
     */
    protected $_quickcartOptions;

    /**
     * @var SalesRuleCollectionFactory
     */
    protected $salesRuleCollectionFactory;

    protected  $freeShippingOptions = [
        [
            'flag' => 'carriers/freeshipping/active',
            'flag_free_shipping' => 'carriers/freeshipping/active',
            'treshold' => 'carriers/freeshipping/free_shipping_subtotal'
        ],
        [
            'flag' => 'carriers/ups/active',
            'flag_free_shipping' => 'carriers/ups/free_shipping_enable',
            'treshold' => 'carriers/ups/free_shipping_subtotal'
        ],
        [
            'flag' => 'carriers/usps/active',
            'flag_free_shipping' => 'carriers/usps/free_shipping_enable',
            'treshold' => 'carriers/usps/free_shipping_subtotal'
        ],
        [
            'flag' => 'carriers/fedex/active',
            'flag_free_shipping' => 'carriers/fedex/free_shipping_enable',
            'treshold' => 'carriers/fedex/free_shipping_subtotal'
        ],
        [
            'flag' => 'carriers/dhl/active',
            'flag_free_shipping' => 'carriers/dhl/free_shipping_enable',
            'treshold' => 'carriers/dhl/free_shipping_subtotal'
        ]
    ];


    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CheckoutSession $checkoutSession
     * @param PriceHelper $priceHelper
     * @param SalesRuleCollectionFactory $salesRuleCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CheckoutSession $checkoutSession,
        PriceHelper $priceHelper,
        SalesRuleCollectionFactory $salesRuleCollectionFactory
    ) {
        parent::__construct($context);

        $this->_storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->priceHelper = $priceHelper;
        $this->salesRuleCollectionFactory = $salesRuleCollectionFactory;
        $this->_quickcartOptions = $this->scopeConfig->getValue('weltpixel_quick_cart', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if QuickCart is enabled
     *
     * @return mixed
     */
    public function quicartIsEnabled()
    {
        return $this->scopeConfig->getValue(
            'weltpixel_quick_cart/general/enable',
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * Check if should open mini-cart after an item was added
     *
     * @return mixed
     */
    public function openMinicart()
    {
        if ($this->quicartIsEnabled()) {
            return $this->scopeConfig->getValue(
                'weltpixel_quick_cart/general/open_minicart',
                ScopeInterface::SCOPE_STORE,
                $this->getStoreId()
            );
        }

        return false;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getHeaderHeight($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/header/header_height', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['header']['header_height'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getHeaderBackground($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/header/header_background', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['header']['header_background'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getHeaderTextColor($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/header/header_text_color', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['header']['header_text_color'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSubtotalBackground($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/footer/subtotal_background', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['footer']['subtotal_background'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSubtotalTextColor($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/footer/subtotal_text_color', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['footer']['subtotal_text_color'];
        }
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function isQuickCartMessageEnabled($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/minicart_message/enable', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['minicart_message']['enable'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getQuickCartMessageContent($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/minicart_message/content', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['minicart_message']['content'];
        }
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function isQuickCartFreeShippingIntegrationEnabled($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/minicart_message/free_shipping_integration', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['minicart_message']['free_shipping_integration'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getQuickCartFreeShippingMessageContent($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/minicart_message/free_shipping_content', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['minicart_message']['free_shipping_content'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getQuickCartMessageTextColor($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/minicart_message/text_color', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['minicart_message']['text_color'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getQuickCartMessageFontSize($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/minicart_message/font_size', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['minicart_message']['font_size'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getQuickCartMessageCustomCss($storeId = 0)
    {
        if ($storeId) {
            return trim($this->scopeConfig->getValue('weltpixel_quick_cart/minicart_message/custom_css', ScopeInterface::SCOPE_STORE, $storeId));
        } else {
            return trim($this->_quickcartOptions['minicart_message']['custom_css']);
        }
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function isShoppingCartMessageEnabled($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/shoppingcart_message/enable', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['shoppingcart_message']['enable'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getShoppingCartMessageContent($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/shoppingcart_message/content', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['shoppingcart_message']['content'];
        }
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function isShoppingCartFreeShippingIntegrationEnabled($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/shoppingcart_message/free_shipping_integration', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['shoppingcart_message']['free_shipping_integration'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getShoppingCartFreeShippingMessageContent($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/shoppingcart_message/free_shipping_content', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['shoppingcart_message']['free_shipping_content'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getShoppingCartMessageTextColor($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/shoppingcart_message/text_color', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['shoppingcart_message']['text_color'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getShoppingCartMessageFontSize($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_quick_cart/shoppingcart_message/font_size', ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_quickcartOptions['shoppingcart_message']['font_size'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getShoppingCartMessageCustomCss($storeId = 0)
    {
        if ($storeId) {
            return trim($this->scopeConfig->getValue('weltpixel_quick_cart/shoppingcart_message/custom_css', ScopeInterface::SCOPE_STORE, $storeId));
        } else {
            return trim($this->_quickcartOptions['shoppingcart_message']['custom_css']);
        }
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuickCartMessageContentForDisplay()
    {
        $quickCartMessageContent = $this->getQuickCartMessageContent();
        if ($this->isQuickCartFreeShippingIntegrationEnabled() && $this->isAtleastOneFreeShippingMethodEnabled()) {
            $freeShippingFromCartRuleApplied = $this->_checkIfFreeShippingFromCartRuleApplied();
            $totals = $this->checkoutSession->getQuote()->getTotals();
            $subtotalData = $totals['subtotal'];
            $subtotal = $subtotalData->getValue();

            if ($this->getTaxCalculationPriceIncludesTax() && $subtotalData->getValueInclTax()) {
                $subtotal = $subtotalData->getValueInclTax();
            } elseif ( $subtotalData->getValueExclTax()) {
                $subtotal = $subtotalData->getValueExclTax();
            }

            $minimumOrderAmount = $this->_getFreeShippingMinimumOrderAmount();
            $freeShippingLimit = $minimumOrderAmount - $subtotal;
            if ($freeShippingFromCartRuleApplied || $freeShippingLimit <= 0) {
                $quickCartMessageContent = $this->getQuickCartFreeShippingMessageContent();
            } else {
                $formattedPrice = $this->priceHelper->currency($freeShippingLimit, true, false);
                $quickCartMessageContent = str_replace(['{amount_needed}'], ["<span id='quickcart-amount-needed'>" . $formattedPrice . "</span>"], $quickCartMessageContent);
            }
        }

        return $quickCartMessageContent;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getShoppingCartMessageContentForDisplay()
    {
        $shoppingCartMessageContent = $this->getShoppingCartMessageContent();
        if ($this->isShoppingCartFreeShippingIntegrationEnabled() && $this->isAtleastOneFreeShippingMethodEnabled()) {
            $freeShippingFromCartRuleApplied = $this->_checkIfFreeShippingFromCartRuleApplied();
            $totals = $this->checkoutSession->getQuote()->getTotals();
            $subtotalData = $totals['subtotal'];
            $subtotal = $subtotalData->getValue();

            if ($this->getTaxCalculationPriceIncludesTax() && $subtotalData->getValueInclTax()) {
                $subtotal = $subtotalData->getValueInclTax();
            } elseif ( $subtotalData->getValueExclTax()) {
                $subtotal = $subtotalData->getValueExclTax();
            }

            $minimumOrderAmount = $this->_getFreeShippingMinimumOrderAmount();
            $freeShippingLimit = $minimumOrderAmount - $subtotal;
            if ($freeShippingFromCartRuleApplied || $freeShippingLimit <= 0) {
                $shoppingCartMessageContent = $this->getShoppingCartFreeShippingMessageContent();
            } else {
                $formattedPrice = $this->priceHelper->currency($freeShippingLimit, true, false);
                $shoppingCartMessageContent = str_replace(['{amount_needed}'], ["<span id='shoppingcart-amount-needed'>" . $formattedPrice . "</span>"], $shoppingCartMessageContent);
            }
        }

        return $shoppingCartMessageContent;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _checkIfFreeShippingFromCartRuleApplied()
    {
        $appliedRuleIds = $this->checkoutSession->getQuote()->getAppliedRuleIds();
        if ($appliedRuleIds) {
            $salesRuleCollection = $this->salesRuleCollectionFactory->create();
            $salesRuleCollection->addFieldToFilter('rule_id', ['in' => explode(",", $appliedRuleIds)]);
            $salesRuleCollection->addFieldToFilter('simple_free_shipping', ['in' => [1,2]]);
            return ($salesRuleCollection->getSize()) ? true : false;
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function isAtleastOneFreeShippingMethodEnabled()
    {
        foreach ($this->freeShippingOptions as $shippingOption) {
            if ($this->scopeConfig->getValue($shippingOption['flag'], ScopeInterface::SCOPE_STORE) &&
                $this->scopeConfig->getValue($shippingOption['flag_free_shipping'], ScopeInterface::SCOPE_STORE)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed|null
     */
    protected function _getFreeShippingMinimumOrderAmount()
    {
        $minimumOrderAmount = null;

        foreach ($this->freeShippingOptions as $shippingOption) {
            if (($this->scopeConfig->getValue($shippingOption['flag'], ScopeInterface::SCOPE_STORE) &&
                $this->scopeConfig->getValue($shippingOption['flag_free_shipping'], ScopeInterface::SCOPE_STORE))) {
                if (is_null($minimumOrderAmount)) {
                    $minimumOrderAmount = $this->scopeConfig->getValue($shippingOption['treshold'], ScopeInterface::SCOPE_STORE);
                }
                $minimumOrderAmount = min($minimumOrderAmount, $this->scopeConfig->getValue($shippingOption['treshold'], ScopeInterface::SCOPE_STORE));
            }
        }
        return $minimumOrderAmount;
    }

    /**
     * @return mixed
     */
    protected function getTaxCalculationPriceIncludesTax() {
        return $this->scopeConfig->getValue('tax/calculation/price_includes_tax', ScopeInterface::SCOPE_STORE);
    }
}
