<?php

namespace Cminds\MarketplaceRma\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 *
 * @package Cminds\MarketplaceRma\Model
 */
class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var array
     */
    private $config = [];

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->storeId = $this->getStoreId();
    }

    /**
     * Get store id.
     *
     * @return int
     */
    private function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Get module config value.
     *
     * @param $fieldKey
     *
     * @return mixed
     */
    private function getConfigValue($fieldKey)
    {
        if (isset($this->config[$fieldKey]) === false) {
            $this->config[$fieldKey] = $this->scopeConfig->getValue(
                'cminds_marketplacerma/' . $fieldKey,
                ScopeInterface::SCOPE_STORE,
                $this->storeId
            );
        }

        return $this->config[$fieldKey];
    }

    /**
     * Return bool value depends of that if module is active or not.
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->getConfigValue('general/module_enabled');
    }

    /**
     * Can vendor get email about new Returns.
     *
     * @return bool
     */
    public function getEmailVendorAboutNewRma()
    {
        return (bool)$this->getConfigValue('general/email_vendor_about_new_rma');
    }

    /**
     * Can customer get email about Returns approval.
     *
     * @return bool
     */
    public function getEmailCustomerAboutApproval()
    {
        return (bool)$this->getConfigValue('general/email_customer_about_approval');
    }

    /**
     * Can customer get email about Returns status change.
     *
     * @return bool
     */
    public function getEmailCustomerAboutStatusChange()
    {
        return (bool)$this->getConfigValue('general/email_customer_about_status_change');
    }

    /**
     * Can vendor delete Returns.
     *
     * @return bool
     */
    public function getCanVendorDeleteRma()
    {
        return (bool)$this->getConfigValue('general/can_vendor_delete_rma');
    }
}
