<?php

namespace Cminds\SupplierRedirection\Block\Settings;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Domain extends Template
{
    /**
     * Customer Session.
     *
     * @var CustomerSession
     */
    private $customerSession;
    private $customer;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        Customer $customer
    ) {
        parent::__construct($context);

        $this->customerSession = $customerSession;
        $this->customer = $customer;
    }

    public function getCustomerSession()
    {
        return $this->customerSession;
    }

    public function getCustomerModel()
    {
        return $this->customer;
    }
}
