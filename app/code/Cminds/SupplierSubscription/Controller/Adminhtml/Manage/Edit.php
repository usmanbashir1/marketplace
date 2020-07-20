<?php

namespace Cminds\SupplierSubscription\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;
use Cminds\SupplierSubscription\Block\Adminhtml\Catalog\Plan\Plans\Form
    as SubscriptionForm;
use Cminds\SupplierSubscription\Model\Plan as PlanModel;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;

class Edit extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var SubscriptionForm
     */
    protected $subscriptionForm;

    /**
     * @var PlanModel
     */
    protected $plan;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ProductFactory
     */
    protected $productLoader;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param SubscriptionForm $subscriptionForm
     * @param PlanModel $plan
     * @param Registry $registry
     * @param ProductFactory $productLoader
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        SubscriptionForm $subscriptionForm,
        PlanModel $plan,
        Registry $registry,
        ProductFactory $productLoader,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->subscriptionForm = $subscriptionForm;
        $this->plan = $plan;
        $this->registry = $registry;
        $this->productLoader = $productLoader;
        $this->storeManager = $storeManager;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $plan = $this->plan;
        $planId = $this->getRequest()->getParam('entity_id', false);
        if ($planId) {
            $plan->load($planId);

            if (!$plan->getId()) {
                $this->messageManager->addError(
                    __('This plan no longer exists.')
                );

                return $this->_redirect(
                    '*/*/index'
                );
            }
        }

        $postData = $this->getRequest()->getParam('fieldData');
        if ($postData) {
            try {
                if (!$plan->getId()) {
                    $postData['created_at'] = date('Y-m-d H:i:s');
                }
                $nameExists = $this->plan->load($postData['name'], 'name');

                if ($nameExists->getId()
                    && !$this->getRequest()->getParam('entity_id', false)
                ) {
                    throw new LocalizedException(
                        __('Plan with this name already exists.')
                    );
                }

                $plan->addData($postData);
                $plan->save();

                if (!$plan->validateVirtualProduct()) {
                    $planProduct = $this->createVirtualProduct($plan);
                } else {
                    $planProduct = $this->updateVirtualProduct($plan);
                }

                $plan->setProductId($planProduct->getId());
                $plan->save();

                $this->messageManager->addSuccess(
                    __('The Plan has been saved.')
                );

                return $this->_redirect(
                    '*/*/index'
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        $this->registry->register('current_plan_data', $plan);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Cminds_SupplierSubscription::manage_subscriptions');
        $resultPage->getConfig()->getTitle()->prepend(__('Subscription Plans'));

        return $resultPage;
    }

    /**
     * Create virtual product related to plan.
     *
     * @param $plan
     * @return \Magento\Catalog\Model\Product
     */
    public function createVirtualProduct($plan)
    {
        $product = $this->productLoader->create();

        $product->setName(__('Subscription plan %1', $plan->getName())->__toString());
        $product->setTypeId(Type::TYPE_VIRTUAL);
        $product->setAttributeSetId($product->getDefaultAttributeSetId());
        $product->setSku(__('Subscription-plan-%1', $plan->getId())->__toString());
        $product->setWebsiteIds($this->getWebsiteIds());
        $product->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE);
        $product->setPrice($plan->getPrice());
        $product->setStatus(Status::STATUS_ENABLED);
        $product->setStockData(array(
            'use_config_manage_stock' => 0,
            'manage_stock' => 0,
            'min_sale_qty' => 1,
            'max_sale_qty' => 12,
            'is_in_stock' => 1,
            'qty' => 9999
        ));

        $product->save();

        return $product;
    }

    /**
     * Update name and price of virtual product related to plan.
     *
     * @param $plan
     * @return \Magento\Catalog\Model\Product
     */
    public function updateVirtualProduct($plan)
    {
        $product = $this->productLoader->create()->load($plan->getProductId());
        $product->setName($plan->getName());
        $product->setPrice($plan->getPrice());
        $product->save();

        return $product;
    }

    /**
     * Get webiste ids.
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        $websiteIds = [];

        foreach ($this->storeManager->getWebsites() as $website) {
            $websiteIds[] = $website->getId();
        }

        return $websiteIds;
    }
}
