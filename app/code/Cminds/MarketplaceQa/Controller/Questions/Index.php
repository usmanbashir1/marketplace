<?php

namespace Cminds\MarketplaceQa\Controller\Questions;

use Cminds\Marketplace\Helper\Data as MarketplaceHelper;
use Cminds\Supplierfrontendproductuploader\Controller\AbstractController;
use Cminds\MarketplaceQa\Model\Qa;
use Cminds\Supplierfrontendproductuploader\Helper\Data as SupplierHelper;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;

class Index extends AbstractController
{
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @var MarketplaceHelper
     */
    private $marketplaceHelper;

    /**
     * @var InvoiceSender
     */
    private $invoiceSender;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var SupplierHelper
     */
    private $supplierHelper;

    private $qa;

    public function __construct(
        Context $context,
        Transaction $transaction,
        MarketplaceHelper $marketplaceHelper,
        SupplierHelper $supplierHelper,
        CustomerSession $customerSession,
        OrderFactory $orderFactory,
        InvoiceSender $invoiceSender,
        ItemFactory $itemFactory,
        StoreManagerInterface $storeManager,
        Qa $qa,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct(
            $context,
            $supplierHelper,
            $storeManager,
            $scopeConfig
        );

        $this->transaction = $transaction;
        $this->orderFactory = $orderFactory;
        $this->itemFactory = $itemFactory;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->invoiceSender = $invoiceSender;
        $this->customerSession = $customerSession;
        $this->qa = $qa;
    }

    public function execute()
    {
        if (!$this->canAccess()) {
            return $this->redirectToLogin();
        }
        
        $this->_view->loadLayout();
        $this->renderBlocks();
        $this->_view->renderLayout();
    }
}
