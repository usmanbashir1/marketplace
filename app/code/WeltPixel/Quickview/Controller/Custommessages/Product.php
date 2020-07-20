<?php

namespace WeltPixel\Quickview\Controller\Custommessages;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use WeltPixel\Quickview\Model\CustommessageBuilder;


class Product extends Action
{

    /**
     * @var CustommessagesBuilder
     */
    protected $custommesageBuilder;

    /**
     * Labels constructor.
     * @param Context $context
     * @param CustommessageBuilder $custommesageBuilder
     */
    public function __construct(
        Context $context,
        CustommessageBuilder $custommesageBuilder
    ) {
        $this->custommesageBuilder = $custommesageBuilder;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('product_id');
        if (!$productId) {
            return $this->prepareResult('');
        }

        $customMessage = $this->custommesageBuilder->getCustomMessageForProduct($productId);
        return $this->prepareResult([
            'html' => $customMessage
        ]);
    }

    /**
     * @param array $result
     * @return string
     */
    protected function prepareResult($result)
    {
        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}
