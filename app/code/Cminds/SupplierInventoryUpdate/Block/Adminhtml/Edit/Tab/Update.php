<?php

namespace Cminds\SupplierInventoryUpdate\Block\Adminhtml\Edit\Tab;

use Cminds\Marketplace\Helper\Data as MarketplaceHelper;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Registry;

class Update extends Template implements TabInterface
{
    /**
     * Data helper object.
     *
     * @var MarketplaceHelper
     */
    private $marketplaceHelper;

    /**
     * Core registry.
     *
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param Context           $context
     * @param Registry          $registry
     * @param MarketplaceHelper $marketplaceHelper
     * @param array             $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        MarketplaceHelper $marketplaceHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );

        $this->marketplaceHelper = $marketplaceHelper;
        $this->coreRegistry = $registry;
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('supplier/form.phtml');
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
        return __('Inventory Update');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Inventory Update');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        $id = $this->getRequest()->getParam('id');
        if ($this->getCustomerId() && $this->marketplaceHelper->isSupplier($id)) {
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
        return $this->getUrl(
            'supplier_inventory/*/updateform',
            ['_current' => true]
        );
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return true;
    }
}
