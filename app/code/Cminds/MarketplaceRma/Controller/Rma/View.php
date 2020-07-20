<?php

namespace Cminds\MarketplaceRma\Controller\Rma;

use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Result\Page;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class View
 *
 * @package Cminds\MarketplaceRma\Controller\Rma
 */
class View extends AbstractController
{
    /**
     * @var Product
     */
    private $product;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * View constructor.
     *
     * @param Context               $context
     * @param PageFactory           $pageFactory
     * @param Product               $product
     * @param Registry              $registry
     * @param CustomerSession       $customerSession
     * @param ModuleConfig          $moduleConfig
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Product $product,
        Registry $registry,
        CustomerSession $customerSession,
        ModuleConfig $moduleConfig
    ) {
        parent::__construct(
            $context,
            $moduleConfig
        );

        $this->product = $product;
        $this->registry = $registry;
        $this->customerSession = $customerSession;
        $this->pageFactory = $pageFactory;
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

        $rmaId = $this->getRequest()->getParam('id');

        $this->registry->register('rma_edit_id', $rmaId);
        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('General Information'));

        return $resultPage;
    }
}
