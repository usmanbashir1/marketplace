<?php

namespace Cminds\DropshipNotification\Controller\Dropship;

use Cminds\Marketplace\Controller\AbstractController;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Store\Model\StoreManagerInterface;
use Cminds\DropshipNotification\Model\Handler;

class Index extends AbstractController
{
    /** @var Handler  */
    protected $dropShipHandler;

    /** @var RedirectFactory */
    protected $resultRedirectFactory;

    public function __construct(
        Context $context,
        Data $helper,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Handler $dropshipHandler,
        RedirectFactory $redirectFactory
    ) {
        parent::__construct($context, $helper, $storeManager, $scopeConfig);
        $this->dropShipHandler = $dropshipHandler;
        $this->resultRedirectFactory = $redirectFactory;
    }

    /**
     * Process frontend dropship request
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        if ($this->canAccess() === false) {
            if ($this->getHelper()->getCustomerSession()->isLoggedIn()) {
                return $this->force404();
            }

            return $this->redirectToLogin();
        }

        $orderId = $this->getRequest()->getParam('order_id');

        if (!$orderId) {
            return $this->force404();
        }

        $this->dropShipHandler->process($orderId);

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

}
