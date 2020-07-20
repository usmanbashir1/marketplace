<?php

namespace Cminds\MarketplaceQa\Block\Questions;

use Cminds\MarketplaceQa\Helper\Data as QaHelper;
use Cminds\MarketplaceQa\Model\Qa;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Answer extends Template
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

    public function getQuestion()
    {
        $data = $this->getRequest()->getParam('id');

        return $this->qa->load($data);
    }
}
