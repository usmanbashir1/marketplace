<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Rma;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Model\Rma;
use Cminds\MarketplaceRma\Model\ResourceModel\Note\Collection as NoteCollection;
use Cminds\MarketplaceRma\Model\ResourceModel\CustomerAddress\Collection as CustomerAddressCollection;
use Cminds\MarketplaceRma\Model\ResourceModel\ReturnProduct\Collection as ReturnProductCollection;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Delete
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Rma
 */
class Delete extends AbstractController
{
    const PATH_MARKETPLACERMA_RMA_INDEX = 'marketplacerma/rma/index';

    /**
     * @var Rma
     */
    private $rma;

    /**
     * @var CustomerAddressCollection
     */
    private $customerAddressCollection;

    /**
     * @var NoteCollection
     */
    private $noteCollection;

    /**
     * @var ReturnProductCollection
     */
    private $returnProductCollection;

    /**
     * Delete constructor.
     *
     * @param Context                   $context
     * @param Rma                       $rma
     * @param ModuleConfig              $moduleConfig
     * @param CustomerAddressCollection $customerAddressCollection
     * @param NoteCollection            $noteCollection
     * @param ReturnProductCollection   $returnProductCollection
     */
    public function __construct(
        Context $context,
        Rma $rma,
        ModuleConfig $moduleConfig,
        CustomerAddressCollection $customerAddressCollection,
        NoteCollection $noteCollection,
        ReturnProductCollection $returnProductCollection
    ) {
        parent::__construct($context, $moduleConfig);

        $this->rma = $rma;
        $this->customerAddressCollection = $customerAddressCollection;
        $this->noteCollection = $noteCollection;
        $this->returnProductCollection = $returnProductCollection;
    }

    /**
     * Execute method.
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if ($this->_request->getParams()) {
            $data = $this->_request->getParams();

            if ($data['id'] != '') {
                try {
                    $model = $this->rma->load($data['id']);

                    if (($model->getData('status') == Rma::RMA_CLOSED)
                        || ($model->getData('status') == Rma::RMA_APPROVED)
                    ) {
                        $this->messageManager->addErrorMessage(
                            __('This Returns is already processed. You can not delete this request.')
                        );

                        return $this->_redirect(self::PATH_MARKETPLACERMA_RMA_INDEX);
                    }

                    // delete related customer address
                    $customerAddressCollection = $this->customerAddressCollection
                        ->addFieldToFilter('rma_id', $model->getData('id'))
                        ->getItems();

                    foreach ($customerAddressCollection as $item) {
                        $item->delete();
                    }

                    // deleted related notes
                    $noteCollection = $this->noteCollection
                        ->addFieldToFilter('rma_id', $model->getData('id'))
                        ->getItems();

                    foreach ($noteCollection as $item) {
                        $item->delete();
                    }

                    // delete related products
                    $returnProductCollection = $this->returnProductCollection
                        ->addFieldToFilter('order_id', $model->getData('order_id'))
                        ->getItems();

                    foreach ($returnProductCollection as $item) {
                        $item->delete();
                    }

                    // delete main Returns model
                    $model->delete();
                    $this->messageManager->addSuccessMessage(__('Returns was successfully deleted.'));
                    return $this->_redirect(self::PATH_MARKETPLACERMA_RMA_INDEX);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
        }

        return $this->_redirect(self::PATH_MARKETPLACERMA_RMA_INDEX);
    }
}
