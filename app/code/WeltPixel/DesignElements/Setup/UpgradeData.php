<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WeltPixel\DesignElements\Setup;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory, EavSetupFactory $eavSetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
    
        if (version_compare($context->getVersion(), '1.4.0') < 0) {
    
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'css_phone_small',
                [
                    'group' => 'Design',
                    'type' => 'text',
                    'label' => 'Small Phone CSS',
                    'input' => 'textarea',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'sort_order' => 0,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '',
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false
                ]
            );
    
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'css_phone',
                [
                    'group' => 'Design',
                    'type' => 'text',
                    'label' => 'Phone CSS',
                    'input' => 'textarea',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'sort_order' => 0,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '',
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false
                ]
            );
    
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'css_tablet_small',
                [
                    'group' => 'Design',
                    'type' => 'text',
                    'label' => 'Small Tablet CSS',
                    'input' => 'textarea',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'sort_order' => 1,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '',
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false
                ]
            );
    
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'css_tablet',
                [
                    'group' => 'Design',
                    'type' => 'text',
                    'label' => 'Tablet CSS',
                    'input' => 'textarea',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'sort_order' => 1,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '',
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false
                ]
            );
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'css_desktop',
                [
                    'group' => 'Design',
                    'type' => 'text',
                    'label' => 'Desktop CSS',
                    'input' => 'textarea',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'sort_order' => 2,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '',
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false
                ]
            );
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'css_desktop_large',
                [
                    'group' => 'Design',
                    'type' => 'text',
                    'label' => 'Large Desktop CSS',
                    'input' => 'textarea',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'sort_order' => 3,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '',
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false
                ]
            );
    
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'custom_js',
                [
                    'group' => 'Design',
                    'type' => 'text',
                    'label' => 'Custom Js',
                    'input' => 'textarea',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'sort_order' => 4,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '',
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false
                ]
            );
        }
    
        if (version_compare($context->getVersion(), '1.4.2') < 0) {
    
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'css_global',
                [
                    'group' => 'Design',
                    'type' => 'text',
                    'label' => 'Custom Global CSS',
                    'input' => 'textarea',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'sort_order' => 0,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '',
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false
                ]
            );
        }
        
        $setup->endSetup();
    }
}
