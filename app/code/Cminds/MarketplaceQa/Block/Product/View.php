<?php

namespace Cminds\MarketplaceQa\Block\Product;

use Cminds\MarketplaceQa\Helper\Data as QaHelper;
use Cminds\MarketplaceQa\Model\ResourceModel\Qa\CollectionFactory;
use Magento\Framework\Registry as CoreRegistry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class View extends Template
{
    private $coreRegistry;
    private $cmindsHelper;
    private $collectionFactory;

    private $collection;

    public function __construct(
        Context $context,
        CoreRegistry $coreRegistry,
        QaHelper $cmindsHelper,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);

        $this->coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;
        $this->cmindsHelper = $cmindsHelper;
    }

    public function getQaCollection()
    {
        if ($this->collection === null) {
            $product = $this->getProduct();

            $collection = $this->collectionFactory->create();
            $collection
                ->addFilter('visible_on_frontend', 1)
                ->addFilter('product_id', $product->getId());

            $collection
                ->getSelect()
                ->where('answer != ?', '');

            if ($this->cmindsHelper->adminApprovalRequired()) {
                $collection->addFilter('approved', 1);
            }

            $this->collection = $collection;
        }

        return $this->collection;
    }

    public function getCustomer()
    {
        return $this->cmindsHelper->getCustomerSession();
    }

    public function getHelper()
    {
        return $this->cmindsHelper;
    }

    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    public function canDisplayForm()
    {
        $questionsLimit = $this->getHelper()->getMaxQuestion();
        if ($questionsLimit === 0) {
            return true;
        }

        $questionsCount = $this->getQaCollection()->getSize();

        return $questionsCount < $questionsLimit;
    }

    protected function _toHtml() // @codingStandardsIgnoreLine
    {
        if (!$this->cmindsHelper->questionFormVisible()) {
            return '';
        }

        return parent::_toHtml();
    }
}
