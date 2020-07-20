<?php

namespace Cminds\SupplierInventoryUpdate\Plugin\Customer\Controller\Adminhtml\Index;

use Cminds\Marketplace\Helper\Data as MarketplaceHelper;
use Cminds\SupplierInventoryUpdate\Helper\Data;
use Cminds\SupplierInventoryUpdate\Model\InventoryUpdateFactory;
use Magento\Customer\Controller\Adminhtml\Index\Save as SaveController;
use Magento\Framework\App\Request\Http;
use Psr\Log\LoggerInterface;

class Save
{
    private $dataHelper;
    private $request;
    private $logger;
    private $inventoryUpdateFactory;
    private $marketplaceHelper;

    public function __construct(
        Data $dataHelper,
        MarketplaceHelper $marketplaceHelper,
        LoggerInterface $logger,
        Http $request,
        InventoryUpdateFactory $inventoryUpdateFactory
    ) {
        $this->marketplaceHelper = $marketplaceHelper;
        $this->dataHelper = $dataHelper;
        $this->request = $request;
        $this->inventoryUpdateFactory = $inventoryUpdateFactory;
        $this->logger = $logger;
    }

    public function getPost()
    {
        return $this->request->getPost();
    }

    public function beforeExecute(SaveController $subject)
    {
        $params = $this->request->getParams();

        if (isset($params['customer']['entity_id'])) {
            $id = $params['customer']['entity_id'];
            if ($this->dataHelper->isEnabled()) {
                if ($this->marketplaceHelper->isSupplier($id)) {
                    try {
                        if ($params['updater_csv_link']) {
                            $model = $this->inventoryUpdateFactory->create()
                                ->load($id, 'supplier_id');

                            $model->setData('supplier_id', $id);
                            $model->setData(
                                'updater_csv_link',
                                $params['updater_csv_link']
                            );
                            $model->setData(
                                'updater_csv_column',
                                $params['updater_csv_column']
                            );
                            $model->setData(
                                'updater_qty_column',
                                $params['updater_qty_column']
                            );
                            $model->setData(
                                'updater_csv_action',
                                $params['updater_csv_action']
                            );
                            $model->setData(
                                'updater_csv_attribute',
                                $params['updater_csv_attribute']
                            );
                            $model->setData(
                                'updater_csv_delimiter',
                                $params['updater_csv_delimiter']
                            );
                            $model->setData(
                                'updater_cost_column',
                                $params['updater_cost_column']
                            );
                            $model->save();
                        }
                    } catch (\Exception $e) {
                        $this->logger->critical($e->getMessage());
                    }
                }
            }
        }
    }
}
