<?php

namespace Cminds\Marketplace\Model\Plugin\Order\Email;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Cminds\Marketplace\Helper\Data;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Email\Container\InvoiceIdentity;

/**
 * Class InvoiceIdentityPlugin
 *
 * @package Cminds\Marketplace\Model\Plugin\Order\Email
 */
class InvoiceIdentityPlugin
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var Registry
     */
    private $registry;


    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Data $dataHelper,
        Registry $registry
    ) {
        $this->customerRepository = $customerRepository;
        $this->dataHelper = $dataHelper;
        $this->registry = $registry;
    }

    public function afterGetGuestTemplateId(InvoiceIdentity $subject, $result)
    {
        $flag = $this->getTemplateChangeFlag();
        if (!empty($flag)) {
            $guestCustomInvoiceEmailTemplate = $this->dataHelper->getGuestInvoiceEmailTemplate();
            return !empty($guestCustomInvoiceEmailTemplate) ? $guestCustomInvoiceEmailTemplate : $result;
        }
        return $result;
    }

    public function afterGetTemplateId(InvoiceIdentity $subject, $result)
    {
        $flag = $this->getTemplateChangeFlag();
        if (!empty($flag)) {
            $customInvoiceEmailTemplate = $this->dataHelper->getInvoiceEmailTemplate();
            return !empty($customInvoiceEmailTemplate) ? $customInvoiceEmailTemplate : $result;
        }
        return $result;
    }

    /**
     * @return mixed|null
     */
    private function getTemplateChangeFlag()
    {
        return $this->registry->registry(Data::LOAD_SUPPLIER_FOR_ORDER_FLAG);
    }
}
