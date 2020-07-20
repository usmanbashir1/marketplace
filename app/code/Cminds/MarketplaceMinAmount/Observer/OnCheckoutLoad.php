<?php

declare(strict_types=1);

namespace Cminds\MarketplaceMinAmount\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Cminds\MarketplaceMinAmount\Helper\Data;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;
use Cminds\MarketplaceMinAmount\Model\Service\CartCheckoutProcess;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\ResponseFactory;

/**
 * MarketplaceMinAmount Observer
 *
 * @category Cminds
 * @package  MarketplaceMinAmount
 * @author   Cminds Core Team <info@cminds.com>
 */
class OnCheckoutLoad implements ObserverInterface
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
     * @var \Magento\Framework\App\ActionFlag
     */
    private $actionFlag;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @var ResponseFactory
     */
    protected $response;

    /**
     * OnCheckoutLoad constructor.
     * @param CheckoutSession $checkoutSession
     * @param Data $dataHelper
     * @param MessageManager $messageManager
     * @param CoreSession $coreSession
     * @param CartCheckoutProcess $cartCheckoutProcess
     * @param ActionFlag $actionFlag
     * @param UrlInterface $url
     * @param ResponseFactory $response
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Data $dataHelper,
        MessageManager $messageManager,
        CoreSession $coreSession,
        CartCheckoutProcess $cartCheckoutProcess,
        ActionFlag $actionFlag,
        UrlInterface $url,
        \Magento\Framework\App\ResponseFactory $response
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->dataHelper = $dataHelper;
        $this->messageManager = $messageManager;
        $this->coreSession = $coreSession;
        $this->cartCheckoutProcess = $cartCheckoutProcess;
        $this->actionFlag = $actionFlag;
        $this->url = $url;
        $this->response = $response;
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
            if (isset($result['error']) && $result['error'] != '') {
                // Stop further processing if your condition is met
                $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);

                // then in last redirect
                $message = __("You can't create an order because the suppliers have set restrictions.");
                $this->messageManager->addErrorMessage($message);
                $cartUrl = $this->url->getUrl('checkout/cart/index');
                $this->response->create()->setRedirect($cartUrl)->sendResponse();

                exit;
            }
        }

        return $this;
    }
}