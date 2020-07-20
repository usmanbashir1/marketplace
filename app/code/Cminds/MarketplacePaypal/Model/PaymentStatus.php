<?php

namespace Cminds\MarketplacePaypal\Model;

use Magento\Framework\Model\AbstractModel;
use Cminds\MarketplacePaypal\Model\ResourceModel\PaymentStatus as PaymentStatusResource;

/**
 * Payment Status Model
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class PaymentStatus extends AbstractModel
{
    const PENDING = 'PENDING';
    const PROCESSING = 'PROCESSING';
    const DENIED = 'DENIED';
    const SUCCESS = 'SUCCESS';
    const CANCELED = 'CANCELED';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(PaymentStatusResource::class);
    }
}
