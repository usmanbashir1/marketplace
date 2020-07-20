<?php

declare(strict_types=1);

namespace Cminds\MarketplaceMinAmount\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Cminds\MarketplaceMinAmount\Helper\Data;
use Cminds\MarketplaceMinAmount\Model\Service\CartCheckoutProcess;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;


/**
 * MarketplaceMinAmount Observer
 *
 * @category Cminds
 * @package  MarketplaceMinAmount
 * @author   Cminds Core Team <info@cminds.com>
 */
class OnCartAndMultishippingLoad implements ObserverInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var \Cminds\MarketplaceMinAmount\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var CartCheckoutProcess
     */
    protected $cartCheckoutProcess;

    /**
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * @var CoreSession
     */
    protected $coreSession;


    /**
     * OnCartAndMultishippingLoad constructor.
     * @param CheckoutSession $checkoutSession
     * @param Data $dataHelper
     * @param CartCheckoutProcess $cartCheckoutProcess
     * @param MessageManager $messageManager
     * @param CoreSession $coreSession
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Data $dataHelper,
        CartCheckoutProcess $cartCheckoutProcess,
        MessageManager $messageManager,
        CoreSession $coreSession
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->dataHelper = $dataHelper;
        $this->cartCheckoutProcess = $cartCheckoutProcess;
        $this->messageManager = $messageManager;
        $this->coreSession = $coreSession;

    }

    public function execute(Observer $observer)
    {
        if (!$this->dataHelper->isModuleEnabled()) {
            return $this;
        }

        /** @var \Magento\Quote\Model\Quote  */
        $quote = $this->checkoutSession->getQuote();

        $addresses = $quote->getAllAddresses();
        $results = [];

        foreach ($addresses as $address) {
            /** Supplier Amount validation */
            $this->cartCheckoutProcess->validateSupplierAmount($address, $results);
       }

        foreach ($results as $result) {
            if (isset($result['error']) && $result['error']) {
                $this->messageManager->addErrorMessage($result['message']);
            }
        }

        $this->coreSession->setSuppliersErrorsMessages($results);

        return $this;
    }
}