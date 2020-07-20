<?php

namespace Cminds\SupplierSubscription\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Cminds SupplierSubscription install data.
 *
 * @category Cminds
 * @package  Cminds_SupplierSubscription
 * @author   Waldemar Karpiel <karpiel.waldemar@gmail.com>
 */
class InstallData implements InstallDataInterface
{
    /**
     * Customer setup factory.
     *
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * Object constructor.
     *
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'current_plan_id',
            [
                'type' => 'int',
                'backend' => '',
                'label' => 'Subscription Plan',
                'input' => 'select',
                'source' => 'Cminds\SupplierSubscription\Model\Config\Source\General\DefaultPlan',
                'required' => false,
                'visible' => true,
                'default' => '',
                'system' => 0,
                'unique' => false,
                'note' => '',
            ]
        );

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'plan_from_date',
            [
                'type' => 'datetime',
                'backend' => '',
                'label' => 'Subscription plan start date',
                'input' => 'date',
                'source' => '',
                'required' => false,
                'visible' => false,
                'default' => '',
                'system' => 0,
                'unique' => false,
                'note' => '',
            ]
        );

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'plan_to_date',
            [
                'type' => 'datetime',
                'backend' => '',
                'label' => 'Subscription plan end date',
                'input' => 'date',
                'source' => '',
                'required' => false,
                'visible' => false,
                'default' => '',
                'system' => 0,
                'unique' => false,
                'note' => '',
            ]
        );

        $setup->endSetup();
    }
}
