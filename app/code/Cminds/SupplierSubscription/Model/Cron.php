<?php

namespace Cminds\SupplierSubscription\Model;

use Cminds\Supplierfrontendproductuploader\Helper\Data as SupplierHelper;
use Cminds\SupplierSubscription\Helper\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\MailException;
use \Psr\Log\LoggerInterface as Logger;

class Cron
{
    /**
     * @var \Cminds\SupplierSubscription\Helper\Data
     */
    private $_helper;

    /**
     * @var Product
     */
    private $_productHelper;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $_customerFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $_scopeConfig;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $_transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManagerInterface;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $_layout;

    /**
     * @var \Magento\Framework\App\State
     */
    private $_state;

    /**
     * @var Logger
     */
    private $_logger;

    /**
     * @var SupplierHelper
     */
    private $_supplierHelper;

    /**
     * Cron constructor.
     * @param \Cminds\SupplierSubscription\Helper\Data $supplierSubscriptionHelper
     * @param Product $supplierSubscriptionProductHelper
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\App\State $state
     * @param SupplierHelper $supplierHelper
     * @param Logger $logger
     */
    public function __construct(
        \Cminds\SupplierSubscription\Helper\Data $supplierSubscriptionHelper,
        \Cminds\SupplierSubscription\Helper\Product $supplierSubscriptionProductHelper,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\State $state,
        \Cminds\Supplierfrontendproductuploader\Helper\Data $supplierHelper,
        Logger $logger
    )
    {
        $this->_helper                      = $supplierSubscriptionHelper;
        $this->_productHelper               = $supplierSubscriptionProductHelper;
        $this->_customerFactory             = $customerFactory;
        $this->_scopeConfig                 = $scopeConfig;
        $this->_transportBuilder            = $transportBuilder;
        $this->_storeManagerInterface       = $storeManagerInterface;
        $this->_layout                      = $layout;
        $this->_state                       = $state;
        $this->_logger                      = $logger;
        $this->_supplierHelper              = $supplierHelper;
    }

    /**
     * @return $this
     * @throws MailException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function execute()
    {
        if ($this->_supplierHelper->isEnabled() === false) {
            return $this;
        }

        $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

        $this->disableProductsFromExpiredSuppliers();

        if (!$this->_helper->isNotificationEnabled()) {
            return $this;
        }

        $attributesToSelect = [
            'id',
            'is_active',
            'customer_is_active',
            'firstname',
            'email',
            'current_plan_id',
            'plan_from_date',
            'plan_to_date'
        ];

        $days           = '+' . $this->_helper->getNotificationDaysToSendEmail()+1 . ' days';
        $dateToFilter   = date('Y-m-d', strtotime($days));

        $customersWithPlan = $this->_customerFactory->create()
            ->addAttributeToSelect($attributesToSelect)
            ->addAttributeToFilter('customer_is_active', ['eq' => '1'])
            ->addAttributeToFilter('current_plan_id', ['notnull' => true])
            ->addAttributeToFilter('plan_to_date', ['date' => true, 'to' => $dateToFilter])
            ->load();

        if (!$customersWithPlan->getSize()) {
            $this->_logger->info('[CMinds_SupplierSubscription] no vendors with a plan about to expire were found');
            return $this;
        }

        $template = $this->_helper->getNotificationEmailTemplate();

        foreach ($customersWithPlan as $customer) {
            $this->_sendEmail($template, $customer);
            $this->_sendEmail($template, $customer, true);
        }

        return $this;
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function disableProductsFromExpiredSuppliers()
    {
        $attributesToSelect = [
            'id',
            'is_active',
            'creator_id',
            'customer_is_active',
            'firstname',
            'email',
            'current_plan_id',
            'plan_from_date',
            'plan_to_date'
        ];

        $customers = $this->_customerFactory->create()
            ->addAttributeToSelect($attributesToSelect)
            ->addAttributeToFilter('customer_is_active', ['eq' => '1'])
            ->addAttributeToFilter('current_plan_id', ['notnull' => true])
            ->addAttributeToFilter('plan_to_date', ['date' => true, 'from' => date('Y-m-d')])
            ->load();

        foreach ($customers as $customer) {
            $this->_productHelper->disableProductsFromExpiredVendor($customer);
        }
    }

    /**
     * @param $template
     * @param $customer
     * @param bool $adminVersion
     * @throws MailException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _sendEmail($template, $customer, $adminVersion = false)
    {
        $sender = [
            'name'  => $this->_helper->getStoreGeneralName(),
            'email' => $this->_helper->getStoreGeneralEmail()
        ];

        $sendTo = [
            "email" => $adminVersion ? $sender['email'] : $customer->getData('email'),
            "name"  => $adminVersion ? $sender['name'] : $customer->getData('firstname'),
        ];

        $transport = $this->_transportBuilder
            ->setTemplateIdentifier($template)
            ->setTemplateOptions([
                    'area'  => Area::AREA_FRONTEND,
                    'store' => $this->_storeManagerInterface->getStore()->getId(),
            ])
            ->setTemplateVars([
                'customer'      => $customer,
                'admin_version' => $adminVersion,
                'expiry_date'   => date("d-m-Y", strtotime($customer->getData('plan_to_date')))
            ])
            ->setFrom($sender)
            ->addTo($sendTo['email'], $sendTo['name'])
            ->getTransport();

        try {
            $transport->sendMessage();
        } catch (MailException $e) {
            $this->_logger->info($e->getMessage());
        }

    }


}
