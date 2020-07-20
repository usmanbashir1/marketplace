<?php

namespace Cminds\MarketplaceQa\Controller\Adminhtml\Questions;

use Cminds\MarketplaceQa\Model\ResourceModel\Qa\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    private $resultPageFactory;
    private $qaFactory;
    private $coreRegistry;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CollectionFactory $qaFactory,
        Registry $coreRegistry
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->qaFactory = $qaFactory;
        $this->coreRegistry = $coreRegistry;
    }

    public function execute()
    {
        $questionId = $this->getRequest()->getParam('id');
        $collection = $this->qaFactory->create()
            ->addFieldToFilter('id', $questionId);
        $item = $collection->getFirstItem();

        $this->coreRegistry->register('question_data', $item);
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}