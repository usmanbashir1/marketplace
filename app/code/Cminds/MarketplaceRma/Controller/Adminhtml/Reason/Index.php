<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Reason;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Reason
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
     * Init action method.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    protected function initAction()
    {
        $resultPage = $this->resultPageFactory->create();

        $resultPage->addBreadcrumb(
            __('Marketplace Returns - Manage Reasons'),
            __('Marketplace Returns - Manage Reasons')
        );

        $resultPage
            ->getConfig()
            ->getTitle()
            ->prepend(__('Marketplace Returns - Manage Reasons'));

        return $resultPage;
    }

    /**
     * Execute method.
     *
     * @return Page|string
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $this->getResponse()->setBody(
                $this->_view
                    ->getLayout()
                    ->createBlock(
                        '\Cminds\MarketplaceRma\Block\Adminhtml\Reason\Index\Grid'
                    )
                    ->toHtml()
            );
        } else {
            return $this->initAction();
        }
    }
}
