<?php

namespace Cminds\SupplierSubscription\Controller;

use Cminds\Supplierfrontendproductuploader\Controller\AbstractController;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

abstract class Plan extends AbstractController
{
    /**
     * Plan constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param StoreManagerInterface $storeManage
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        Data $helper,
        StoreManagerInterface $storeManage,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct(
            $context,
            $helper,
            $storeManage,
            $scopeConfig
        );
    }
}
