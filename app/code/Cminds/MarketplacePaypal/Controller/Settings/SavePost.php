<?php

namespace Cminds\MarketplacePaypal\Controller\Settings;

use Cminds\Supplierfrontendproductuploader\Controller\AbstractController;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Cminds MarketplacePaypal save paypal settings controller.
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Piotr Pierzak <piotrek.pierzak@gmail.com>
 */
class SavePost extends AbstractController
{
    /**
     * Customer session object.
     *
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Customer repository object.
     *
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    public function __construct(
        Context $context,
        Data $helper,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct(
            $context,
            $helper,
            $storeManager,
            $scopeConfig
        );

        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    public function execute()
    {
        if (!$this->canAccess()) {
            return $this->redirectToLogin();
        }

        $customerId = $this->customerSession->getCustomer()->getId();
        $postData = $this->getRequest()->getParams();

        $customer = $this->customerRepository->getById($customerId);

        $customer->setCustomAttribute('supplier_paypal_email', '');
        if (!empty($postData['supplier_paypal_email'])) {
            $customer->setCustomAttribute(
                'supplier_paypal_email',
                $postData['supplier_paypal_email']
            );
        }

        $this->customerRepository->save($customer);

        $this->messageManager->addSuccessMessage(
            __('Paypal email address has been saved.')
        );

        $this->_redirect('*/*/paypal/');
    }
}
