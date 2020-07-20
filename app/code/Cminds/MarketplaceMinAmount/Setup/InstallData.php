<?php

namespace Cminds\MarketplaceMinAmount\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $customerSetupFactory;

    /**
     * Object constructor.
     *
     * @param CustomerSetupFactory $customerSetupFactory Customer setup factory object.
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @param ModuleDataSetupInterface $setup Module data setup object.
     * @param ModuleContextInterface   $context Module context object.
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'supplier_min_order_amount',
            [
                'type' => 'decimal',
                'label' => __('Supplier Min Order Amount'),
                'input' => 'text',
                'required' => false,
                'visible' => false,
                'system' => 0,
            ]
        );
        $customerSetup
            ->getEavConfig()
            ->getAttribute(
                'customer',
                'supplier_min_order_amount'
            )
            ->setData('used_in_forms', ['adminhtml_customer'])
            ->save();

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'supplier_min_order_qty',
            [
                'type' => 'decimal',
                'label' => 'Supplier Min Order Qty',
                'input' => 'text',
                'required' => false,
                'visible' => false,
                'system' => 0,
            ]
        );
        $customerSetup
            ->getEavConfig()
            ->getAttribute(
                'customer',
                'supplier_min_order_qty'
            )
            ->setData('used_in_forms', ['adminhtml_customer'])
            ->save();

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'supplier_min_order_amount_per',
            [
                'type' => 'int',
                'label' => 'Supplier Minimum Order Amount Per',
                'source' => 'Cminds\MarketplaceMinAmount\Model\Config\Source\MinimumAmount',
                'input' => 'select',
                'visible' => false,
                'required' => false,
                'default' => 0,
                'system' => 0,
            ]
        );
        $customerSetup
            ->getEavConfig()
            ->getAttribute(
                'customer',
                'supplier_min_order_amount_per'
            )
            ->setData('used_in_forms', ['adminhtml_customer'])
            ->save();

        $setup->endSetup();
    }
}
