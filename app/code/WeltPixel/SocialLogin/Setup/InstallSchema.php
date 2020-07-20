<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package WeltPixel\SocialLogin\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('weltpixel_sociallogin_customer')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('weltpixel_sociallogin_customer'))
                ->addColumn('id', Table::TYPE_INTEGER, 11, [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true,
                ], 'SocialLogin User Id')
                ->addColumn('sociallogin_id', Table::TYPE_TEXT, 255, ['unsigned' => true, 'nullable => false'], 'SocialLogin Id')
                ->addColumn('customer_id', Table::TYPE_INTEGER, 10, ['unsigned' => true, 'nullable => false'], 'Magento Customer Id')
                ->addColumn('type', Table::TYPE_TEXT, 255, ['default' => ''], 'Type')
                ->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Created At')
                ->addForeignKey(
                    $installer->getFkName('weltpixel_sociallogin_customer', 'customer_id', 'customer_entity', 'entity_id'),
                    'customer_id',
                    $installer->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE)
                ->setComment('SocialLogin Customer Table');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
