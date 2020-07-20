<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml;

use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Backend\App\Action;

/**
 * Class AbstractController
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml
 */
abstract class AbstractController extends Action
{
    const PATH_ADMIN_DASHBOARD = 'admin/dashboard/index';

    /**
     * MarketplaceRma admin resources.
     */
    const MARKETPLACERMA_RESOURCE_RMA_MAIN_CONTENT= 'Cminds_MarketplaceRma::rma_content';

    const MARKETPLACERMA_RESOURCE_RMA_LIST = 'Cminds_MarketplaceRma::rma_list';

    const MARKETPLACERMA_RESOURCE_MANAGE_STATUS = 'Cminds_MarketplaceRma::rma_manage_status';

    const MARKETPLACERMA_RESOURCE_MANAGE_TYPE = 'Cminds_MarketplaceRma::rma_manage_type';

    const MARKETPLACERMA_RESOURCE_MANAGE_REASONS = 'Cminds_MarketplaceRma::rma_manage_reasons';

    /**
     * MarketplaceRma admin controllers.
     */
    const MARKETPLACERMA_ACTION_RMA_LIST = 'rma';

    const MARKETPLACERMA_ACTION_STATUS_LIST = 'status';

    const MARKETPLACERMA_ACTION_TYPE_LIST = 'type';

    const MARKETPLACERMA_ACTION_REASON_LIST = 'reason';

    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * AbstractController constructor.
     *
     * @param Action\Context $context
     * @param ModuleConfig   $moduleConfig
     */
    public function __construct(
        Action\Context $context,
        ModuleConfig $moduleConfig
    ) {
        $this->moduleConfig = $moduleConfig;

        parent::__construct($context);
    }

    /**
     * Before going to parent dispatch method we need to check is module enabled.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if ($this->moduleConfig->isActive() === false) {
            $this->messageManager->addErrorMessage(
                __('MarketplaceRma is currently disabled in configuration.')
            );

            return $this->_redirect(self::PATH_ADMIN_DASHBOARD);
        }

        if ($this->_isAllowed() === false) {
            $this->messageManager->addErrorMessage(
                __('You do not have access to this section.')
            );

            return $this->_redirect(self::PATH_ADMIN_DASHBOARD);
        }

        return parent::dispatch($request);
    }

    /**
     * Check is user has access to the specified resource.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getControllerName()) {
            case self::MARKETPLACERMA_ACTION_RMA_LIST:
                return $this->_authorization->isAllowed(self::MARKETPLACERMA_RESOURCE_RMA_LIST);
            case self::MARKETPLACERMA_ACTION_STATUS_LIST:
                return $this->_authorization->isAllowed(self::MARKETPLACERMA_RESOURCE_MANAGE_STATUS);
            case self::MARKETPLACERMA_ACTION_TYPE_LIST:
                return $this->_authorization->isAllowed(self::MARKETPLACERMA_RESOURCE_MANAGE_TYPE);
            case self::MARKETPLACERMA_ACTION_REASON_LIST:
                return $this->_authorization->isAllowed(self::MARKETPLACERMA_RESOURCE_MANAGE_REASONS);
            default:
                return $this->_authorization->isAllowed(self::MARKETPLACERMA_RESOURCE_RMA_MAIN_CONTENT);
        }
    }
}
