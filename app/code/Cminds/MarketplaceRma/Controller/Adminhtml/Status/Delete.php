<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Status;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Model\Status;
use Cminds\MarketplaceRma\Model\Rma;
use Cminds\MarketplaceRma\Model\ResourceModel\Rma\CollectionFactory as RmaCollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Delete
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Status
 */
class Delete extends AbstractController
{
    const PATH_MARKETPLACERMA_STATUS_INDEX = 'marketplacerma/status/index';

    /**
     * @var Status
     */
    private $status;

    /**
     * @var RmaCollectionFactory
     */
    private $rmaCollectionFactory;

    /**
     * Delete constructor.
     *
     * @param Context              $context
     * @param Status               $status
     * @param ModuleConfig         $moduleConfig
     * @param RmaCollectionFactory $rmaCollectionFactory
     */
    public function __construct(
        Context $context,
        Status $status,
        ModuleConfig $moduleConfig,
        RmaCollectionFactory $rmaCollectionFactory
    ) {
        parent::__construct($context, $moduleConfig);

        $this->status = $status;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
    }

    /**
     * Execute method.
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if ($this->_request->getParams()
            && $this->_request->getParam('id') !== ''
            && $this->_request->getParam('id') !== null
        ) {
            $data = $this->_request->getParams();

            if ($data['id'] != Rma::RMA_CLOSED && $data['id'] != Rma::RMA_OPEN) {
                $rmaCollection = $this->rmaCollectionFactory
                    ->create()
                    ->addFieldToFilter('status', $data['id'])
                    ->count();

                if ($rmaCollection > 0) {
                    $this->messageManager->addErrorMessage(
                        __('One or more Returns request is using this status.')
                    );

                    return $this->_redirect(self::PATH_MARKETPLACERMA_STATUS_INDEX);
                }

                try {
                    $model = $this->status->load($data['id']);
                    $model->delete();
                    $this->messageManager->addSuccessMessage(
                        __('Status was successfully deleted.')
                    );
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            } elseif ($data['id'] == Rma::RMA_CLOSED || $data['id'] == Rma::RMA_OPEN) {
                $this->messageManager->addNoticeMessage(
                    __('You can not delete default status.')
                );
            }
        }

        return $this->_redirect(self::PATH_MARKETPLACERMA_STATUS_INDEX);
    }
}
