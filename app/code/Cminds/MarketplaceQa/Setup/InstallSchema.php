<?php

namespace Cminds\MarketplaceQa\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @param SchemaSetupInterface   $setup   Schema setup object.
     * @param ModuleContextInterface $context Module context object.
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable('cminds_marketplace_qa');

        if ($installer->getConnection()->isTableExists($tableName) === false) {
            $table = $installer->getConnection()
                ->newTable(
                    $installer->getTable(
                        $tableName
                    )
                )
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'ID'
                )
                ->addColumn(
                    'supplier_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Supplier Id'
                )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Product Id'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Customer Id'
                )
                ->addColumn(
                    'customer_email',
                    Table::TYPE_TEXT,
                    30,
                    ['nullable' => true],
                    'Customer Email'
                )
                ->addColumn(
                    'customer_name',
                    Table::TYPE_TEXT,
                    64,
                    ['nullable' => true],
                    'Customer Name'
                )
                ->addColumn(
                    'question',
                    Table::TYPE_TEXT,
                    512,
                    ['nullable' => false],
                    'Question'
                )
                ->addColumn(
                    'answer',
                    Table::TYPE_TEXT,
                    512,
                    ['nullable' => true],
                    'Answer'
                )
                ->addColumn(
                    'visible_on_frontend',
                    Table::TYPE_SMALLINT,
                    1,
                    ['nullable' => false, 'default' => '0'],
                    'Is Visible on Frontend'
                )
                ->addColumn(
                    'approved',
                    Table::TYPE_SMALLINT,
                    1,
                    ['nullable' => false, 'default' => '0'],
                    'Is Approved'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false],
                    'Date Created'
                )
                ->setComment('Supplier Ordered Products');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
