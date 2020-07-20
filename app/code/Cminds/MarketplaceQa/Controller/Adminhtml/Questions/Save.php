<?php

namespace Cminds\MarketplaceQa\Controller\Adminhtml\Questions;

use Cminds\MarketplaceQa\Model\QaFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;

class Save extends Action
{
    /**
     * @var RequestInterface
     */
    protected $request;

    private $qaFactory;

    public function __construct(
        Context $context,
        QaFactory $qaFactory,
        RequestInterface $request
    ) {
        parent::__construct($context);

        $this->qaFactory = $qaFactory;
        $this->request = $request;
    }

    public function execute()
    {
        $post = $this->request->getParams();
        $questionId = $this->request->getParam('id');
        
        try {
            $item = $this->qaFactory->create()
                ->load($questionId);

            if ((int)$post['approved'] === 1 && $post['answer'] === '') {
                $this->messageManager->addErrorMessage(
                    __('Answer has been not provided, entry can not be approved.')
                );
                $post['approved'] = false;
            } else {
                $this->messageManager->addSuccessMessage(
                    __('Data successfully saved.')
                );
            }

            $item
                ->setData($post)
                ->save();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('An error occurred.')
            );
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}