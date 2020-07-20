<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Type;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\Type;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Model\ResourceModel\Rma\CollectionFactory as RmaCollectionFactory;
use Magento\Backend\App\Action\Context;

/**
 * Class Delete
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Type
 */
class Delete extends AbstractController
{
    const PATH_MARKETPLACERMA_TYPE_INDEX = 'marketplacerma/type/index';

    /**
     * @var Type
     */
    private $type;

    /**
     * @var RmaCollectionFactory
     */
    private $rmaCollectionFactory;

    /**
     * Delete constructor.
     *
     * @param Context              $context
     * @param Type                 $type
     * @param ModuleConfig         $moduleConfig
     * @param RmaCollectionFactory $rmaCollectionFactory
     */
    public function __construct(
        Context $context,
        Type $type,
        ModuleConfig $moduleConfig,
        RmaCollectionFactory $rmaCollectionFactory
    ) {
        parent::__construct($context, $moduleConfig);

        $this->type = $type;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
    }

    /**
     * Execute method.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
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
                ->addFieldToFilter('request_type', $data['id'])
                ->count();

            if ($rmaCollection > 0) {
                $this->messageManager->addErrorMessage(
                    __('One or more Returns request is using this request type.')
                );

                return $this->_redirect(self::PATH_MARKETPLACERMA_TYPE_INDEX);
            }

            try {
                $model = $this->type->load($data['id']);
                $model->delete();
                $this->messageManager->addSuccessMessage(
                    __('Type was successfully deleted.')
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $this->_redirect(self::PATH_MARKETPLACERMA_TYPE_INDEX);
    }
}
