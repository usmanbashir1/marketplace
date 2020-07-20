<?php

namespace Cminds\MarketplacePaypal\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Paypal\Model\Express as PaypalExpress;

/**
 * Cminds MarketplacePaypal config model.
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Piotr Pierzak <piotrek.pierzak@gmail.com>
 */
class Config
{
    /**
     * Scope config object.
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Store manager object.
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Paypal express object.
     *
     * @var PaypalExpress
     */
    private $paypalExpress;

    /**
     * Store id.
     *
     * @var int
     */
    private $storeId;

    /**
     * Already fetched config values.
     *
     * @var array
     */
    private $config = [];

    /**
     * Object initialization.
     *
     * @param ScopeConfigInterface  $scopeConfig Scope config object.
     * @param StoreManagerInterface $storeManager Store manager object.
     * @param PaypalExpress         $paypalExpress Paypal express object.
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        PaypalExpress $paypalExpress
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->paypalExpress = $paypalExpress;

        $this->storeId = $this->getStoreId();
    }

    /**
     * Return store id.
     *
     * @return int
     */
    private function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Return config field value.
     *
     * @param string $fieldKey Field key.
     *
     * @return mixed
     */
    private function getConfigValue($fieldKey)
    {
        if (isset($this->config[$fieldKey]) === false) {
            $this->config[$fieldKey] = $this->scopeConfig->getValue(
                'cminds_marketplacepaypal_configuration/' . $fieldKey,
                ScopeInterface::SCOPE_STORE,
                $this->storeId
            );
        }

        return $this->config[$fieldKey];
    }

    /**
     * Return bool values depends if paypal express payment method is
     * active or not.
     *
     * @return bool
     */
    public function isPaypalExpressActive()
    {
        return $this->paypalExpress->isActive($this->storeId);
    }

    /**
     * Return bool value depends of that if module is active or not.
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->getConfigValue('general/enable');
    }

    /**
     * Return transfer type.
     *
     * @return int
     */
    public function getTransferType()
    {
        return (int)$this->getConfigValue(
            'general/transfer_type'
        );
    }

    /**
     * Return payment mode.
     *
     * @return int
     */
    public function getPaymentMode()
    {
        return (int)$this->getConfigValue(
            'general/payment_mode'
        );
    }
}
