<?php
namespace Cminds\MultipleProductVendors\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Manufacturer extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('product_manufacturer_codes', 'id');
    }
}
