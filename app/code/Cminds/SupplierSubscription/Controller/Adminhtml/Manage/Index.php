<?php

namespace Cminds\SupplierSubscription\Controller\Adminhtml\Manage;

use Cminds\SupplierSubscription\Controller\Adminhtml\AbstractManage;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Cminds\SupplierSubscription\Controller\Adminhtml\Manage
 */
class Index extends AbstractManage
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
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

    /**
     * Init actions.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    protected function initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Cminds_SupplierSubscription::manage_subscriptions');
        $resultPage->addBreadcrumb(
            __('Supplier Subscription Plans'),
            __('Supplier Subscription Plans')
        );
        $resultPage
            ->getConfig()
            ->getTitle()
            ->prepend(__('Supplier Subscription Plans'));

        return $resultPage;
    }

    public function execute()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->setBody(
                $this->_view->getLayout()
                    ->createBlock(
                        'Cminds\SupplierSubscription\Block\Adminhtml'
                        . '\Catalog\Plan\Plans\Grid'
                    )
                    ->toHtml()
            );
        } else {
            return $this->initAction();
        }
    }
}
