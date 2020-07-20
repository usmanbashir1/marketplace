<?php
namespace Cminds\MultipleProductVendors\Model\ResourceModel\Manufacturer;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Cminds\MultipleProductVendors\Model\Manufacturer',
            'Cminds\MultipleProductVendors\Model\ResourceModel\Manufacturer'
        );
    }
}
