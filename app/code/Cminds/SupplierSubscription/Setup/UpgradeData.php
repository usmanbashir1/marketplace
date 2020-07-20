<?php

namespace Cminds\SupplierSubscription\Setup;

use Magento\Catalog\Model\Product;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var Config
     */
    private $resourceConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory      $eavSetupFactory
     * @param CustomerSetupFactory $customerSetupFactory
     * @param Config               $resourceConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param IndexerRegistry      $indexerRegistry
     * @param EavConfig            $eavConfig
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory,
        Config $resourceConfig,
        ScopeConfigInterface $scopeConfig,
        IndexerRegistry $indexerRegistry,
        EavConfig $eavConfig
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->resourceConfig = $resourceConfig;
        $this->scopeConfig = $scopeConfig;
        $this->eavConfig = $eavConfig;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     * @throws \Exception
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $customerSetup
                ->getEavConfig()
                ->getAttribute(
                    'customer',
                    'current_plan_id'
                )
                ->setData(
                    'used_in_forms',
                    ['adminhtml_customer']
                )
                ->save();

        }

        if (version_compare($context->getVersion(), '1.0.8', '<')) {

            $eavSetup->addAttribute(
                Product::ENTITY,
                'disabled_supplier_subscription_expired',
                [
                    'type' => 'int',
                    'label' => 'flag for when Supplier Subscription is expired',
                    'input' => 'select',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'visible' => false,
                    'required' => true,
                    'user_defined' => false,
                    'default' => 0,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'visible_in_advanced_search' => false,
                    'used_in_product_listing' => 1,
                ]
            );
        }

        $indexer = $this->indexerRegistry->get(Customer::CUSTOMER_GRID_INDEXER_ID);
        $indexer->reindexAll();

        $this->eavConfig->clear();

        $setup->endSetup();
    }
}
