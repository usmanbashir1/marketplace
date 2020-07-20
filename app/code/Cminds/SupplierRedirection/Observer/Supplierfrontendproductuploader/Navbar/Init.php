<?php
/**
 * Cminds SupplierRedirection
 *
 * @category Cminds
 * @package  Cminds_SupplierRedirection
 */
namespace Cminds\SupplierRedirection\Observer\Supplierfrontendproductuploader\Navbar;

use Cminds\Marketplace\Helper\Data as CmindsHelper;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Customer;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Framework\UrlInterface;

/**
 * Cminds SupplierRedirection Add the menu item for supplier redirection.
 *
 * @category Cminds
 * @package  Cminds_SupplierRedirection
 */
class Init implements ObserverInterface
{
    /**
     * Navigation items.
     *
     * @var DataObject
     */
    private $navItems;

    /**
     * Marketplace helper object.
     *
     * @var CmindsHelper
     */
    private $cmindsHelper;

    /**
     * DataObject factory.
     *
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * Customer Model.
     *
     * @var CustomerModel
     */
    private $customer;

    /**
     *  StoreManager
     *
     *  @var StoreManager
     */
    private $storeManager;

    /**
     * Init constructor.
     *
     * @param CmindsHelper      $cmindsHelper
     * @param DataObjectFactory $dataObjectFactory
     * @param Customer $customer
     * @param StoreManager $storeManager
     */
    public function __construct(
        CmindsHelper $cmindsHelper,
        DataObjectFactory $dataObjectFactory,
        Customer $customer,
        StoreManager $storeManager
    ) {
        $this->cmindsHelper = $cmindsHelper;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->customer = $customer;
        $this->storeManager = $storeManager;
    }

    /**
     * Set navigation items used in Cminds\Supplierfrontendproductuploader\Block\Navbar.
     *
     * @param Observer $observer
     *
     * @return Init
     */
    public function execute(Observer $observer)
    {
        $navItems = $observer->getNavigationItems();
        $navItemsData = $navItems->getData();
        $items = array_merge(
            $this->getNavigationItems($navItemsData)->getData(),
            $navItemsData
        );
        $navItems->setData($items);

        return $this;
    }

    /**
     * Get configuration of supplier pages.
     *
     * @return bool
     */
    public function supplierPagesEnabled()
    {
        return $this->cmindsHelper->supplierPagesEnabled();
    }

    /**
     * Get vendor panel navigation items from marketplace module.
     *
     * @param array $items
     *
     * @return DataObject
     */
    private function getNavigationItems($items)
    {
        if ($this->navItems === null) {
            if ($this->supplierPagesEnabled()) {
                $navItems['supplier_domain'] = [
                    'label' => 'Domain Settings',
                    'url' => 'supplierredirection/settings/domain',
                    'parent' => 'settings',
                    'sort' => -1,
                ];

                $navItems['settings']['action_names'] = array_merge(
                    $items['settings']['action_names'],
                    ['supplierredirection_settings_domain']
                );

                $customer = $this->cmindsHelper->getLoggedSupplier();
                $supplierCustomUrl = $customer->getDomainUrl();
                if (isset($supplierCustomUrl) and $supplierCustomUrl != '') {
                    $storeUrl = $this->storeManager->getStore()
                        ->getBaseUrl(UrlInterface::URL_TYPE_WEB);
                    $navItems['my_supplier_page'] = null;
                    $navItems['my_supplier_page'] = [
                        'label' => 'My Supplier Page',
                        'url' => $storeUrl.$supplierCustomUrl,
                        'parent' => null,
                        'sort' => 4.5,
                    ];
                }
            }
            $this->navItems = $this->dataObjectFactory->create()->setData($navItems);
        }

        return $this->navItems;
    }
}