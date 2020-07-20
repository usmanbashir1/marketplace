<?php

namespace WeltPixel\ProductLabels\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use WeltPixel\ProductLabels\Model\ProductLabelBuilder;


class Labels extends Action
{

    /**
     * @var ProductLabelBuilder
     */
    protected $productLabelBuilder;

    /**
     * Labels constructor.
     * @param Context $context
     * @param ProductLabelBuilder $productLabelBuilder
     */
    public function __construct(
        Context $context,
        ProductLabelBuilder $productLabelBuilder
    ) {
        $this->productLabelBuilder = $productLabelBuilder;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('product_id');
        $prefix = $this->getRequest()->getParam('prefix', 'product');

        if (!$productId) {
            return $this->prepareResult('');
        }
        $productLabels = $this->productLabelBuilder->getLabelForProduct($productId, $prefix);
        return $this->prepareResult($productLabels);
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
