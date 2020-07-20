<?php

namespace Cminds\MultipleProductVendors\Setup;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory      $eavSetupFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->scopeConfig = $scopeConfig;
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
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.3', '<')) {

            $attributeSetId = (int)$this->scopeConfig->getValue(
                'products_settings/adding_products/attributes_set'
            );

            /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\Collection $defaultAttributeGroups */
            $defaultAttributeGroups = $eavSetup->getAttributeGroupCollectionFactory()
                ->addFieldToFilter('default_id', 1)
                ->addFieldToFilter('attribute_set_id', $attributeSetId)
                ->load();

            foreach ($defaultAttributeGroups as $group) {
                $group = $group->getAttributeGroupName();

                $eavSetup->addAttribute(
                    Product::ENTITY,
                    'vendor_description',
                    [
                        'type' => 'text',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Vendor description',
                        'input' => 'textarea',
                        'class' => '',
                        'source' => '',
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'default' => '',
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => true,
                        'used_in_product_listing' => true,
                        'is_wysiwyg_enabled'      => true,
                        'is_html_allowed_on_front' => false,
                        'unique' => false,
                        'apply_to' => ''
                    ]
                );
            }

            /* hide them from prdocut edit page */
            $eavSetup
                ->updateAttribute(
                    Product::ENTITY,
                    'manufacturer_code',
                    'available_for_supplier',
                    0
                )
                ->updateAttribute(
                    Product::ENTITY,
                    'vendor_code',
                    'available_for_supplier',
                    0
                );

        }

        $setup->endSetup();
    }
}
