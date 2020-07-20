<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Type;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\ResourceModel\Type\CollectionFactory;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

/**
 * Class Edit
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Type
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
    private $typeFactory;

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
     * @param CollectionFactory $typeFactory
     * @param ModuleConfig      $moduleConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        CollectionFactory $typeFactory,
        ModuleConfig $moduleConfig
    ) {
        parent::__construct($context, $moduleConfig);

        $this->resultPageFactory = $resultPageFactory;
        $this->typeFactory = $typeFactory;
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
        $collection = $this->typeFactory
            ->create()
            ->addFieldToFilter('id', $rmaId);
        $item = $collection->getFirstItem();
        $this->coreRegistry->register('rma_type_data', $item);

        $resultPage = $this->resultPageFactory->create();

        if ($rmaId) {
            $resultPage
                ->getConfig()
                ->getTitle()
                ->prepend(__('Edit Type'));
        } else {
            $resultPage
                ->getConfig()
                ->getTitle()
                ->prepend(__('Create Type'));
        }

        return $resultPage;
    }
}
