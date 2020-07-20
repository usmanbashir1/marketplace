<?php

namespace Cminds\MarketplaceRma\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Rma
 *
 * @package Cminds\MarketplaceRma\Model
 */
class Rma extends AbstractModel
{
    /**
     * Returns default statuses.
     */
    const RMA_OPEN = 1;
    const RMA_CLOSED = 2;
    const RMA_APPROVED = 3;

    protected function _construct()
    {
        $this->_init(
            'Cminds\MarketplaceRma\Model\ResourceModel\Rma'
        );
    }
}
