<?php

namespace Cminds\MarketplaceRma\Controller\Rma;

use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Cminds\MarketplaceRma\Controller\Rma
 */
class Index extends AbstractController
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * Index constructor.
     *
     * @param Context         $context
     * @param PageFactory     $pageFactory
     * @param CustomerSession $customerSession
     * @param ModuleConfig    $moduleConfig
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        CustomerSession $customerSession,
        ModuleConfig $moduleConfig
    ) {
        parent::__construct(
            $context,
            $moduleConfig
        );

        $this->pageFactory = $pageFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * Execute method.
     *
     * @return bool|ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $this->customerSession->authenticate();
        }

        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Returns'));

        return $resultPage;
    }
}
