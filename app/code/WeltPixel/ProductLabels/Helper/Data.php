<?php

namespace WeltPixel\ProductLabels\Helper;

use Magento\Framework\App\Helper\Context;
use WeltPixel\ProductLabels\Model\ProductLabelBuilder;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var ProductLabelBuilder
     */
    protected $productLabelBuilder;

    /**
     * Data constructor.
     * @param Context $context
     * @param ProductLabelBuilder $productLabelBuilder
     */
    public function __construct(
        Context $context,
        ProductLabelBuilder $productLabelBuilder
    )
    {
        parent::__construct($context);
        $this->productLabelBuilder = $productLabelBuilder;
    }

    /**
     * @param $productId
     * @return string
     */
    public function getLabelsOnCategoryPage($productId)
    {
        return $this->productLabelBuilder->getLabelsOnCategoryPage($productId);
    }

    /**
     * @param $productId
     * @return string
     */
    public function getLabelsOnProductPage($productId)
    {
        return $this->productLabelBuilder->getLabelsOnProductPage($productId);
    }
}
