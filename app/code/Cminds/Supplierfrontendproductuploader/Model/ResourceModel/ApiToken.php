<?php

namespace Cminds\Supplierfrontendproductuploader\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ApiToken extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            'supplierfrontendproductuploader_customer_apitoken',
            'entity_id'
        );
    }
}
