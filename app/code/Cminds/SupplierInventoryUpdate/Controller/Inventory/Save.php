<?php

namespace Cminds\SupplierInventoryUpdate\Controller\Inventory;

use Cminds\SupplierInventoryUpdate\Helper\Data as UpdaterHelper;
use Cminds\SupplierInventoryUpdate\Model\InventoryUpdateFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Cminds SupplierInventoryUpdate controller that saves supplier inventory
 * updater details.
 *
 * @category Cminds
 * @package  Cminds_SupplierInventoryUpdate
 * @author   Mateusz Niziolek
 */
class Save extends Action
{
    private $logger;
    private $updaterHelper;
    private $inventoryUpdateFactory;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        UpdaterHelper $updaterHelper,
        InventoryUpdateFactory $inventoryUpdateFactory
    ) {
        $this->inventoryUpdateFactory = $inventoryUpdateFactory;
        $this->updaterHelper = $updaterHelper;
        $this->logger = $logger;

        parent::__construct($context);
    }

    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        $supplier = $this->updaterHelper->getSupplier();

        try {
            $model = $this->inventoryUpdateFactory->create();
            $model->load($supplier->getId(), 'supplier_id');
            $model->setData('supplier_id', $supplier->getId());
            $model->setData('updater_csv_link', $postData['updater_csv_link']);
            $model->setData(
                'updater_csv_column',
                $postData['updater_csv_column']
            );
            $model->setData(
                'updater_qty_column',
                $postData['updater_qty_column']
            );
            $model->setData(
                'updater_csv_action',
                $postData['updater_csv_action']
            );
            $model->setData(
                'updater_csv_attribute',
                $postData['updater_csv_attribute']
            );
            $model->setData(
                'updater_csv_delimiter',
                $postData['updater_csv_delimiter']
            );
            $model->setData(
                'updater_cost_column',
                $postData['updater_cost_column']
            );
            $model->save();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}
