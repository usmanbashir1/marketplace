<?php

namespace Cminds\MarketplaceQa\Controller\Adminhtml\Questions;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    private $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Cminds_MarketplaceQa::marketplace_qa'
        );
        $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
        $resultPage->addBreadcrumb(
            __('Questions'),
            __('Questions')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Supplier Products Questions'));

        return $resultPage;
    }
}