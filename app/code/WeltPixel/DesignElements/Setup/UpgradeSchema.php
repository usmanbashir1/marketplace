<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WeltPixel\DesignElements\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Cms module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.4.0') < 0) {
            
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'css_phone_small',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom CSS - Small Phone'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'css_phone',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom CSS - Phone'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'css_tablet_small',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom CSS - Small Tablet'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'css_tablet',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom CSS - Tablet'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'css_desktop',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom CSS - Desktop Medium'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'css_desktop_large',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom CSS - Desktop Large'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'custom_js',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom Js'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_block'),
                'css_phone_small',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom CSS - Small Phone'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_block'),
                'css_phone',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom CSS - Phone'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_block'),
                'css_tablet_small',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom CSS - Small Tablet'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_block'),
                'css_tablet',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom CSS - Tablet'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_block'),
                'css_desktop',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom CSS - Desktop Medium'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_block'),
                'css_desktop_large',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom CSS - Desktop Large'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_block'),
                'custom_js',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom Js'
                ]
            );
        }
    
        if (version_compare($context->getVersion(), '1.4.1') < 0) {
            
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'css_global',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom Global CSS'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_block'),
                'css_global',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Custom Global CSS'
                ]
            );
        }
    }
}
