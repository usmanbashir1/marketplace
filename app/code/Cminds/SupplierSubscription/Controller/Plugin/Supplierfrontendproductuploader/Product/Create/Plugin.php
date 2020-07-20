<?php

namespace Cminds\SupplierSubscription\Controller\Plugin\Supplierfrontendproductuploader\Product\Create;

use Cminds\SupplierSubscription\Controller\Plugin\Supplierfrontendproductuploader\Product\AbstractPlugin;
use Cminds\SupplierSubscription\Helper\Data as SubscriptionHelper;
use Cminds\SupplierSubscription\Model\Plan;
use Cminds\SupplierSubscription\Model\PlanFactory;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;

class Plugin extends AbstractPlugin
{
    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * SupplierSubscription data helper.
     *
     * @var SubscriptionHelper
     */
    public $subscriptionHelper;

    /**
     * Object initialization.
     *
     * @param CustomerSession    $customerSession
     * @param PlanFactory        $planFactory
     * @param UrlInterface       $url
     * @param ResponseInterface  $response
     * @param ManagerInterface   $messageManager
     * @param SubscriptionHelper $subscriptionHelper
     */
    public function __construct(
        CustomerSession $customerSession,
        PlanFactory $planFactory,
        UrlInterface $url,
        ResponseInterface $response,
        ManagerInterface $messageManager,
        SubscriptionHelper $subscriptionHelper
    ) {
        $this->url = $url;
        $this->response = $response;
        $this->messageManager = $messageManager;
        $this->subscriptionHelper = $subscriptionHelper;
        $this->customerSession = $customerSession;

        parent::__construct($customerSession, $planFactory);
    }

    /**
     * Check if limit of products in current subscription plan was reached.
     *
     * @return void
     */
    public function beforeExecute()
    {
        if ($this->subscriptionHelper->isEnabled() === false) {
            return;
        }

        if($this->getCustomer()->getId()){
            $this->subscriptionHelper->checkCustomerDefaultPlan($this->getCustomer());

            /** @var Plan $currentPlan */
            $currentPlan = $this->getCurrentPlan();
            $validateProductsLimit = $currentPlan->validateProductsLimit($this->getCustomer());

            if (!$validateProductsLimit) {
                $this->messageManager->addErrorMessage(
                    __('You reached limit of products in your current subscription plan.')
                );

                $this->response->setRedirect(
                    $this->url->getUrl('supplier')
                );
            }
        }
    }
}
