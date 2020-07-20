<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Rma;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index extends AbstractController
{
    /**
     * Authorization level of a basic admin session.
     */
    const ADMIN_RESOURCE = 'Cminds_MarketplaceRma::manage_suppliers';

    /**
     * Page factory object.
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Object constructor.
     *
     * @param Context      $context
     * @param ModuleConfig $moduleConfig
     * @param PageFactory  $resultPageFactory
     */
    public function __construct(
        Context $context,
        ModuleConfig $moduleConfig,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $moduleConfig);

        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Init action.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    protected function initAction()
    {
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);

        $resultPage->addBreadcrumb(
            __('Marketplace Manage Returns'),
            __('Marketplace Manage Returns')
        );

        $resultPage
            ->getConfig()
            ->getTitle()
            ->prepend(__('Marketplace Manage Returns'));

        return $resultPage;
    }

    /**
     * Execute controller main logic.
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
                        'Cminds\MarketplaceRma\Block\Adminhtml\Rma\Index\Grid'
                    )
                    ->toHtml()
            );
        } else {
            return $this->initAction();
        }
    }
}
