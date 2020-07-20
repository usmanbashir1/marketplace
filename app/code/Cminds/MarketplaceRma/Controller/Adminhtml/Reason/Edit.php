<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Reason;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Model\ResourceModel\Reason\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

/**
 * Class Edit
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Reason
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
    private $reasonFactory;

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
     * @param CollectionFactory $reasonFactory
     * @param ModuleConfig      $moduleConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        CollectionFactory $reasonFactory,
        ModuleConfig $moduleConfig
    ) {
        parent::__construct($context, $moduleConfig);

        $this->resultPageFactory = $resultPageFactory;
        $this->reasonFactory = $reasonFactory;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Execute method.
     *
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $rmaId = $this->getRequest()->getParam('id');
        $collection = $this->reasonFactory
            ->create()
            ->addFieldToFilter('id', $rmaId);
        $item = $collection->getFirstItem();
        $this->coreRegistry->register('rma_reason_data', $item);

        $resultPage = $this->resultPageFactory->create();
        if ($rmaId) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Reason'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('Create Reason'));
        }

        return $resultPage;
    }
}
