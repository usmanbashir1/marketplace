<?php

namespace Cminds\SupplierInventoryUpdate\Controller\Inventory;

use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Update extends Action
{
    private $customerSession;
    private $pageFactory;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);

        $this->customerSession = $customerSession;
        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
        if ($this->customerSession->isLoggedIn()) {
            $pageObject = $this->pageFactory->create();

            return $pageObject;
        } else {
            $this->_redirect('/customer/account/login');
        }
    }
}
