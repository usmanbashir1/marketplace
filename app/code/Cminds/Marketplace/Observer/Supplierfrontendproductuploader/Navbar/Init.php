<?php

namespace Cminds\Marketplace\Observer\Supplierfrontendproductuploader\Navbar;

use Cminds\Marketplace\Helper\Data as CmindsHelper;
use Magento\Customer\Model\Customer;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

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
     * Init constructor.
     *
     * @param CmindsHelper      $cmindsHelper
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        CmindsHelper $cmindsHelper,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->cmindsHelper = $cmindsHelper;
        $this->dataObjectFactory = $dataObjectFactory;
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
     * Get configuration of supplier pages.
     *
     * @return bool
     */
    public function shippingMethodsEnabled()
    {
        return $this->cmindsHelper->shippingMethodsEnabled();
    }

    /**
     * Get supplier page url from helper.
     *
     * @param int $supplierId
     *
     * @return string
     */
    public function getSupplierRawPageUrl($supplierId)
    {
        return $this->cmindsHelper->getSupplierRawPageUrl($supplierId);
    }

    /**
     * Get logged customer from session.
     *
     * @return Customer
     */
    public function getLoggedSupplier()
    {
        return $this->cmindsHelper->getLoggedSupplier();
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
            $navItems = [
                'orders' => [
                    'label' => 'Orders',
                    'url' => 'marketplace/order',
                    'parent' => null,
                    'action_names' => [
                        'marketplace_order_index',
                        'marketplace_order_view',
                        'marketplace_shipment_create',
                        'marketplace_invoice_create',
                        'marketplace_shipment_view',
                    ],
                    'sort' => 2.5,
                ],
                'order_list' => [
                    'label' => 'Order List',
                    'url' => 'marketplace/order',
                    'parent' => 'orders',
                    'sort' => 0,
                ],
                'report_products' => [
                    'label' => 'Products',
                    'url' => null,
                    'parent' => 'reports',
                    'sort' => 1,
                    'fix_label' => true,
                ],
                'reports_bestsellers' => [
                    'label' => 'Bestsellers',
                    'url' => 'marketplace/reports/bestsellers',
                    'parent' => 'reports',
                    'sort' => 2,
                    'fix_label_children' => true,
                ],
                'reports_ordered_items' => [
                    'label' => 'Ordered Items',
                    'url' => 'supplier/product/ordered',
                    'parent' => 'reports',
                    'sort' => 3,
                    'fix_label_children' => true,
                ],
                'reports_most_viewed' => [
                    'label' => 'Most Viewed',
                    'url' => 'marketplace/reports/mostViewed',
                    'parent' => 'reports',
                    'sort' => 4,
                    'fix_label_children' => true,
                ],
                'reports_low_stack' => [
                    'label' => 'Low stock',
                    'url' => 'marketplace/reports/lowStock',
                    'parent' => 'reports',
                    'sort' => 5,
                    'fix_label_children' => true,
                ],
            ];

            if ($this->supplierPagesEnabled()) {
                $navItems['supplier_page'] = [
                    'label' => 'My Profile Page',
                    'url' => 'marketplace/settings/profile',
                    'parent' => 'settings',
                    'sort' => -1,
                ];

                $navItems['settings']['action_names'] = array_merge(
                    $items['settings']['action_names'],
                    ['marketplace_settings_profile']
                );

                $navItems['my_supplier_page'] = [
                    'label' => 'My Supplier Page',
                    'url' => $this->getSupplierRawPageUrl($this->getLoggedSupplier()->getId()),
                    'parent' => null,
                    'sort' => 4.5,
                ];
            }

            if ($this->shippingMethodsEnabled()) {
                $navItems['shipping_methods'] = [
                    'label' => 'Shipping Methods',
                    'url' => 'marketplace/settings/methods',
                    'parent' => 'settings',
                    'sort' => 1,
                ];
                $navItems['settings']['action_names'] = array_merge(
                    $items['settings']['action_names'],
                    ['marketplace_settings_shipping']
                );
            }

            $navItems['reports']['action_names'] = array_merge(
                $items['settings']['action_names'],
                [
                    'marketplace_reports_orders',
                    'supplier_product_ordered',
                    'marketplace_reports_bestsellers',
                    'marketplace_reports_mostViewed',
                    'marketplace_reports_lowStock',
                ]
            );

            $this->navItems = $this->dataObjectFactory->create()->setData($navItems);
        }

        return $this->navItems;
    }
}
