<?php

namespace Cminds\MarketplacePaypal\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Cminds MarketplacePaypal install data.
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class InstallData implements InstallDataInterface
{
    private $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * Object constructor.
     *
     * @param CustomerSetupFactory $customerSetupFactory Customer setup factory object.
     * @param AttributeSetFactory  $attributeSetFactory Attribute set factory object.
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     * @throws \Exception
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'supplier_paypal_email',
            [
                'type' => 'text',
                'label' => __('Paypal Email'),
                'input' => 'text',
                'required' => false,
                'default' => '',
                'visible' => true,
                'admin_only' => false,
                'system' => false,
            ]
        );

        $customerSetup
            ->getEavConfig()
            ->getAttribute(Customer::ENTITY, 'supplier_paypal_email')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer'],
            ])
            ->save();

        $setup->endSetup();
    }
}
