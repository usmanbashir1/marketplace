<?php

namespace Cminds\MarketplaceQa\Block\Questions;

use Cminds\MarketplaceQa\Helper\Data as QaHelper;
use Cminds\MarketplaceQa\Model\Qa;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Count extends Template
{
    protected $registry;
    protected $cmindsHelper;
    protected $qa;
    protected $productFactory;
    protected $title = 'Manage Your Answer';

    public function __construct(
        Context $context,
        Registry $registry,
        QaHelper $cmindsHelper,
        Qa $qa,
        ProductFactory $productFactory
    ) {
        parent::__construct($context);

        $this->registry = $registry;
        $this->qa = $qa;
        $this->productFactory = $productFactory;
        $this->cmindsHelper = $cmindsHelper;
    }

    public function getQaCollection()
    {
        $product = $this->registry->registry('product');

        return $this->qa->getCollection()
            ->addFilter('product_id', $product->getId())
            ->addFilter('approved', 1)
            ->addFilter('visible_on_frontend', 1);
    }

    public function getProduct()
    {
        return $this->registry->registry('product');
    }
}
