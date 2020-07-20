<?php

namespace Cminds\DropshipNotification\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Cminds DropshipNotification install schema.
 *
 * @category Cminds
 * @package  Cminds_DropshipNotification
 * @author   Piotr Pierzak <piotr@cminds.com>
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

        /**
         * Add dropship_notification_flag column to sales_order_item table.
         */
        $table = $setup->getTable('sales_order_item');
        $setup->getConnection()->addColumn(
            $table,
            'dropship_notification_flag',
            [
                'type' => Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Dropship Notification',
            ]
        );

        /**
         * Add dropship_notification_date column to sales_order_item table.
         */
        $setup->getConnection()->addColumn(
            $table,
            'dropship_notification_date',
            [
                'type' => Table::TYPE_TIMESTAMP,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Dropship Notification Date',
            ]
        );

        $setup->endSetup();
    }
}
