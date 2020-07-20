<?php

declare(strict_types=1);

namespace Cminds\MarketplaceMinAmount\Observer\Adminhtml\CustomerSave;

use Cminds\Marketplace\Observer\Adminhtml\CustomerSave\CustomerSaveAbstract;
use Cminds\Marketplace\Helper\Data as MarketplaceHelper;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;

/**
 * Save MinOrder Amount settings for supplier
 *
 * @category Cminds
 * @package  MarketplaceMinAmount
 * @author   Cminds Core Team <info@cminds.com>
 */
class SaveMinOrderAmount extends CustomerSaveAbstract
{
    /**
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * @var MarketplaceHelper
     */
    protected $marketplaceHelper;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var CustomerResource
     */
    protected $_customerResource;

    /**
     * SaveMinOrderAmount constructor.
     *
     * @param MarketplaceHelper $marketplaceHelper
     * @param Context $context
     */
    public function __construct(
        MessageManager $messageManager,
        MarketplaceHelper $marketplaceHelper,
        CustomerFactory $customerFactory,
        CustomerResource $customerResource,
        Context $context
    ) {
        parent::__construct($marketplaceHelper, $context);

        $this->messageManager = $messageManager;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->_customerFactory = $customerFactory;
        $this->_customerResource = $customerResource;
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $supplierId = $observer->getCustomer()->getId();
        if (!$supplierId) {
            return $this;
        }

        $customer = $this->_customerFactory->create();
        $this->_customerResource->load($customer, $supplierId);

        $postData = $this->request->getParams();
        try {
            if (isset($postData['supplier_min_order_amount'])) {
                $customer->setData('supplier_min_order_amount', $postData['supplier_min_order_amount']);
            }

            if (isset($postData['supplier_min_order_qty'])) {
                $customer->setData('supplier_min_order_qty', $postData['supplier_min_order_qty']);
            }

            if (isset($postData['supplier_min_order_amount_per'])) {
                $customer->setData('supplier_min_order_amount_per', $postData['supplier_min_order_amount_per']);
            }

            $this->_customerResource->save($customer);

        } catch (LocalizedException $e) {
            $this->logger->info($e->getMessage());
        }
    }
}