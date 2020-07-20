<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Status;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;

/**
 * Class Create
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Status
 */
class Create extends AbstractController
{
    /**
     * Execute method.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
