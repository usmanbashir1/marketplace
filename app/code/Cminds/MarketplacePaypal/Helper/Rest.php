<?php

namespace Cminds\MarketplacePaypal\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Rest Helper
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class Rest extends AbstractHelper
{
    const TOKEN_URL = 'https://api.sandbox.paypal.com/v1/oauth2/token';

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * Rest constructor.
     * @param Context $context
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Context $context,
        TimezoneInterface $timezone
    ) {
        parent::__construct($context);
        $this->timezone = $timezone;
    }

    /**
     * Get token url
     *
     * @return string
     */
    public function getTokenUrl()
    {
        return self::TOKEN_URL;
    }

    /**
     * Get payout url
     *
     * @return string|null
     */
    public function getPayoutUrl()
    {
        return $this->scopeConfig->getValue('cminds_marketplacepaypal_configuration/rest_api/payout_url');
    }

    /**
     * Get payout update url
     *
     * @param string $batchId
     * @return string
     */
    public function getPayoutUpdateUrl(string $batchId): string
    {
        $template = $this->scopeConfig->getValue(
            'cminds_marketplacepaypal_configuration/rest_api/update_url'
        );

        return sprintf($template, $batchId);
    }

    /**
     * Get client id (login)
     *
     * @return string|null
     */
    public function getClientId()
    {
        return $this->scopeConfig->getValue('cminds_marketplacepaypal_configuration/rest_api/client_id');
    }

    /**
     * Get secret (password)
     *
     * @return string|null
     */
    public function getSecret()
    {
        return $this->scopeConfig->getValue('cminds_marketplacepaypal_configuration/rest_api/secret');
    }

    /**
     * Get email subject
     *
     * @return string|null
     */
    public function getEmailSubject()
    {
        return $this->scopeConfig->getValue('cminds_marketplacepaypal_configuration/general/email_subject');
    }

    /**
     * Get email subject
     *
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->scopeConfig->getValue('cminds_marketplacepaypal_configuration/general/currency');
    }

    /**
     * @return string
     */
    public function getCurrentDatetime(): string
    {
        return $this->timezone->date()->format(DateTime::DATETIME_PHP_FORMAT);
    }
}