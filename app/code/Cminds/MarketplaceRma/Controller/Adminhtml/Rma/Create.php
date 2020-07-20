<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Rma;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Create
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Rma
 */
class Create extends AbstractController
{
    const PATH_MARKETPLACERMA_RMA_CREATE = 'marketplacerma/rma/create';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * Create constructor.
     *
     * @param Context      $context
     * @param PageFactory  $resultPageFactory
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ModuleConfig $moduleConfig
    ) {
        parent::__construct($context, $moduleConfig);

        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute method.
     *
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (isset($params['is_new'])) {
            if ($params['is_new'] == 1) {
                $this->_getSession()->setNewRmaTempData(null);
            }
        }

        if (isset($params['customer_id']) === true
            && isset($params['order_id']) === false
            && isset($params['rma_products']) === false
        ) {
            if ($params['customer_id'] != 0
                && $params['customer_id'] != ''
                && $params['customer_id'] != null
            ) {
                $this->_getSession()->setNewRmaTempData([
                    'step' => 1,
                    'customer_id' => $params['customer_id']
                ]);
            }
        } elseif (isset($params['customer_id']) === false
            && isset($params['order_id']) === true
            && isset($params['rma_products']) === false

        ) {
            $oldRmaTempData = $this->_getSession()->getNewRmaTempData();
            $this->_getSession()->setNewRmaTempData([
                'step' => 2,
                'customer_id' => $oldRmaTempData['customer_id'],
                'order_id' => $params['order_id']
            ]);
        } elseif (isset($params['customer_id']) === false
            && isset($params['order_id']) === false
            && isset($params['rma_products']) === true
        ) {
            $oldRmaTempData = $this->_getSession()->getNewRmaTempData();
            $this->_getSession()->setNewRmaTempData([
                'step' => 3,
                'customer_id' => $oldRmaTempData['customer_id'],
                'order_id' => $oldRmaTempData['order_id'],
                'rma_products' => $params['rma_products']
            ]);
        }

        $resultPage = $this->resultPageFactory->create();

        $resultPage
            ->getConfig()
            ->getTitle()
            ->prepend(__('Create Returns'));

        return $resultPage;
    }
}
