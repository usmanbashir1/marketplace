<?php

namespace Cminds\SupplierSubscription\Controller\Plugin\Supplierfrontendproductuploader\Product\Save;

use Cminds\SupplierSubscription\Controller\Plugin\Supplierfrontendproductuploader\Product\AbstractPlugin;
use Cminds\SupplierSubscription\Model\Plan;
use Cminds\SupplierSubscription\Model\PlanFactory;
use Cminds\SupplierSubscription\Helper\Data as SubscriptionHelper;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
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
     * @param   CustomerSession $customerSession
     * @param   PlanFactory $planFactory
     * @param   UrlInterface $url
     * @param   ResponseInterface $response
     * @param   ManagerInterface $messageManager
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
     * Validate number of uploaded images.
     *
     * @param ActionInterface  $subject Subject object.
     * @param RequestInterface $request Request object.
     *
     * @return void
     */
    public function beforeDispatch(
        ActionInterface $subject,
        RequestInterface $request
    ) {
        if ($this->subscriptionHelper->isEnabled() === false) {
            return;
        }

        if($this->customerSession->getCustomer()->getId())
            $this->subscriptionHelper
                ->checkCustomerDefaultPlan($this->customerSession->getCustomer());

        /** @var Plan $currentPlan */
        $currentPlan = $this->getCurrentPlan();
        $imageLimit = $currentPlan->getData('images_number');
        if ($imageLimit > 0) {
            $imagePostCount = 0;
            $postImages = $request->getPost('image');
            if (is_array($postImages)) {
                $imagePostCount = count($postImages);
            }

            if ($imagePostCount > $imageLimit) {
                $subject->getActionFlag()->set('', 'no-dispatch', true);

                $this->messageManager->addErrorMessage(
                    __('You have reached limit of images number per product.')
                );

                $this->response->setRedirect(
                    $request->getServerValue('HTTP_REFERER')
                );
            }
        }
    }
}
