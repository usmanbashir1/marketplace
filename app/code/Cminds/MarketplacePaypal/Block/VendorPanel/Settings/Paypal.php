<?php

namespace Cminds\MarketplacePaypal\Block\VendorPanel\Settings;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Setting Paypal
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class Paypal extends Template
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
     * Paypal constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        array $data
    ) {
        parent::__construct($context, $data);

        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get Supplier Paypal Email
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSupplierPaypalEmail(): string
    {
        $customerId = $this->customerSession->getCustomerId();
        $customer = $this->customerRepository->getById($customerId);

        $paypalEmail = $customer->getCustomAttribute('supplier_paypal_email');
        $paypalEmail = $paypalEmail !== null ? $paypalEmail->getValue() : '';
        return $paypalEmail;
    }
}
