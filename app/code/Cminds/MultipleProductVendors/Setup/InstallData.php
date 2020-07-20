<?php

namespace Cminds\MultipleProductVendors\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\AttributeManagement;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetup;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        AttributeManagement $attributeManagement,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->eavSetup = $eavSetupFactory;
        $this->scopeConfig = $scopeConfig;
    }

    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $attributeSetId = (int)$this->scopeConfig->getValue(
            'products_settings/adding_products/attributes_set'
        );

        $eavSetup = $this->eavSetup->create(['setup' => $setup]);

        /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\Collection $defaultAttributeGroups */
        $defaultAttributeGroups = $eavSetup->getAttributeGroupCollectionFactory()
            ->addFieldToFilter('default_id', 1)
            ->addFieldToFilter('attribute_set_id', $attributeSetId)
            ->load();

        foreach ($defaultAttributeGroups as $group) {
            $group = $group->getAttributeGroupName();

            $eavSetup->addAttribute(
                Product::ENTITY,
                'manufacturer_code',
                [
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'input' => 'text',
                    'label' => 'Manufacturer Code',
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => 0,
                    'group' => $group,
                    'available_for_supplier' => 1,
                ]
            );

            $eavSetup->addAttribute(
                Product::ENTITY,
                'vendor_code',
                [
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'input' => 'text',
                    'label' => 'Vendor Code',
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => 0,
                    'group' => $group,
                    'available_for_supplier' => 1,
                ]
            );

            $eavSetup->addAttribute(
                Product::ENTITY,
                'main_product',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'input' => 'boolean',
                    'label' => 'Main Product',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => 0,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => 0,
                    'group' => $group,
                ]
            );
        }

        $eavSetup
            ->updateAttribute(
                Product::ENTITY,
                'manufacturer_code',
                'available_for_supplier',
                1
            )
            ->updateAttribute(
                Product::ENTITY,
                'vendor_code',
                'available_for_supplier',
                1
            );

        $setup->endSetup();
    }
}
