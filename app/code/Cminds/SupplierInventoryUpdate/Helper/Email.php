<?php

namespace Cminds\SupplierInventoryUpdate\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Email extends AbstractHelper
{
    private $transportBuilder;
    private $storeManager;
    private $customerRepositoryInterface;

    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->customerRepositoryInterface = $customerRepositoryInterface;

        parent::__construct($context);
    }

    public function sendEmailTemplate($vendorId, $items)
    {
        $store = $this->storeManager->getStore()->getId();
        $vendor = $this->customerRepositoryInterface->getById($vendorId);

        $transport = $this->transportBuilder->setTemplateIdentifier('supplierinventoryupdate_template')
            ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
            ->setTemplateVars(
                [
                    'store' => $this->storeManager->getStore(),
                    'items' => (object)$items,
                    'vendor_name' => $vendor->getFirstname() . " " . $vendor->getLastname(),
                ]
            )
            ->setFrom('general')
            ->addTo($this->getStoreEmail(), $this->getStoreName())
            ->getTransport();
        $transport->sendMessage();

        return $this;
    }

    public function getStoreName()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/name',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getStoreEmail()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            ScopeInterface::SCOPE_STORE
        );
    }
}
