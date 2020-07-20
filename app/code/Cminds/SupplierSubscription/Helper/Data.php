<?php

namespace Cminds\SupplierSubscription\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Customer\Model\Customer;

class Data extends AbstractHelper
{
    /**
     * Get enable configuration from config.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag('subscriptions_configuration/general/module_enabled');
    }

    /**
     * Get default plan id from store configuration.
     *
     * @return int
     */
    public function getDefaultPlanId()
    {
        return (int)$this->scopeConfig->getValue(
            'subscriptions_configuration/general/default_plan'
        );
    }

    /**
     * @return bool
     */
    public function isNotificationEnabled()
    {
        return $this->scopeConfig->isSetFlag('subscriptions_configuration/notification/notification_enabled');
    }

    /**
     * @return int
     */
    public function getNotificationDaysToSendEmail()
    {
        return (int)$this->scopeConfig->getValue(
            'subscriptions_configuration/notification/notification_days'
        );
    }

    /**
     * @return mixed
     */
    public function getNotificationEmailTemplate()
    {
        return $this->scopeConfig->getValue(
            'subscriptions_configuration/notification/email_template'
        );
    }

    /**
     * @return mixed
     */
    public function getStoreGeneralName()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_general/name',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    /**
     * @return mixed
     */
    public function getStoreGeneralEmail()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_general/email',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    /**
     * Check if customer has default plan, if not add default plan.
     *
     * @param Customer $customerSession
     *
     * @return bool
     */
    public function checkCustomerDefaultPlan(Customer $customer)
    {
        $defaultPlanWasPresent = true;

        $currentPlanId = $customer->getCurrentPlanId();
        $defaultPlanId = $this->getDefaultPlanId();

        if (!$currentPlanId && $defaultPlanId) {
            $planToDate = date('Y-m-d H:i:s', strtotime('+12 months'));
            $customer
                ->setPlanFromDate(date('Y-m-d H:i:s'))
                ->setPlanToDate($planToDate)
                ->setCurrentPlanId($defaultPlanId)
                ->save();
            $defaultPlanWasPresent = false;
        }

        return $defaultPlanWasPresent;
    }

}