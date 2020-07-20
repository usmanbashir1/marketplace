<?php

namespace Cminds\Supplierfrontendproductuploader\Model\ResourceModel\ApiToken;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\Supplierfrontendproductuploader\Model\ApiToken',
            'Cminds\Supplierfrontendproductuploader\Model\ResourceModel\ApiToken'
        );
    }
}
