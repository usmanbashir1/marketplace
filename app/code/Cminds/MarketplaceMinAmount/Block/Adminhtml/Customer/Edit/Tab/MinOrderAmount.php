<?php

declare(strict_types=1);

namespace Cminds\MarketplaceMinAmount\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Backend\Block\Widget\Form\Generic;

/**
 * MarketplaceMinAmount Admin Block
 *
 * @category Cminds
 * @package  MarketplaceMinAmount
 * @author   Cminds Core Team <info@cminds.com>
 */
class MinOrderAmount extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Cminds\MarketplaceMinAmount\Model\Config\Source\MinimumAmount
     */
    protected $minimumAmount;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerResource
     */
    protected $customerResource;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * MinOrderAmount constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param CustomerFactory $customerFactory
     * @param CustomerResource $customerResource
     * @param \Cminds\MarketplaceMinAmount\Model\Config\Source\MinimumAmount $minimumAmount
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        CustomerFactory $customerFactory,
        CustomerResource $customerResource,
        \Cminds\MarketplaceMinAmount\Model\Config\Source\MinimumAmount $minimumAmount,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->systemStore = $systemStore;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->minimumAmount = $minimumAmount;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Minimum Order Amount');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Minimum Order Amount');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }

        return true;
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * Admin Tab form
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function initForm()
    {
        if (!$this->canShowTab()) {
            return $this;
        }
        /**@var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('_minamount');
        $supplierId = $this->getCustomerId();
        $supplier = $this->customerFactory->create();
        $this->customerResource->load($supplier, $supplierId);

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Order Restrictions for Supplier')]);

        $fieldset->addField(
            'supplier_min_order_amount',
            'text',
            [
                'name' => 'supplier_min_order_amount',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('Minimum Order Amount'),
                'title' => __('Minimum Order Amount'),
                'note' => __('in %1 (base store currency). Empty or 0 means no restriction.', $this->getCurrentCurrencyCode())
            ]
        );

        $fieldset->addField(
            'supplier_min_order_qty',
            'text',
            [
                'name' => 'supplier_min_order_qty',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('Minimum Order Qty'),
                'title' => __('Minimum Order Qty'),
                'note' => __('Empty or 0 means no restriction.')
            ]
        );

        $fieldset->addField(
            'supplier_min_order_amount_per',
            'select',
            [
                'name' => 'supplier_min_order_amount_per',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('Restrictions Per'),
                'title' => __('Restrictions Per'),
                'values' => $this->minimumAmount->toOptionArray(),
            ]
        );

        $form->setValues([
            'supplier_min_order_amount' => $supplier->getSupplierMinOrderAmount(),
            'supplier_min_order_qty' => $supplier->getSupplierMinOrderQty(),
            'supplier_min_order_amount_per' => $supplier->getSupplierMinOrderAmountPer()
        ]);

        $this->setForm($form);

        return $this;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->canShowTab()) {
            $this->initForm();
            return parent::_toHtml();
        } else {
            return '';
        }
    }

    /**
     * Get current currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->priceCurrency->getCurrency()->getCurrencyCode();
    }

    /**
     * Get current currency symbol
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->priceCurrency->getCurrency()->getCurrencySymbol();
    }
}