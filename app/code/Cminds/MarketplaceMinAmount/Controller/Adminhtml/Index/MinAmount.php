<?php

declare(strict_types=1);

namespace Cminds\MarketplaceMinAmount\Controller\Adminhtml\Index;

use \Magento\Customer\Controller\Adminhtml\Index as Index;

/**
 * MinAmount Action
 *
 * @category Cminds
 * @package  MarketplaceMinAmount
 * @author   Cminds Core Team <info@cminds.com>
 */
class MinAmount extends Index
{
    public function execute()
    {
        $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}