<?php

namespace Cminds\SupplierSubscription\Controller\Adminhtml\Manage;

use Cminds\SupplierSubscription\Block\Adminhtml\Catalog\Plan\Plans\Form
    as PlanForm;
use Cminds\SupplierSubscription\Model\Plan as PlanModel;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var PlanForm
     */
    protected $planForm;

    /**
     * @var PlanModel
     */
    protected $plan;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Delete constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PlanForm $planForm
     * @param PlanModel $plan
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PlanForm $planForm,
        PlanModel $plan,
        Registry $registry
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->planForm = $planForm;
        $this->plan = $plan;
        $this->registry = $registry;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $planId = $this->getRequest()->getParam('id', false);

        if ($planId) {
            $plan = $this->plan;
            $plan->load($planId);

            if (!$plan->getId()) {
                $this->messageManager->addError(
                    __('This field no longer exists.')
                );
            }

            try {
                $plan->delete();
            } catch (LocalizedException $e) {
                $this->messageManager->addError(
                    __('Can not delete this field.')
                );
            }
        }

        return $this->_redirect('*/*/index');
    }
}
