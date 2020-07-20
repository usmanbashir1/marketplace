<?php

namespace Cminds\MarketplaceQa\Controller\Adminhtml\Questions;

use Cminds\MarketplaceQa\Model\Qa;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Changestatus extends Action
{
    private $resultPageFactory;
    private $qa;

    public function __construct(
        Context $context,
        Qa $qa,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->qa = $qa;
    }

    public function execute()
    {
        $questionId = $this->request->getParam('id');
        $visibility = $this->request->getParam('value');

        try {
            $item = $this->qa->load($questionId);
            $item->setData('approved', $visibility);
            $saved = $item->save();
        } catch (\Exception $e) {
            $saved = $e;
        }

        echo json_encode($saved);
    }
}