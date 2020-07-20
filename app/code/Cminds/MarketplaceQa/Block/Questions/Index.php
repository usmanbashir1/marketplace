<?php

namespace Cminds\MarketplaceQa\Block\Questions;

use Cminds\MarketplaceQa\Helper\Data as QaHelper;
use Cminds\MarketplaceQa\Model\Qa;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ProductFactory;

class Index extends Template
{
    private $registry;
    private $cmindsHelper;
    private $objectManager;
    private $qa;
    private $productFactory;
    private $title = 'Questions & Answers';

    public function __construct(
        Context $context,
        Registry $registry,
        QaHelper $cmindsHelper,
        Qa $qa,
        ProductFactory $productFactory,
        ObjectManagerInterface $objectManagerInterface
    ) {
        parent::__construct($context);

        $this->registry = $registry;
        $this->qa = $qa;
        $this->productFactory = $productFactory;
        $this->cmindsHelper = $cmindsHelper;
        $this->objectManager = $objectManagerInterface;
    }

    public function getQaCollection()
    {
        return $this->qa->getCollection()
            ->addFilter(
                'supplier_id',
                $this->cmindsHelper->getCustomerSession()->getId()
            );
    }

    public function getHelper()
    {
        return $this->cmindsHelper;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getProduct()
    {
        return $this->productFactory->create();
    }
}
