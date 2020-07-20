<?php

namespace WeltPixel\ProductLabels\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;


/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            /**
             * Create table 'weltpixel_productlabels_rule_idx'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('weltpixel_productlabels_rule_idx'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )->addColumn(
                    'rule_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Rule Id'
                )->addColumn(
                    'product_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Product Id'
                )->addIndex(
                    $installer->getIdxName(
                        $installer->getTable('weltpixel_productlabels_rule_idx'),
                        ['rule_id']
                    ),
                    ['rule_id']
                )->addIndex(
                    $installer->getIdxName(
                        $installer->getTable('weltpixel_productlabels_rule_idx'),
                        ['product_id']
                    ),
                    ['product_id']
                )->setComment(
                    'WeltPixel Product Labels Rules Index'
                );

            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $tableName = $installer->getTable('weltpixel_productlabels_rule_idx');
            if ($installer->getConnection()->isTableExists($tableName)) {
                $installer->getConnection()
                    ->addColumn(
                        $tableName,
                        'store_id',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            'unsigned' => true,
                            'nullable' => false,
                            'comment' => 'Store Id',
                        ]
                    );
                $installer->getConnection()
                    ->addIndex(
                        $tableName,
                        $installer->getIdxName(
                            $tableName,
                            ['store_id']
                        ),
                        ['store_id']
                    )
                ;
            }
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $tableName = $installer->getTable('weltpixel_productlabels');
            if ($installer->getConnection()->isTableExists($tableName)) {
                $installer->getConnection()
                    ->addColumn(
                        $tableName,
                        'valid_from',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                            'unsigned' => true,
                            'nullable' => true,
                            'comment'  => 'Valid From',
                            'default'  => NULL,
                            'after'    => 'status'
                        ]
                    );

                $installer->getConnection()
                    ->addColumn(
                        $tableName,
                        'valid_to',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                            'unsigned' => true,
                            'nullable' => true,
                            'comment'  => 'Valid To',
                            'default'  => NULL,
                            'after'    => 'valid_from'
                        ]
                    );
            }
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $tableName = $installer->getTable('weltpixel_productlabels');
            if ($installer->getConnection()->isTableExists($tableName)) {
                $installer->getConnection()
                    ->addColumn(
                        $tableName,
                        'product_page_position',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                            'unsigned' => true,
                            'nullable' => false,
                            'default' => '1',
                            'after' => 'conditions_serialized',
                            'comment' => 'Position in Product Page'
                        ]
                    );
            }
        }

        $installer->endSetup();
    }
}