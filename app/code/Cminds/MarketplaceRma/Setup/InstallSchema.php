<?php

namespace Cminds\MarketplaceRma\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 * @package Cminds\MarketplaceRma\Setup
 */
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

        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable(
                    'cminds_marketplace_rma'
                )
            )
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_TEXT,
                11,
                ['nullable' => false],
                'Order Id'
            )
            ->addColumn(
                'package_opened',
                Table::TYPE_SMALLINT,
                1,
                ['nullable' => false, 'default' => '0'],
                'Package Opened'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_TEXT,
                11,
                ['nullable' => true],
                'Customer Id'
            )
            ->addColumn(
                'request_type',
                Table::TYPE_TEXT,
                30,
                ['nullable' => true],
                'Request Type'
            )
            ->addColumn(
                'additional_info',
                Table::TYPE_TEXT,
                512,
                ['nullable' => false],
                'Additional Info'
            )
            ->addColumn(
                'reason',
                Table::TYPE_TEXT,
                512,
                ['nullable' => true],
                'Reason'
            )
            ->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                3,
                ['nullable' => false],
                'Status'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Date Created'
            )
            ->addIndex(
                $installer->getIdxName(
                    'cminds_marketplace_rma',
                    ['order_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['order_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->setComment('Marketplace Returns');
            $installer->getConnection()->createTable($table);
            
        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable(
                    'cminds_marketplace_rma_status'
                )
            )
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                30,
                ['nullable' => true],
                'Name'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Date Created'
            )
            ->setComment('Marketplace Returns Status');
            $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable(
                    'cminds_marketplace_rma_reason'
                )
            )
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Name'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Date Created'
            )
            ->setComment('Marketplace Returns Reason');
            $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable(
                    'cminds_marketplace_rma_type'
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
                'name',
                Table::TYPE_TEXT,
                30,
                ['nullable' => true],
                'Name'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Date Created'
            )
            ->setComment('Marketplace Returns Reason');
            $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable(
                    'cminds_marketplace_rma_return_product'
                )
            )
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'ID'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Order Id'
            )
            ->addColumn(
                'invoice_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Invoice Id'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Product Id'
            )
            ->addColumn(
                'return_qty',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Return Qty'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Date Created'
            )
            ->setComment('Marketplace Returns return products');
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable(
                    'cminds_marketplace_rma_note'
                )
            )
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'ID'
            )
            ->addColumn(
                'rma_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Rma Id'
            )
            ->addColumn(
                'notify_customer',
                Table::TYPE_SMALLINT,
                1,
                ['nullable' => false, 'default' => '0'],
                'Notify Customer'
            )
            ->addColumn(
                'note',
                Table::TYPE_TEXT,
                512,
                ['nullable' => false],
                'Note'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Date Created'
            )
            ->setComment('Marketplace Returns notes');
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable(
                    'cminds_marketplace_rma_customer_address'
                )
            )
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'ID'
            )
            ->addColumn(
                'rma_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Rma Id'
            )
            ->addColumn(
                'first_name',
                Table::TYPE_TEXT,
                512,
                ['nullable' => true],
                'First Name'
            )
            ->addColumn(
                'last_name',
                Table::TYPE_TEXT,
                512,
                ['nullable' => true],
                'Last Name'
            )
            ->addColumn(
                'company',
                Table::TYPE_TEXT,
                512,
                ['nullable' => true],
                'Company'
            )
            ->addColumn(
                'telephone',
                Table::TYPE_TEXT,
                512,
                ['nullable' => true],
                'Telephone'
            )
            ->addColumn(
                'fax',
                Table::TYPE_TEXT,
                512,
                ['nullable' => true],
                'Fax'
            )
            ->addColumn(
                'street',
                Table::TYPE_TEXT,
                512,
                ['nullable' => true],
                'Street'
            )
            ->addColumn(
                'city',
                Table::TYPE_TEXT,
                512,
                ['nullable' => true],
                'City'
            )
            ->addColumn(
                'country',
                Table::TYPE_TEXT,
                512,
                ['nullable' => true],
                'City'
            )
            ->addColumn(
                'zipcode',
                Table::TYPE_TEXT,
                512,
                ['nullable' => true],
                'Zip Code'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Date Created'
            )
            ->addIndex(
                $installer->getIdxName(
                    'cminds_marketplace_rma_customer_address',
                    ['rma_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['rma_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->setComment('Marketplace Returns related customer address');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
