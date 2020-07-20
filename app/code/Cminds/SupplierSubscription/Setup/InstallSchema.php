<?php

namespace Cminds\SupplierSubscription\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Cminds SupplierSubscription install schema.
 *
 * @category Cminds
 * @package  Cminds_SupplierSubscription
 * @author   Waldemar Karpiel <karpiel.waldemar@gmail.com>
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $table = $setup
            ->getConnection()
            ->newTable($setup->getTable('cminds_suppliersubscription_plan'))
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
                'name',
                Table::TYPE_TEXT,
                255,
                [],
                'Plan Name'
            )
            ->addColumn(
                'price',
                Table::TYPE_DECIMAL,
                [5, 2],
                [
                    'nullable' => false,
                ],
                'Plan Price'
            )
            ->addColumn(
                'products_number',
                Table::TYPE_INTEGER,
                11,
                [
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'Allowed Number of Products'
            )
            ->addColumn(
                'images_number',
                Table::TYPE_INTEGER,
                11,
                [
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'Allowed Number of Images Per Product'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT_UPDATE,
                ],
                'Updated At'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT,
                ],
                'Created At'
            )
            ->setComment('Cminds Supplier Subscription Plan Entity');

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
