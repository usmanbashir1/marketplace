<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Status;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Status
 */
class Index extends AbstractController
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param Context      $context
     * @param PageFactory  $resultPageFactory
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ModuleConfig $moduleConfig
    ) {
        parent::__construct($context, $moduleConfig);

        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Parent initAction method.
     *
     * @return Page
     */
    protected function initAction()
    {
        $resultPage = $this->resultPageFactory->create();

        $resultPage->addBreadcrumb(
            __('Marketplace Returns - Manage Status'),
            __('Marketplace Returns - Manage Status')
        );

        $resultPage
            ->getConfig()
            ->getTitle()
            ->prepend(__('Marketplace Returns - Manage Status'));

        return $resultPage;
    }

    /**
     * Execute method.
     *
     * @return \Magento\Framework\App\ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $this->getResponse()->setBody(
                $this->_view
                    ->getLayout()
                    ->createBlock(
                        '\Cminds\MarketplaceRma\Block\Adminhtml\Status\Index\Grid'
                    )
                    ->toHtml()
            );
        } else {
            return $this->initAction();
        }
    }
}
