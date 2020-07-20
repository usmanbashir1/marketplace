<?php

namespace Cminds\MarketplacePaypal\Model\ResourceModel\PaymentStatus;

use Cminds\MarketplacePaypal\Model\ResourceModel\PaymentStatus as PaymentStatusResource;
use Cminds\MarketplacePaypal\Model\PaymentStatus;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Statuses Collection
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(PaymentStatus::class, PaymentStatusResource::class);
    }
}
