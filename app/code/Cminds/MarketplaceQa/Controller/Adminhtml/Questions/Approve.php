<?php

namespace Cminds\MarketplaceQa\Controller\Adminhtml\Questions;

use Cminds\MarketplaceQa\Model\Qa;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;

class Approve extends Action
{
    /**
     * @var RequestInterface
     */
    protected $request;

    private $resultPageFactory;

    private $qa;

    public function __construct(
        Context $context,
        Qa $qa,
        PageFactory $resultPageFactory,
        RequestInterface $request
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->qa = $qa;
        $this->request = $request;
    }

    public function execute()
    {
        $questionId = $this->request->getParam('id');
        
        try {
            $item = $this->qa->load($questionId);
            $item->setData('approved', true);
            $saved = $item->save();
        } catch (\Exception $e) {
            $saved = $e;
        }

        $this->messageManager->addSuccess('Question has been approved.');

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}