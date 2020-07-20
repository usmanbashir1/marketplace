<?php

declare(strict_types=1);

namespace Cminds\MarketplaceMinAmount\Controller\Settings;

use Cminds\Supplierfrontendproductuploader\Controller\AbstractController;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Cminds MarketplaceMinAmount save minamount settings controller.
 *
 * @category Cminds
 * @package  MarketplaceMinAmount
 * @author   Cminds Core Team <info@cminds.com>
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
        $customer = $this->customerRepository->getById($customerId);
        $postData = $this->getRequest()->getParams();

        if (!empty($postData['supplier_min_order_amount'])) {
            $customer->setCustomAttribute(
                'supplier_min_order_amount',
                $postData['supplier_min_order_amount']
            );
        } else {
            $customer->setCustomAttribute('supplier_min_order_amount', '');
        }
        if (!empty($postData['supplier_min_order_qty'])) {
            $customer->setCustomAttribute(
                'supplier_min_order_qty',
                $postData['supplier_min_order_qty']
            );
        } else {
            $customer->setCustomAttribute('supplier_min_order_qty', '');
        }
        if (!empty($postData['supplier_min_order_amount_per'])) {
            $customer->setCustomAttribute(
                'supplier_min_order_amount_per',
                $postData['supplier_min_order_amount_per']
            );
        } else {
            $customer->setCustomAttribute('supplier_min_order_amount_per',
                \Cminds\MarketplaceMinAmount\Model\Config\Source\MinimumAmount::NONE);
        }

        try {
            $this->customerRepository->save($customer);

            $this->messageManager->addSuccessMessage(
                __('Order restrictions have been saved.')
            );
        }  catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                __('There was an error during save order restrictions.')
            );
        }

        $this->_redirect('*/*/minamount/');
    }
}
