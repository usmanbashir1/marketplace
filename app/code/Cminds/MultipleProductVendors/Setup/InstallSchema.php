<?php

namespace Cminds\MultipleProductVendors\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface // @codingStandardsIgnoreLine
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) // @codingStandardsIgnoreLine
    {
        $installer = $setup->startSetup();

        if (!$installer->tableExists('product_manufacturer_codes')) {
            $table = $setup->getConnection()
                ->newTable(
                    $setup->getTable('product_manufacturer_codes')
                )->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )->addColumn(
                    'manufacturer_code',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Manufacturer code'
                )->setComment(
                    'Manufacturer code'
                );

            $installer->getConnection()
                ->createTable($table);
        }

        $installer->endSetup();
    }
}
