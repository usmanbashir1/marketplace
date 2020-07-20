<?php

namespace Cminds\SupplierInventoryUpdate\Model\Update;

use Cminds\SupplierInventoryUpdate\Helper\Data as UpdaterHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class AbstractUpdate extends AbstractModel
{
    private $updaterHelper;

    public function __construct(
        Context $context,
        Registry $registry,
        UpdaterHelper $updaterHelper
    ) {
        parent::__construct(
            $context,
            $registry
        );

        $this->updaterHelper = $updaterHelper;
    }
}
