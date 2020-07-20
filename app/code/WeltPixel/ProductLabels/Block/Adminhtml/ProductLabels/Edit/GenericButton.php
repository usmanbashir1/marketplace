<?php
namespace WeltPixel\ProductLabels\Block\Adminhtml\ProductLabels\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \WeltPixel\ProductLabels\Model\ProductLabelsFactory
     */
    protected $productLabelsFactory;

    /**
     * @param Context $context
     * @param \WeltPixel\ProductLabels\Model\ProductLabelsFactory $productLabelsFactory
     */
    public function __construct(
        Context $context,
        \WeltPixel\ProductLabels\Model\ProductLabelsFactory $productLabelsFactory
    ) {
        $this->context = $context;
        $this->productLabelsFactory = $productLabelsFactory;
    }

    /**
     * Return item ID
     *
     * @return int|null
     */
    public function getProductLabelId()
    {
        try {
            /** @var \WeltPixel\ProductLabels\Model\ProductLabels $productLabel */
            $productLabel = $this->productLabelsFactory->create();
            return $productLabel->load($this->context->getRequest()->getParam('id'))->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
