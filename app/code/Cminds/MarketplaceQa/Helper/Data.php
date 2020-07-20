<?php

namespace Cminds\MarketplaceQa\Helper;

use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Data constructor.
     *
     * @param Context         $context
     * @param CustomerSession $session
     */
    public function __construct(
        Context $context,
        CustomerSession $session
    ) {
        $this->customerSession = $session;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomerSession()
    {
        return $this->customerSession->getCustomer();
    }

    /**
     * @return bool
     */
    public function marketplaceQaEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            'configuration_marketplace_qa/configure/enabled'
        );
    }

    /**
     * @return int
     */
    public function getMaxQuestion()
    {
        return (int) $this->scopeConfig->getValue(
            'configuration_marketplace_qa/configure/max_question_per_product'
        );
    }

    /**
     * @return bool
     */
    public function questionFormVisible()
    {
        return (bool)$this->scopeConfig->getValue(
            'configuration_marketplace_qa/configure/question_form_visible_on_product_page'
        );
    }

    /**
     * @return bool
     */
    public function qaVisibleOnFrontend()
    {
        return (bool)$this->scopeConfig->getValue(
            'configuration_marketplace_qa/configure/question_and_answers_visible_on_product_page'
        );
    }

    /**
     * @return bool
     */
    public function adminApprovalRequired()
    {
        return (bool)$this->scopeConfig->getValue(
            'configuration_marketplace_qa/configure/admin_approval_required'
        );
    }

    /**
     * @return bool
     */
    public function notifyCustomerWhenQuestionWasSent()
    {
        return (bool)$this->scopeConfig->getValue(
            'configuration_marketplace_qa/configure/notify_customer_when_question_was_sent'
        );
    }

    /**
     * @return bool
     */
    public function notifyCustomerWhenAnswerWasPlaced()
    {
        return (bool)$this->scopeConfig->getValue(
            'configuration_marketplace_qa/configure/notify_customer_when_answer_was_placed'
        );
    }

    /**
     * @return bool
     */
    public function notifySupplierAboutNewQuestion()
    {
        return (bool)$this->scopeConfig->getValue(
            'configuration_marketplace_qa/configure/notify_supplier_about_new_question'
        );
    }

    /**
     * @return bool
     */
    public function notifyAdminAboutNewQuestion()
    {
        return (bool)$this->scopeConfig->getValue(
            'configuration_marketplace_qa/configure/notify_admin_about_new_question'
        );
    }

    /**
     * @return string
     */
    public function getStoreGeneralEmail()
    {
        return (string)$this->scopeConfig->getValue(
            'trans_email/ident_general/email'
        );
    }

    /**
     * @return string
     */
    public function getStoreGeneralName()
    {
        return (string)$this->scopeConfig->getValue(
            'trans_email/ident_general/name'
        );
    }
}
