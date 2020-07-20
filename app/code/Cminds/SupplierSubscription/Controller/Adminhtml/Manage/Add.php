<?php

namespace Cminds\SupplierSubscription\Controller\Adminhtml\Manage;

use Cminds\SupplierSubscription\Controller\Adminhtml\AbstractManage;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

class Add extends AbstractManage
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Add constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $this->_forward('edit');
    }
}
