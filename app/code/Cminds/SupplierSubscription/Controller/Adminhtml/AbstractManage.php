<?php

namespace Cminds\SupplierSubscription\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class AbstractManage extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Cminds_SupplierSubscription::manage_supplier_subscriptions';
}
