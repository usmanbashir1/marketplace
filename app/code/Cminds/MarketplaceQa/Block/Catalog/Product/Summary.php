<?php

namespace Cminds\MarketplaceQa\Block\Catalog\Product;

use Cminds\MarketplaceQa\Helper\Data as QaHelper;
use Cminds\MarketplaceQa\Model\ResourceModel\Qa\CollectionFactory;
use Magento\Framework\Registry as CoreRegistry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Summary extends Template
{
    private $coreRegistry;
    private $collectionFactory;
    private $cmindsHelper;

    public function __construct(
        Context $context,
        CoreRegistry $coreRegistry,
        CollectionFactory $collectionFactory,
        QaHelper $cmindsHelper
    ) {
        parent::__construct($context);

        $this->coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;
        $this->cmindsHelper = $cmindsHelper;
    }

    public function getQaCollection()
    {
        $product = $this->getProduct();

        $collection = $this->collectionFactory->create();
        $collection
            ->addFilter('product_id', $product->getId())
            ->addFilter('approved', 1)
            ->addFilter('visible_on_frontend', 1);

        return $collection;
    }

    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    protected function _toHtml()
    {
        if (!$this->cmindsHelper->marketplaceQaEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }
}
