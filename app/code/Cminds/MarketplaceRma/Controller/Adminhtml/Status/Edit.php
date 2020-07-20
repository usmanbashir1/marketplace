<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Status;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\ResourceModel\Status\CollectionFactory;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

/**
 * Class Edit
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Status
 */
class Edit extends AbstractController
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var CollectionFactory
     */
    private $statusFactory;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Edit constructor.
     *
     * @param Context           $context
     * @param PageFactory       $resultPageFactory
     * @param Registry          $coreRegistry
     * @param CollectionFactory $statusFactory
     * @param ModuleConfig      $moduleConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        CollectionFactory $statusFactory,
        ModuleConfig $moduleConfig
    ) {
        parent::__construct($context, $moduleConfig);

        $this->resultPageFactory = $resultPageFactory;
        $this->statusFactory = $statusFactory;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Execute method.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|Page
     */
    public function execute()
    {
        $rmaId = $this->getRequest()->getParam('id');
        $collection = $this->statusFactory
            ->create()
            ->addFieldToFilter('id', $rmaId);
        $item = $collection->getFirstItem();
        $this->coreRegistry->register('rma_status_data', $item);

        $resultPage = $this->resultPageFactory->create();
        if ($rmaId) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Status'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('Create Status'));
        }

        return $resultPage;
    }
}
