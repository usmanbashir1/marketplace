<?php

namespace Cminds\DropshipNotification\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Cminds DropshipNotification config model.
 *
 * @category Cminds
 * @package  Cminds_DropshipNotification
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Config
{
    const XML_PATH_GENERAL_ENABLE =
        'cminds_dropshipnotification/general/enable';
    const XML_PATH_NOTIFICATION_SEND_EMAIL =
        'cminds_dropshipnotification/dropship_notification/send_email';
    const XML_PATH_NOTIFICATION_CHANGE_ORDER_STATUS =
        'cminds_dropshipnotification/dropship_notification/change_order_status';
    const XML_PATH_NOTIFICATION_NEW_ORDER_STATUS =
        'cminds_dropshipnotification/dropship_notification/new_order_status';
    const XML_PATH_TO_NOTIFICATION_EMAIL =
        'cminds_dropshipnotification/dropship_notification/email_template_id';

    const STATUS_COMPLETED = 1;
    const STATUS_INCOMPLETE = 0;

    /**
     * @var null|int
     */
    private $storeId = null;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Already fetched config values.
     *
     * @var array
     */
    private $config = [];

    /**
     * Object initialization.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Store id setter.
     *
     * @param null|int $storeId
     *
     * @return Config
     */
    public function setStoreId($storeId = null)
    {
        $this->storeId = $storeId;

        return $this;
    }

    /**
     * Return config field value.
     *
     * @param string $keyPath Key path.
     *
     * @return mixed
     */
    private function getConfigValue($keyPath)
    {
        if (isset($this->config[$keyPath]) === false) {
            $this->config[$keyPath] = $this->scopeConfig->getValue(
                $keyPath,
                ScopeInterface::SCOPE_STORE,
                $this->storeId
            );
        }

        return $this->config[$keyPath];
    }

    /**
     * Return bool value depends of that if module is enabled or not.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->getConfigValue(self::XML_PATH_GENERAL_ENABLE);
    }

    /**
     * Return bool value depends of that if should send email notification or not.
     *
     * @return bool
     */
    public function shouldSendEmailNotification()
    {
        return (bool)$this->getConfigValue(self::XML_PATH_NOTIFICATION_SEND_EMAIL);
    }

    /**
     * Return bool value depends of that if should change order status or not.
     *
     * @return bool
     */
    public function shouldChangeOrderStatus()
    {
        return (bool)$this->getConfigValue(self::XML_PATH_NOTIFICATION_CHANGE_ORDER_STATUS);
    }

    /**
     * Return new order status.
     *
     * @return string
     */
    public function geNewOrderStatus()
    {
        return $this->getConfigValue(self::XML_PATH_NOTIFICATION_NEW_ORDER_STATUS);
    }

    /**
     * Get Template Id to send on the Dropship Notification.
     *
     * @return string|int
     */
    public function getTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_TO_NOTIFICATION_EMAIL);
    }
}
