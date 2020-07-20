<?php

namespace WeltPixel\Sitemap\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;


/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $setup->getConnection()->addColumn(
            $setup->getTable('cms_page'),
            'exclude_from_sitemap',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Exclude from sitemap',
                'default' => 0
            ]
        );

        /**
         * Create table 'weltpixel_sitemap'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('weltpixel_sitemap')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Sitemap Url'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Updated At'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Store id'
        )->addColumn(
            'changefreq',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => 'daily'],
            'Changefrequency'
        )->addColumn(
            'priority',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '2,1',
            ['nullable' => false, 'default' => 0.5],
            'Priority'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('weltpixel_sitemap'),
                ['url'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['url'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'WeltPixel Sitemap'
        );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}