<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Rma;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Model\ResourceModel\Rma\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

/**
 * Class Edit
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Rma
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
    private $rmaFactory;

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
     * @param CollectionFactory $rmaFactory
     * @param ModuleConfig      $moduleConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        CollectionFactory $rmaFactory,
        ModuleConfig $moduleConfig
    ) {
        parent::__construct($context, $moduleConfig);

        $this->resultPageFactory = $resultPageFactory;
        $this->rmaFactory = $rmaFactory;
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
        $collection = $this->rmaFactory
            ->create()
            ->addFieldToFilter('id', $rmaId);
        $item = $collection->getFirstItem();
        $this->coreRegistry->register('rma_data', $item);

        $resultPage = $this->resultPageFactory->create();
        if ($rmaId) {
            $resultPage
                ->getConfig()
                ->getTitle()
                ->prepend(__('Edit Returns'));
        } else {
            $resultPage
                ->getConfig()
                ->getTitle()
                ->prepend(__('Create Returns'));
        }

        return $resultPage;
    }
}
