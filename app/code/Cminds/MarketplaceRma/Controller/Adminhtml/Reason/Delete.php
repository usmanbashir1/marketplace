<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Reason;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Model\Reason;
use Cminds\MarketplaceRma\Model\ResourceModel\Rma\CollectionFactory as RmaCollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Delete
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Reason
 */
class Delete extends AbstractController
{
    const PATH_MARKETPLACERMA_REASON_INDEX = 'marketplacerma/reason/index';

    /**
     * @var Reason
     */
    private $reason;

    /**
     * @var RmaCollectionFactory
     */
    private $rmaCollectionFactory;

    /**
     * Delete constructor.
     *
     * @param Context              $context
     * @param Reason               $reason
     * @param ModuleConfig         $moduleConfig
     * @param RmaCollectionFactory $rmaCollectionFactory
     */
    public function __construct(
        Context $context,
        Reason $reason,
        ModuleConfig $moduleConfig,
        RmaCollectionFactory $rmaCollectionFactory
    ) {
        parent::__construct($context, $moduleConfig);

        $this->reason = $reason;
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

            $rmaCollection = $this->rmaCollectionFactory
                ->create()
                ->addFieldToFilter('reason', $data['id'])
                ->count();

            if ($rmaCollection > 0) {
                $this->messageManager->addErrorMessage(
                    __('One or more Returns request is using this reason.')
                );

                return $this->_redirect(self::PATH_MARKETPLACERMA_REASON_INDEX);
            }

            try {
                $model = $this->reason->load($data['id']);
                $model->delete();
                $this->messageManager->addSuccessMessage(
                    __('Reason was successfully deleted.')
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $this->_redirect(self::PATH_MARKETPLACERMA_REASON_INDEX);
    }
}
