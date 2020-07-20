<?php

namespace Cminds\SupplierInventoryUpdate\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable('cminds_inventoryUpdate');

        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    'Entity ID'
                )
                ->addColumn(
                    'supplier_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                    ],
                    'Supplier Id'
                )
                ->addColumn(
                    'updater_csv_link',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                        'default' => '',
                    ],
                    'Updater CSV Link'
                )
                ->addColumn(
                    'updater_csv_column',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                        'default' => '',
                    ],
                    'Updater CSV column'
                )
                ->addColumn(
                    'updater_qty_column',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                        'default' => '',
                    ],
                    'Updater QTY column'
                )
                ->addColumn(
                    'updater_csv_action',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                        'default' => '',
                    ],
                    'Updater CSV Action'
                )
                ->addColumn(
                    'updater_csv_attribute',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                        'default' => '',
                    ],
                    'Updater CSV Attribute'
                )
                ->addColumn(
                    'updater_csv_delimiter',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                        'default' => '',
                    ],
                    'Updater CSV delimiter'
                )
                ->addColumn(
                    'updater_cost_column',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                        'default' => '',
                    ],
                    'Updater Cost Column'
                )
                ->setComment('Cminds Supplier Inventory Update');
            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}
