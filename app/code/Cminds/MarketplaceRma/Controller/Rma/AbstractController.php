<?php

namespace Cminds\MarketplaceRma\Controller\Rma;

use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class AbstractController
 *
 * @package Cminds\MarketplaceRma\Controller\Rma
 */
abstract class AbstractController extends Action
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * AbstractController constructor.
     *
     * @param Context      $context
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        Context $context,
        ModuleConfig $moduleConfig
    ) {
        $this->moduleConfig = $moduleConfig;

        parent::__construct($context);
    }

    /**
     * Check is module enabled before dispatch.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if ($this->moduleConfig->isActive() === false) {
            if ($this->moduleConfig->isActive() === false) {
                $this->messageManager->addErrorMessage(__('MarketplaceRma is currently disabled in configuration'));
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                return $resultRedirect;
            }
        }
        return parent::dispatch($request);
    }
}
