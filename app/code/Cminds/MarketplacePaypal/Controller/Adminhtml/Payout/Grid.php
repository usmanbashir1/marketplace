<?php

namespace Cminds\MarketplacePaypal\Controller\Adminhtml\Payout;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Grid Controller
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class Grid extends Action
{
    const ADMIN_RESOURCE = 'MarketplacePaypal::billing_report_pay_paypal';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * Grid constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute
     *
     * @return Page|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Cminds_Marketplace::payout_status')
            ->addBreadcrumb(__('Payout Payments'), __('Payout Payments'))
            ->getConfig()->getTitle()->prepend(__('Payout payments'));

        return $resultPage;
    }
}
