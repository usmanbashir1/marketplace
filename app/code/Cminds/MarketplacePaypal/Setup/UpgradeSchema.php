<?php

namespace Cminds\MarketplacePaypal\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Upgrade Schema
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrade entry point
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->createPayoutPaymentTable($setup);
        }
    }

    /**
     * Create payout payment table
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    protected function createPayoutPaymentTable(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        $table = $setup->getConnection()
            ->newTable(
                $setup->getTable(
                    'cminds_marketplace_payout_status'
                )
            )
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Payout payment id'
            )
            ->addColumn(
                'supplier_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Supplier id'
            )
            ->addColumn(
                'payout_batch_id',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Paypal payout batch id'
            )
            ->addColumn(
                'status',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Current payment status'
            )
            ->addColumn(
                'recipient_email',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Payment recipient'
            )
            ->addColumn(
                'amount',
                Table::TYPE_DECIMAL,
                [5, 2],
                ['nullable' => false]
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Order Id'
            )
            ->addColumn(
                'payment_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Supplier payment Id'
            )
            ->addColumn(
                'payment_date',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'Payment Date'
            )
            ->addIndex($setup->getIdxName(
                'cminds_marketplace_payout_status',
                'payout_batch_id',
                AdapterInterface::INDEX_TYPE_UNIQUE
            ), 'payout_batch_id', AdapterInterface::INDEX_TYPE_UNIQUE)
            ->addForeignKey(
                $setup->getFkName('cminds_marketplace_payout_status', 'order_id', 'sales_order', 'entity_id'),
                'order_id', 'sales_order', 'entity_id'
            )
            ->addForeignKey(
                $setup->getFkName(
                    'cminds_marketplace_payout_status', 'supplier_id', 'customer_entity', 'entity_id'
                ),
                'supplier_id', 'customer_entity', 'entity_id'
            )
            ->setComment('Cminds Marketplace Payout payment status');
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }
}
