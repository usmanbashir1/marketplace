<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Type;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Create
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Type
 */
class Create extends AbstractController
{
    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
